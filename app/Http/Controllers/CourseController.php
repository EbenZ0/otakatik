<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class CourseController extends Controller
{
    /**
     * Show course registration page
     */
    public function showCourse()
    {
        $courses = Course::active()->with('instructor')->get();
        return view('course', compact('courses'));
    }

    /**
     * Show course dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $enrolledCourses = $user->enrolledCourses;
        
        // Calculate overall progress
        $totalProgress = 0;
        $courseCount = $enrolledCourses->count();
        
        if ($courseCount > 0) {
            foreach ($enrolledCourses as $registration) {
                $totalProgress += $registration->progress;
            }
            $overallProgress = $totalProgress / $courseCount;
        } else {
            $overallProgress = 0;
        }

        return view('course-dashboard', compact('user', 'enrolledCourses', 'overallProgress'));
    }

    /**
     * Show user's enrolled courses
     */
    public function myCourses()
    {
        $user = Auth::user();
        $enrolledCourses = $user->enrolledCourses;
        
        return view('my-courses', compact('enrolledCourses'));
    }

    /**
     * Show purchase history
     */
    public function purchaseHistory()
    {
        $user = Auth::user();
        $allRegistrations = CourseRegistration::where('user_id', $user->id)
            ->with('course')
            ->latest()
            ->get();

        return view('purchase-history', compact('allRegistrations'));
    }

    /**
     * Handle course registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'nama_lengkap' => 'required|string|max:255',
            'ttl' => 'required|string|max:255',
            'tempat_tinggal' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'discount_code' => 'nullable|string|max:50',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        
        // Check if course has available slots
        if (!$course->hasAvailableSlots()) {
            return back()->withErrors(['error' => 'Maaf, kuota untuk kursus ini sudah penuh.']);
        }

        // Calculate price with discount
        $price = $course->price;
        $finalPrice = $course->price;
        $discountCode = null;

        if (!empty($validated['discount_code'])) {
            if ($validated['discount_code'] === 'PROMOPNJ') {
                $finalPrice = $price * 0.9; // 10% discount
                $discountCode = 'PROMOPNJ';
            }
        }

        // Create registration
        $registration = CourseRegistration::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'nama_lengkap' => $validated['nama_lengkap'],
            'ttl' => $validated['ttl'],
            'tempat_tinggal' => $validated['tempat_tinggal'],
            'gender' => $validated['gender'],
            'price' => $price,
            'final_price' => $finalPrice,
            'discount_code' => $discountCode,
            'status' => 'pending',
            'progress' => 0,
        ]);

        return redirect()->route('purchase.history')->with('success', 'Pendaftaran course berhasil! Menunggu persetujuan admin.');
    }

    /**
     * Show course details
     */
    public function show($id)
    {
        $course = Course::with(['instructor', 'materials', 'assignments'])->findOrFail($id);
        
        // Check if user is enrolled in this course
        $isEnrolled = false;
        $userRegistration = null;
        
        if (Auth::check()) {
            $userRegistration = CourseRegistration::where('user_id', Auth::id())
                ->where('course_id', $id)
                ->where('status', 'paid')
                ->first();
            $isEnrolled = !is_null($userRegistration);
        }

        return view('course-detail', compact('course', 'isEnrolled', 'userRegistration'));
    }

    /**
     * Delete course registration
     */
    public function destroy($id)
    {
        $course = CourseRegistration::findOrFail($id);
        
        // Check if user is authorized to delete this course registration
        if (Auth::id() !== $course->user_id && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $course->delete();

        return redirect()->route('purchase.history')->with('success', 'Pendaftaran course berhasil dihapus!');
    }

    /**
     * Update course progress (for instructors)
     */
    public function updateProgress(Request $request, $id)
    {
        $registration = CourseRegistration::findOrFail($id);
        
        // Check if user is instructor of this course
        if (Auth::user()->is_instructor && $registration->course->instructor_id !== Auth::id()) {
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

        return back()->with('success', 'Progress berhasil diupdate!');
    }
}