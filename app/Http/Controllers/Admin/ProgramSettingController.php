<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramLevel;
use App\Models\ProgramRequirement;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgramSettingController extends Controller
{
    public function index(Request $request)
    {
        $levels = ProgramLevel::orderBy('sort_order')->orderBy('id')->get();
        $requirements = ProgramRequirement::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.program-settings.index', compact('levels', 'requirements'));
    }

    // --- PROGRAM LEVELS ---

    public function storeLevel(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:program_levels,code',
            'sort_order' => 'nullable|integer',
        ]);

        $code = !empty($validated['code']) 
            ? Str::slug($validated['code'], '_') 
            : Str::slug($validated['name'], '_');

        // Ensure unique code
        $baseCode = $code;
        $counter = 1;
        while (ProgramLevel::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter++;
        }

        ProgramLevel::create([
            'name' => $validated['name'],
            'code' => $code,
            'sort_order' => $validated['sort_order'] ?? (ProgramLevel::max('sort_order') + 1),
            'is_active' => true,
        ]);

        return redirect()->route('admin.program-settings.index', ['tab' => 'levels'])
            ->with('success', 'Program level added successfully.');
    }

    public function updateLevel(Request $request, ProgramLevel $level)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $level->update([
            'name' => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? $level->sort_order,
        ]);

        return redirect()->route('admin.program-settings.index', ['tab' => 'levels'])
            ->with('success', 'Program level updated successfully.');
    }

    public function destroyLevel(ProgramLevel $level)
    {
        $usedInCourses = Course::where('program_level', $level->code)->count();
        if ($usedInCourses > 0) {
            return redirect()->route('admin.program-settings.index', ['tab' => 'levels'])
                ->with('error', "Cannot delete '{$level->name}' because it is assigned to {$usedInCourses} courses. You can deactivate it instead.");
        }

        $level->delete();

        return redirect()->route('admin.program-settings.index', ['tab' => 'levels'])
            ->with('success', 'Program level deleted successfully.');
    }

    public function toggleLevel(ProgramLevel $level)
    {
        $level->update(['is_active' => !$level->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $level->is_active,
            'message' => "Level '{$level->name}' status updated."
        ]);
    }

    // --- PROGRAM REQUIREMENTS ---

    public function storeRequirement(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:program_requirements,code',
            'category' => 'nullable|string|in:test,waiver,other',
            'sort_order' => 'nullable|integer',
        ]);

        $code = !empty($validated['code']) 
            ? Str::slug($validated['code'], '_') 
            : Str::slug($validated['name'], '_');

        $baseCode = $code;
        $counter = 1;
        while (ProgramRequirement::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter++;
        }

        ProgramRequirement::create([
            'name' => $validated['name'],
            'code' => $code,
            'category' => $validated['category'] ?? 'test',
            'sort_order' => $validated['sort_order'] ?? (ProgramRequirement::max('sort_order') + 1),
            'is_active' => true,
        ]);

        return redirect()->route('admin.program-settings.index', ['tab' => 'requirements'])
            ->with('success', 'Requirement added successfully.');
    }

    public function updateRequirement(Request $request, ProgramRequirement $requirement)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'nullable|string|in:test,waiver,other',
            'sort_order' => 'nullable|integer',
        ]);

        $requirement->update([
            'name' => $validated['name'],
            'category' => $validated['category'] ?? $requirement->category,
            'sort_order' => $validated['sort_order'] ?? $requirement->sort_order,
        ]);

        return redirect()->route('admin.program-settings.index', ['tab' => 'requirements'])
            ->with('success', 'Requirement updated successfully.');
    }

    public function destroyRequirement(ProgramRequirement $requirement)
    {
        $requirement->delete();

        return redirect()->route('admin.program-settings.index', ['tab' => 'requirements'])
            ->with('success', 'Requirement deleted successfully.');
    }

    public function toggleRequirement(ProgramRequirement $requirement)
    {
        $requirement->update(['is_active' => !$requirement->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $requirement->is_active,
            'message' => "Requirement '{$requirement->name}' status updated."
        ]);
    }
}

