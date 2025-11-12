<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\User;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'instructor_users' => User::where('is_instructor', true)->count(),
            'regular_users' => User::where('is_admin', false)->where('is_instructor', false)->count(),
            'active_this_month' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'inactive_courses' => Course::where('is_active', false)->count(),
            'total_registrations' => CourseRegistration::count(),
            'pending_registrations' => CourseRegistration::where('status', 'pending')->count(),
            'paid_registrations' => CourseRegistration::where('status', 'paid')->count(),
            'cancelled_registrations' => CourseRegistration::where('status', 'cancelled')->count(),
            'total_revenue' => CourseRegistration::where('status', 'paid')->sum('final_price'),
            'monthly_revenue' => CourseRegistration::where('status', 'paid')
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('final_price'),
        ];

        $recentRegistrations = CourseRegistration::with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        $popularCourses = Course::withCount(['registrations' => function($query) {
                $query->where('status', 'paid');
            }])
            ->orderBy('registrations_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentRegistrations', 'popularCourses'));
    }

    /**
     * Show users management page
     */
    public function users()
    {
        $users = User::withCount('courseRegistrations')->latest()->paginate(10);
        
        $userStats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'instructor_users' => User::where('is_instructor', true)->count(),
            'regular_users' => User::where('is_admin', false)->where('is_instructor', false)->count(),
            'active_this_month' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Sample data for charts (you can replace with actual data)
        $ageDistribution = [
            ['range' => '18-24', 'count' => 15, 'color' => '#3B82F6'],
            ['range' => '25-34', 'count' => 25, 'color' => '#10B981'],
            ['range' => '35-44', 'count' => 18, 'color' => '#F59E0B'],
            ['range' => '45-54', 'count' => 12, 'color' => '#EF4444'],
            ['range' => '55+', 'count' => 8, 'color' => '#8B5CF6'],
        ];

        $educationDistribution = [
            ['level' => 'High School', 'count' => 20, 'color' => '#3B82F6'],
            ['level' => 'Bachelor', 'count' => 35, 'color' => '#10B981'],
            ['level' => 'Master', 'count' => 15, 'color' => '#F59E0B'],
            ['level' => 'Doctorate', 'count' => 5, 'color' => '#EF4444'],
            ['level' => 'Other', 'count' => 3, 'color' => '#8B5CF6'],
        ];

        $locationDistribution = [
            ['location' => 'Jakarta', 'count' => 25, 'color' => '#3B82F6'],
            ['location' => 'Bandung', 'count' => 15, 'color' => '#10B981'],
            ['location' => 'Surabaya', 'count' => 12, 'color' => '#F59E0B'],
            ['location' => 'Bali', 'count' => 8, 'color' => '#EF4444'],
            ['location' => 'Lainnya', 'count' => 18, 'color' => '#8B5CF6'],
        ];

        return view('admin.users', compact('users', 'userStats', 'ageDistribution', 'educationDistribution', 'locationDistribution'));
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        
        $request->validate([
            'role' => 'required|in:user,admin,instructor'
        ]);

        $user->update([
            'is_admin' => $request->role === 'admin',
            'is_instructor' => $request->role === 'instructor'
        ]);

        return back()->with('success', 'User role updated successfully!');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }

    /**
     * Show course registrations management page
     */
    public function courses()
    {
        $courses = CourseRegistration::with(['user', 'course.instructor'])
            ->latest()
            ->paginate(10);

        return view('admin.courses', compact('courses'));
    }

    /**
 * Update course registration status
 */
public function updateCourseStatus(Request $request, $id)
{
    $registration = CourseRegistration::findOrFail($id);
    
    $request->validate([
        'status' => 'required|in:pending,paid,cancelled'
    ]);

    $registration->update([
        'status' => $request->status
    ]);

    // Auto-approve jika menggunakan coupon instructor
    if ($registration->discount_code === 'INSTRUCTOR100' && $request->status === 'pending') {
        $registration->update(['status' => 'paid']);
    }

    // Update course enrollment count if status is paid
    if ($request->status === 'paid' || $registration->discount_code === 'INSTRUCTOR100') {
        $course = $registration->course;
        $course->update([
            'current_enrollment' => $course->registrations()->where('status', 'paid')->count()
        ]);
    }

    return back()->with('success', 'Course status updated successfully!');
}

    /**
     * Export courses to CSV
     */
    public function exportCourses()
    {
        $registrations = CourseRegistration::with(['user', 'course'])->get();
        
        $fileName = 'course_registrations_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['ID', 'User Name', 'User Email', 'Course Title', 'Price', 'Final Price', 'Status', 'Registration Date']);

        foreach ($registrations as $registration) {
            fputcsv($handle, [
                $registration->id,
                $registration->user->name,
                $registration->user->email,
                $registration->course->title,
                $registration->price,
                $registration->final_price,
                $registration->status,
                $registration->created_at->format('Y-m-d H:i:s')
            ]);
        }

        fclose($handle);
        
        return response()->streamDownload(function() use ($handle) {
            //
        }, $fileName, $headers);
    }

    /**
     * Show financial analytics page
     */
    public function financial()
    {
        // Sample financial data
        $revenueData = [
            'total_revenue' => CourseRegistration::where('status', 'paid')->sum('final_price'),
            'monthly_revenue' => CourseRegistration::where('status', 'paid')
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('final_price'),
            'total_registrations' => CourseRegistration::where('status', 'paid')->count(),
            'average_ticket' => CourseRegistration::where('status', 'paid')->avg('final_price'),
        ];

        return view('admin.financial', compact('revenueData'));
    }

    /**
     * Show refund management page
     */
    public function refund()
    {
        $refundRequests = CourseRegistration::where('status', 'cancelled')
            ->with(['user', 'course'])
            ->latest()
            ->get();

        return view('admin.refund', compact('refundRequests'));
    }

    /**
     * Process refund request
     */
    public function processRefund(Request $request, $id)
    {
        $registration = CourseRegistration::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string'
        ]);

        // Process refund logic here
        // This is where you would integrate with payment gateway

        $action = $request->action === 'approve' ? 'approved' : 'rejected';
        
        return back()->with('success', "Refund request {$action} successfully!");
    }

    /**
     * Show course management page (Tambah Course)
     */
    public function manageCourses()
    {
        $courses = Course::with('instructor')->latest()->get();
        $instructors = User::where('is_instructor', true)->get();
        
        return view('admin.manage-courses', compact('courses', 'instructors'));
    }

    /**
     * Create new course - Fix for Oracle constraints
     */
    public function createCourse(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'type' => 'required|in:Full Online,Hybrid,Tatap Muka', // UPDATE INI
        'instructor_id' => 'nullable|exists:users,id',
        'price' => 'required|numeric|min:0',
        'discount_percent' => 'required|numeric|min:0|max:100',
        'discount_code' => 'nullable|string|max:50',
        'min_quota' => 'required|integer|min:1',
        'max_quota' => 'required|integer|min:1',
        'is_active' => 'boolean'
    ]);

    $courseData = [
        'title' => $validated['title'],
        'description' => $validated['description'],
        'type' => $validated['type'], // UPDATE INI
        'price' => (float)$validated['price'],
        'discount_percent' => (float)$validated['discount_percent'],
        'discount_code' => $validated['discount_code'] ?? null,
        'min_quota' => (int)$validated['min_quota'],
        'max_quota' => (int)$validated['max_quota'],
        'current_enrollment' => 0,
        'is_active' => $validated['is_active'] ?? true,
    ];

    // Handle instructor - Full Online tidak butuh instructor
    if ($validated['type'] === 'Full Online') {
        $courseData['instructor_id'] = null;
    } else {
        $courseData['instructor_id'] = $validated['instructor_id'];
        
        // Validasi untuk Hybrid/Tatap Muka harus punya instructor
        if (empty($validated['instructor_id'])) {
            return redirect()->back()->withErrors(['instructor_id' => 'Instruktur harus dipilih untuk course Hybrid/Tatap Muka'])->withInput();
        }
    }

    Course::create($courseData);

    return redirect()->route('admin.courses.manage')->with('success', 'Course "'.$validated['title'].'" berhasil dibuat!');
}

    /**
     * Show edit course form
     */
    public function editCourse($id)
    {
        $course = Course::findOrFail($id);
        $instructors = User::where('is_instructor', true)->get();
        
        return view('admin.edit-course', compact('course', 'instructors'));
    }

    /**
     * Update course
     */
    public function updateCourse(Request $request, $id)
{
    $course = Course::findOrFail($id);
    
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'type' => 'required|in:Full Online,Hybrid,Tatap Muka', // UPDATE INI JUGA
        'instructor_id' => 'nullable|exists:users,id',
        'price' => 'required|numeric|min:0',
        'discount_percent' => 'required|numeric|min:0|max:100',
        'discount_code' => 'nullable|string|max:50',
        'min_quota' => 'required|integer|min:1',
        'max_quota' => 'required|integer|min:1',
        'is_active' => 'boolean'
    ]);

    // Handle instructor
    if ($validated['type'] === 'Full Online') {
        $validated['instructor_id'] = null;
    }

    $course->update($validated);

    return redirect()->route('admin.courses.manage')->with('success', 'Course berhasil diupdate!');
}

    /**
     * Delete course
     */
    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        
        // Check if there are any registrations for this course
        if ($course->registrations()->count() > 0) {
            return redirect()->route('admin.courses.manage')->with('error', 'Tidak bisa menghapus course yang sudah memiliki pendaftaran!');
        }

        $course->delete();

        return redirect()->route('admin.courses.manage')->with('success', 'Course berhasil dihapus!');
    }

    /**
     * Toggle course active status
     */
    public function toggleCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->update([
            'is_active' => !$course->is_active
        ]);

        $status = $course->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.courses.manage')->with('success', "Course berhasil $status!");
    }

    /**
     * Update course active status
     */
    public function updateCourseActiveStatus(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $course->update([
            'is_active' => $request->is_active
        ]);

        $status = $request->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json(['success' => true, 'message' => "Course berhasil $status!"]);
    }
}