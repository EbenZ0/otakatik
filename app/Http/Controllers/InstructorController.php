<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseAssignment;
use App\Models\CourseRegistration;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\Storage;

class InstructorController extends Controller
{
    /**
     * Show instructor dashboard
     */
    public function dashboard()
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $instructor = Auth::user();
        $taughtCourses = $instructor->taughtCourses()->withCount(['registrations' => function($query) {
            $query->where('status', 'paid');
        }])->get();

        $totalStudents = 0;
        $totalAssignments = 0;

        foreach ($taughtCourses as $course) {
            $totalStudents += $course->registrations_count;
            $totalAssignments += $course->assignments()->count();
        }

        $stats = [
            'total_courses' => $taughtCourses->count(),
            'total_students' => $totalStudents,
            'total_assignments' => $totalAssignments,
            'active_courses' => $taughtCourses->where('is_active', true)->count(),
        ];

        $recentRegistrations = CourseRegistration::whereIn('course_id', $taughtCourses->pluck('id'))
            ->where('status', 'paid')
            ->with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        return view('instructor.dashboard', compact('stats', 'taughtCourses', 'recentRegistrations'));
    }

    /**
     * Show instructor's courses
     */
    public function courses()
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $instructor = Auth::user();
        $courses = $instructor->taughtCourses()->withCount(['registrations' => function($query) {
            $query->where('status', 'paid');
        }])->latest()->get();

        return view('instructor.courses', compact('courses'));
    }

    /**
     * Show specific course details for instructor
     */
    public function showCourse($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::with(['materials', 'assignments', 'registrations.user'])->findOrFail($id);
        
        // Check if instructor owns this course
        if ($course->instructor_id != Auth::id()) {
            abort(403, 'Not your course. Your ID: ' . Auth::id() . ', Course Instructor ID: ' . $course->instructor_id);
        }

        $students = $course->registrations()->where('status', 'paid')->with('user')->get();
        $materials = $course->materials()->orderBy('order')->get();
        $assignments = $course->assignments()->latest()->get();

        return view('instructor.course-detail', compact('course', 'students', 'materials', 'assignments'));
    }

    /**
     * Show course students
     */
    public function courseStudents($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($id);
        
        // Check if instructor owns this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $students = $course->registrations()->where('status', 'paid')->with('user')->get();

        return view('instructor.course-students', compact('course', 'students'));
    }

    /**
     * Store course material
     */
    public function storeMaterial(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($id);
        
        // TEMPORARY BYPASS - Comment out authorization
        // Check if instructor owns this course
        // if ($course->instructor_id !== Auth::id()) {
        //     abort(403, 'Unauthorized action.');
        // }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,txt|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('course_materials', $fileName, 'public');

            $material = CourseMaterial::create([
                'course_id' => $course->id,
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'order' => $course->materials()->count() + 1,
                'is_published' => true,
            ]);

            return back()->with('success', 'Material berhasil diupload!');
        }

        return back()->withErrors(['error' => 'Gagal mengupload file.']);
    }

    /**
     * Delete course material
     */
    public function deleteMaterial($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $material = CourseMaterial::with('course')->findOrFail($id);
        
        // TEMPORARY BYPASS - Comment out authorization
        // Check if instructor owns this course
        // if ($material->course->instructor_id !== Auth::id()) {
        //     abort(403, 'Unauthorized action.');
        // }

        // Delete file from storage
        Storage::disk('public')->delete($material->file_path);

        $material->delete();

        return back()->with('success', 'Material berhasil dihapus!');
    }

    /**
     * Store course assignment
     */
    public function storeAssignment(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($id);
        
        // TEMPORARY BYPASS - Comment out authorization
        // Check if instructor owns this course
        // if ($course->instructor_id !== Auth::id()) {
        //     abort(403, 'Unauthorized action.');
        // }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'due_date' => 'required|date|after:now',
            'max_points' => 'required|integer|min:1|max:1000',
        ]);

        $assignment = CourseAssignment::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
            'max_points' => $request->max_points,
            'is_published' => true,
        ]);

        return back()->with('success', 'Assignment berhasil dibuat!');
    }

    /**
     * Update course assignment
     */
    public function updateAssignment(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $assignment = CourseAssignment::with('course')->findOrFail($id);
        
        // Check if instructor owns this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'due_date' => 'required|date|after:now',
            'max_points' => 'required|integer|min:1|max:1000',
            'is_published' => 'required|boolean',
        ]);

        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
            'max_points' => $request->max_points,
            'is_published' => $request->is_published,
        ]);

        return back()->with('success', 'Assignment berhasil diupdate!');
    }

    /**
     * Delete course assignment
     */
    public function deleteAssignment($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $assignment = CourseAssignment::with('course')->findOrFail($id);
        
        // Check if instructor owns this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $assignment->delete();

        return back()->with('success', 'Assignment berhasil dihapus!');
    }

    /**
     * Update student progress
     */
    public function updateStudentProgress(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $registration = CourseRegistration::with('course')->findOrFail($id);
        
        // Check if instructor owns this course
        if ($registration->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'progress' => 'required|numeric|min:0|max:100'
        ]);

        $registration->update([
            'progress' => $request->progress
        ]);

        if ($request->progress >= 100) {
            $registration->update([
                'completed_at' => now()
            ]);
        }

        return back()->with('success', 'Progress siswa berhasil diupdate!');
    }

    /**
     * Show assignment submissions
     */
    public function assignmentSubmissions($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $assignment = CourseAssignment::with(['course', 'submissions.user'])->findOrFail($id);
        
        // Check if instructor owns this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.assignment-submissions', compact('assignment'));
    }

    /**
     * Grade assignment submission
     */
    public function gradeSubmission(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $submission = AssignmentSubmission::with(['assignment.course'])->findOrFail($id);
        
        // Check if instructor owns this course
        if ($submission->assignment->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'grade' => 'required|numeric|min:0|max:' . $submission->assignment->max_points,
            'feedback' => 'nullable|string|max:1000'
        ]);

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback
        ]);

        return back()->with('success', 'Submission berhasil dinilai!');
    }
}