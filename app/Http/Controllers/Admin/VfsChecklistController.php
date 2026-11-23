<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\VfsChecklist;
use App\Models\VfsChecklistTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VfsChecklistController extends Controller
{
    protected const DEFAULT_CHECKLIST = [
        'VFS Appointment',
        'Visa Application',
        'Photo 35X45',
        'Passport',
        'Academic Certificates (Education Board and ministry attestation)',
        'Academic Transcripts (Education Board and ministry attestation)',
        'English Proficiency (If any)',
        'CV',
        'Motivation Letter',
        'Final Offer Letter and college others documents',
        'Accommodation',
        'Birth Certificate (Notarize and attested)',
        'Insurance',
        'Flight Booking',
        'Student Bank ATM Card',
        'Sponsor NID (Translation and notarize)',
        'Applicant NID (Translation and notarize)',
        'Sponsor Income Source (Trade License or Job Certificate) (Translation and notarize)',
        'TIN certificate',
        'TAX certificate 2 years',
        'Bank Statement',
        'Sponsor Bank ATM Card',
        'Applicants ATM Card',
        'Bank Account Cheque Book copy',
        'Deposit Slip (If possible)',
        'Financial Declaration Affidavit',
    ];

    public function __construct()
    {
        $this->middleware('can:*application|*consultant');
    }

    public function index(Request $request)
    {
        $query = Application::with(['student', 'university', 'vfsChecklist'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_id', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $applications = $query->paginate(15);
        $totalApplications = Application::count();
        $vfsScheduled = Application::whereNotNull('vfs_appointment_date')->count();

        return view('admin.vfs-checklist.index', compact('applications', 'totalApplications', 'vfsScheduled'));
    }

    public function overview(Request $request)
    {
        // Get the latest application ID for each student that has a VFS appointment date
        $latestVfsSubquery = Application::whereNotNull('vfs_appointment_date')
            ->selectRaw('MAX(id) as id')
            ->groupBy('student_id');

        $query = Application::with(['student', 'university.country'])
            ->whereIn('id', $latestVfsSubquery)
            ->orderBy('vfs_appointment_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_id', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('passport_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('vfs_result')) {
            $query->where('vfs_result', $request->vfs_result);
        }

        $applications = $query->paginate(20);

        // Add history for each application in the current page
        foreach ($applications as $app) {
            $app->history = Application::where('student_id', $app->student_id)
                ->where('id', '!=', $app->id)
                ->whereNotNull('vfs_appointment_date')
                ->with('university')
                ->orderBy('vfs_appointment_date', 'desc')
                ->get();
            
            // Count total attempts including current one
            $app->total_vfs_attempts = $app->history->count() + 1;
        }

        return view('admin.vfs-checklist.overview', compact('applications'));
    }

    public function vfsShow(Application $application)
    {
        $application->load(['student', 'university.country']);
        $application->history = Application::where('student_id', $application->student_id)
            ->where('id', '!=', $application->id)
            ->whereNotNull('vfs_appointment_date')
            ->with('university')
            ->orderBy('vfs_appointment_date', 'desc')
            ->get();

        return view('admin.vfs-checklist.vfs-show', compact('application'));
    }

    public function vfsEdit(Application $application)
    {
        $application->load(['student', 'university.country']);
        return view('admin.vfs-checklist.vfs-edit', compact('application'));
    }

    public function updateVfsResult(Request $request, Application $application)
    {
        $request->validate([
            'vfs_result' => 'required|in:submitted,not_submitted,not_attended,rejected,approved',
            'vfs_note' => 'nullable|string',
        ]);

        $data = ['vfs_result' => $request->vfs_result];
        
        // Only update vfs_note if it is actually sent in the request
        // This prevents the note from being cleared when updating status from the list
        if ($request->has('vfs_note')) {
            $data['vfs_note'] = $request->vfs_note;
        }

        $application->update($data);

        return redirect()->route('admin.vfs-checklist.overview')->with('success', 'VFS Status updated successfully.');
    }

    public function show(Application $application)
    {
        // Get country's ID from application's university
        $countryId = $application->university?->country_id;

        // Auto-create checklist items from templates if none exist
        if ($application->vfsChecklist()->count() === 0) {
            $this->createChecklistFromTemplates($application, $countryId);
        }

        // Get active template names filtered by country (global + country-specific)
        $activeTemplateNames = VfsChecklistTemplate::getActiveItems($countryId);

        $checklistItems = $application->vfsChecklist()
            ->with('checkedBy')
            ->whereIn('checklist_item', $activeTemplateNames)
            ->orderBy('id')
            ->get();

        return view('admin.vfs-checklist.show', compact('application', 'checklistItems', 'countryId'));
    }

    private function createChecklistFromTemplates(Application $application, ?int $countryId = null): void
    {
        $templates = VfsChecklistTemplate::where('is_active', true);

        // Filter by country: get global items + country-specific items
        if ($countryId) {
            $templates->where(function ($q) use ($countryId) {
                $q->whereNull('country_id')
                    ->orWhere('country_id', $countryId);
            });
        } else {
            $templates->whereNull('country_id');
        }

        $templates = $templates->orderBy('sort_order')->get();

        // If no templates exist, use default checklist (global only)
        if ($templates->isEmpty()) {
            $templates = collect(self::DEFAULT_CHECKLIST)->map(function ($item, $index) {
                return (object) [
                    'item_name' => $item,
                    'sort_order' => $index,
                    'country_id' => null,
                ];
            });
        }

        $items = $templates->map(function ($template) use ($application) {
            return [
                'application_id' => $application->id,
                'checklist_item' => $template->item_name,
                'is_checked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        VfsChecklist::insert($items->toArray());
    }

    public function resetToTemplates(Application $application)
    {
        $countryId = $application->university?->country_id;

        // Delete existing items
        $application->vfsChecklist()->delete();

        // Create from templates with country filter
        $this->createChecklistFromTemplates($application, $countryId);

        return redirect()->route('admin.vfs-checklist.show', $application)
            ->with('success', 'Checklist reset to country-specific List.');
    }

    public function syncWithTemplates(Application $application)
    {
        $countryId = $application->university?->country_id;
        $existingItems = $application->vfsChecklist()->pluck('checklist_item')->toArray();

        $activeTemplates = VfsChecklistTemplate::where('is_active', true);

        // Filter by country: get global items + country-specific items
        if ($countryId) {
            $activeTemplates->where(function ($q) use ($countryId) {
                $q->whereNull('country_id')
                    ->orWhere('country_id', $countryId);
            });
        } else {
            $activeTemplates->whereNull('country_id');
        }

        $activeTemplates = $activeTemplates->orderBy('sort_order')->get();

        $newItems = [];
        foreach ($activeTemplates as $template) {
            if (!in_array($template->item_name, $existingItems)) {
                $newItems[] = [
                    'application_id' => $application->id,
                    'checklist_item' => $template->item_name,
                    'is_checked' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($newItems)) {
            VfsChecklist::insert($newItems);
            $message = count($newItems) . ' new item(s) added from List.';
        } else {
            $message = 'No new items to add. Checklist is up to date.';
        }

        return redirect()->route('admin.vfs-checklist.show', $application)
            ->with('success', $message);
    }

    // Template Management
    public function templates()
    {
        $templates = VfsChecklistTemplate::with('country')
            ->orderBy('sort_order')
            ->orderBy('item_name')
            ->get();

        $countries = \App\Models\Country::where('status', '1')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.vfs-checklist.list', compact('templates', 'countries'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255|unique:vfs_checklist_templates,item_name',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        $maxOrder = VfsChecklistTemplate::max('sort_order') ?? 0;

        VfsChecklistTemplate::create([
            'item_name' => $request->item_name,
            'country_id' => $request->country_id,
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.vfs-checklist.templates')
            ->with('success', 'List item added successfully.');
    }

    public function updateTemplate(Request $request, VfsChecklistTemplate $template)
    {
        $request->validate([
            'item_name' => 'required|string|max:255|unique:vfs_checklist_templates,item_name,' . $template->id,
            'country_id' => 'nullable|exists:countries,id',
            'is_active' => 'boolean',
        ]);

        $template->update([
            'item_name' => $request->item_name,
            'country_id' => $request->country_id,
            'is_active' => $request->boolean('is_active', $template->is_active),
        ]);

        return redirect()->route('admin.vfs-checklist.templates')
            ->with('success', 'List item updated successfully.');
    }

    public function deleteTemplate(VfsChecklistTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.vfs-checklist.templates')
            ->with('success', 'List item deleted.');
    }

    public function reorderTemplates(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:vfs_checklist_templates,id',
        ]);

        foreach ($request->order as $index => $id) {
            VfsChecklistTemplate::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function seedDefaults()
    {
        $existing = VfsChecklistTemplate::pluck('item_name')->toArray();

        foreach (self::DEFAULT_CHECKLIST as $index => $item) {
            if (!in_array($item, $existing)) {
                VfsChecklistTemplate::create([
                    'item_name' => $item,
                    'sort_order' => $index,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('admin.vfs-checklist.templates')
            ->with('success', 'Default checklist items added successfully.');
    }

    public function storeItem(Request $request, Application $application)
    {
        $request->validate([
            'checklist_item' => 'required|string|max:255',
        ]);

        VfsChecklist::create([
            'application_id' => $application->id,
            'checklist_item' => $request->checklist_item,
        ]);

        return redirect()->back()->with('success', 'Checklist item added.');
    }

    public function toggleItem(VfsChecklist $item)
    {
        DB::transaction(function () use ($item) {
            if ($item->is_checked) {
                $item->update([
                    'is_checked' => false,
                    'checked_by' => null,
                    'checked_at' => null,
                ]);
            } else {
                $item->update([
                    'is_checked' => true,
                    'checked_by' => Auth::id(),
                    'checked_at' => now(),
                ]);
            }
        });

        return response()->json(['success' => true, 'is_checked' => $item->is_checked]);
    }

    public function updateNotes(Request $request, VfsChecklist $item)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $item->update(['notes' => $request->notes]);

        return response()->json(['success' => true]);
    }

    public function deleteItem(VfsChecklist $item)
    {
        $applicationId = $item->application_id;
        $item->delete();

        return redirect()->route('admin.vfs-checklist.show', $applicationId)
            ->with('success', 'Checklist item deleted.');
    }

    public function bulkCheck(Request $request, Application $application)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:vfs_checklists,id',
        ]);

        // Only update items that actually belong to this application
        VfsChecklist::whereIn('id', $request->item_ids)
            ->where('application_id', $application->id)
            ->update([
                'is_checked' => true,
                'checked_by' => Auth::id(),
                'checked_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function bulkUncheck(Request $request, Application $application)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:vfs_checklists,id',
        ]);

        // Only update items that actually belong to this application
        VfsChecklist::whereIn('id', $request->item_ids)
            ->where('application_id', $application->id)
            ->update([
                'is_checked' => false,
                'checked_by' => null,
                'checked_at' => null,
            ]);

        return response()->json(['success' => true]);
    }
}