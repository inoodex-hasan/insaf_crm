<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentActivity;
use Illuminate\Http\Request;

class StudentActivityController extends Controller
{
    /**
     * Store a new activity / note / call record for a student.
     */
    public function store(Request $request, Student $student)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:note,call,meeting,status_change,document,application,payment,vfs,system'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $activity = $student->activities()->create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'properties' => [
                'logged_by_name' => auth()->user()?->name,
                'logged_at' => now()->toDateTimeString(),
            ],
        ]);

        $activity->load('user');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Activity logged successfully.',
                'activity' => [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'type_label' => $activity->type_label,
                    'type_badge_class' => $activity->type_badge_class,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'user_name' => $activity->user?->name ?? 'System',
                    'created_at_human' => $activity->created_at->diffForHumans(),
                    'created_at_formatted' => $activity->created_at->format('M d, Y h:i A'),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Activity logged successfully.');
    }

    /**
     * Delete an activity record (if permitted).
     */
    public function destroy(Student $student, StudentActivity $activity)
    {
        // Ensure activity belongs to the student
        if ($activity->student_id !== $student->id) {
            abort(404);
        }

        // Only creator or admin can delete
        if ($activity->user_id && $activity->user_id !== auth()->id() && !auth()->user()->can('*admin')) {
            abort(403, 'Unauthorized action.');
        }

        $activity->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Activity record deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Activity record deleted successfully.');
    }
}

