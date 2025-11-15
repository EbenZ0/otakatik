<?php

namespace App\Http\Controllers;

use App\Models\CourseRegistration;
use App\Models\Course;
use App\Models\Refund;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Get user's enrolled courses
        $enrolledCourses = CourseRegistration::where('user_id', $user->id)
            ->with(['course', 'refund'])
            ->latest()
            ->paginate(6);

        // Get refund requests
        $refundRequests = Refund::where('user_id', $user->id)
            ->with(['registration.course'])
            ->latest()
            ->limit(5)
            ->get();

        // Calculate stats
        $stats = [
            'total_courses' => CourseRegistration::where('user_id', $user->id)->count(),
            'active_courses' => CourseRegistration::where('user_id', $user->id)
                ->where('status', 'paid')
                ->count(),
            'pending_refunds' => Refund::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'approved_refunds' => Refund::where('user_id', $user->id)
                ->where('status', 'approved')
                ->sum('amount'),
        ];

        return view('student.dashboard', compact('enrolledCourses', 'refundRequests', 'stats'));
    }

    public function myCourses()
    {
        $courses = CourseRegistration::where('user_id', Auth::id())
            ->with(['course', 'refund'])
            ->latest()
            ->paginate(12);

        return view('student.courses', compact('courses'));
    }

    public function courseDetail($registrationId)
    {
        $registration = CourseRegistration::where('id', $registrationId)
            ->where('user_id', Auth::id())
            ->with(['course', 'refund'])
            ->firstOrFail();

        // Get refund if exists
        $refund = Refund::where('course_registration_id', $registrationId)->first();

        return view('student.course-detail', compact('registration', 'refund'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('student.profile', compact('user'));
    }

    public function updateProfile($request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $validated['profile_picture'] = $path;
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}