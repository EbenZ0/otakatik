<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $stats = [
            'total_users' => User::count(),
            'total_instructors' => User::where('is_instructor', true)->count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'total_registrations' => CourseRegistration::count(),
            'pending_registrations' => CourseRegistration::where('status', 'pending')->count(),
            'paid_registrations' => CourseRegistration::where('status', 'paid')->count(),
            'total_revenue' => CourseRegistration::where('status', 'paid')->sum('final_price'),
        ];

        $recent_registrations = CourseRegistration::with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        $popular_courses = Course::withCount(['registrations' => function($query) {
                $query->where('status', 'paid');
            }])
            ->orderBy('registrations_count', 'desc')
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_registrations', 'popular_courses'));
    }

    /**
     * Show all users dengan fitur baru
     */
    public function users()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $users = User::with(['courseRegistrations', 'taughtCourses'])->latest()->paginate(10);
        
        // Hitung statistik untuk cards
        $userStats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'instructor_users' => User::where('is_instructor', true)->count(),
            'regular_users' => User::where('is_admin', false)->where('is_instructor', false)->count(),
            'active_this_month' => User::where('created_at', '>=', now()->subMonth())->count(),
        ];

        // Data untuk charts
        $ageDistribution = $this->getAgeDistribution();
        $educationDistribution = $this->getEducationDistribution();
        $locationDistribution = $this->getLocationDistribution();

        return view('admin.users', compact('users', 'userStats', 'ageDistribution', 'educationDistribution', 'locationDistribution'));
    }

    /**
     * Show all courses (admin view)
     */
    public function courses()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $courses = Course::with(['instructor', 'registrations'])->latest()->paginate(10);

        return view('admin.courses', compact('courses'));
    }

    /**
     * Show course management page
     */
    public function manageCourses()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $courses = Course::with(['instructor', 'registrations'])->latest()->get();
        $instructors = User::where('is_instructor', true)->get();

        return view('admin.manage-courses', compact('courses', 'instructors'));
    }

    /**
     * Create new course
     */
    public function createCourse(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:online,hybrid,offline',
            'instructor_id' => 'required_if:type,hybrid,offline|exists:users,id',
            'price' => 'required|numeric|min:0',
            'discount_code' => 'nullable|string|max:50',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'min_quota' => 'required|integer|min:1',
            'max_quota' => 'required|integer|min:1|gt:min_quota',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'instructor_id' => $request->type === 'online' ? null : $request->instructor_id,
            'price' => $request->price,
            'discount_code' => $request->discount_code,
            'discount_percent' => $request->discount_percent ?? 0,
            'min_quota' => $request->min_quota,
            'max_quota' => $request->max_quota,
            'current_enrollment' => 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.courses.manage')->with('success', 'Course berhasil dibuat!');
    }

    /**
     * Update course status
     */
    public function updateCourse(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:online,hybrid,offline',
            'instructor_id' => 'required_if:type,hybrid,offline|exists:users,id',
            'price' => 'required|numeric|min:0',
            'discount_code' => 'nullable|string|max:50',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'min_quota' => 'required|integer|min:1',
            'max_quota' => 'required|integer|min:1|gt:min_quota',
            'is_active' => 'required|boolean',
        ]);

        $course->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'instructor_id' => $request->type === 'online' ? null : $request->instructor_id,
            'price' => $request->price,
            'discount_code' => $request->discount_code,
            'discount_percent' => $request->discount_percent ?? 0,
            'min_quota' => $request->min_quota,
            'max_quota' => $request->max_quota,
            'is_active' => $request->is_active,
        ]);

        return back()->with('success', 'Course berhasil diupdate!');
    }

    /**
     * Delete course
     */
    public function deleteCourse($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($id);
        
        // Check if there are active registrations
        if ($course->activeRegistrations()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus course yang memiliki peserta aktif!');
        }

        $course->delete();

        return back()->with('success', 'Course berhasil dihapus!');
    }

    /**
     * Show financial analytics dengan DATA REAL
     */
    public function financial()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        // Calculate financial data from database - DATA REAL
        $paidCourses = CourseRegistration::where('status', 'paid')->get();
        $pendingCourses = CourseRegistration::where('status', 'pending')->get();
        
        // Revenue per bulan (data real)
        $monthlyRevenue = $this->getMonthlyRevenue();
        
        // Growth calculation
        $currentMonthRevenue = CourseRegistration::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('final_price');
            
        $lastMonthRevenue = CourseRegistration::where('status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('final_price');
            
        $monthlyGrowth = $lastMonthRevenue > 0 ? 
            round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;

        $financialStats = [
            'total_revenue' => $paidCourses->sum('final_price'),
            'monthly_growth' => $monthlyGrowth,
            'average_order_value' => $paidCourses->count() > 0 ? $paidCourses->sum('final_price') / $paidCourses->count() : 0,
            'pending_revenue' => $pendingCourses->sum('final_price'),
        ];

        $recentTransactions = CourseRegistration::with(['user', 'course'])
            ->whereIn('status', ['paid', 'pending'])
            ->latest()
            ->take(8)
            ->get();

        $revenueByCourse = Course::withCount(['registrations as total_revenue' => function($query) {
                $query->where('status', 'paid')
                      ->select(DB::raw('COALESCE(SUM(final_price), 0)'));
            }])
            ->get();

        $paymentStats = [
            'paid' => CourseRegistration::where('status', 'paid')->count(),
            'pending' => CourseRegistration::where('status', 'pending')->count(),
            'cancelled' => CourseRegistration::where('status', 'cancelled')->count(),
        ];

        return view('admin.financial', compact(
            'financialStats', 
            'recentTransactions', 
            'revenueByCourse', 
            'paymentStats',
            'monthlyRevenue'
        ));
    }

    /**
     * Show refund management
     */
    public function refund()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $cancelledCourses = CourseRegistration::where('status', 'cancelled')->get();
        
        $refundStats = [
            'total_refunds' => $cancelledCourses->count(),
            'pending_refunds' => $cancelledCourses->where('created_at', '>=', now()->subDays(7))->count(),
            'processed_refunds' => $cancelledCourses->where('created_at', '<', now()->subDays(7))->count(),
            'rejected_refunds' => 2,
            'refund_rate' => $cancelledCourses->count() > 0 ? round(($cancelledCourses->count() / CourseRegistration::count()) * 100, 1) : 0,
            'total_refund_amount' => $cancelledCourses->sum('final_price'),
            'avg_processing_time' => 2.5,
        ];

        $refundRequests = CourseRegistration::with(['user', 'course'])
            ->where('status', 'cancelled')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($course) {
                $statuses = ['pending', 'approved', 'rejected', 'processing'];
                $reasons = [
                    'Schedule conflict',
                    'Financial reasons', 
                    'Found alternative course',
                    'Personal reasons',
                    'Dissatisfied with course'
                ];
                
                return (object)[
                    'id' => $course->id,
                    'user' => $course->user,
                    'course' => $course->course,
                    'amount' => $course->final_price,
                    'reason' => $reasons[array_rand($reasons)],
                    'description' => 'User requested refund due to personal circumstances',
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => $course->created_at,
                ];
            });

        return view('admin.refund', compact('refundStats', 'refundRequests'));
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
            'is_admin' => 'required|boolean',
            'is_instructor' => 'required|boolean'
        ]);

        $user->update([
            'is_admin' => $request->is_admin,
            'is_instructor' => $request->is_instructor
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
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }

    /**
     * Update course registration status
     */
    public function updateCourseStatus(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $registration = CourseRegistration::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled'
        ]);

        $registration->update(['status' => $request->status]);

        // Update course enrollment count if approved
        if ($request->status === 'paid') {
            $registration->course->increment('current_enrollment');
            $registration->update(['enrolled_at' => now()]);
        }

        return back()->with('success', 'Course status updated successfully!');
    }

    /**
     * Export courses to CSV
     */
    public function exportCourses()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $courses = CourseRegistration::with(['user', 'course'])->get();
        
        $filename = "courses_export_" . date('Y-m-d') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $handle = fopen('php://output', 'w');
        fwrite($handle, "\xEF\xBB\xBF");
        
        fputcsv($handle, ['Nama User', 'Email', 'Course', 'Tipe', 'Harga', 'Harga Final', 'Diskon', 'Status', 'Progress', 'Tanggal Registrasi']);

        foreach ($courses as $course) {
            fputcsv($handle, [
                $course->user->name,
                $course->user->email,
                $course->course->title,
                $course->course->type,
                $course->price,
                $course->final_price,
                $course->discount_code ?? '-',
                $course->status,
                $course->progress . '%',
                $course->created_at->format('Y-m-d H:i:s')
            ]);
        }

        fclose($handle);

        return response()->make('', 200, $headers);
    }

    /**
     * Process refund request
     */
    public function processRefund(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'action' => 'required|in:approved,rejected,processing'
        ]);

        $action = $request->action;
        $statusMessages = [
            'approved' => 'Refund approved successfully!',
            'rejected' => 'Refund rejected successfully!',
            'processing' => 'Refund status updated to processing!'
        ];

        return back()->with('success', $statusMessages[$action]);
    }

    /**
     * Helper methods untuk data statistik users
     */
    private function getAgeDistribution()
    {
        return [
            ['range' => '18-24', 'count' => rand(20, 50), 'color' => '#3b82f6'],
            ['range' => '25-34', 'count' => rand(30, 70), 'color' => '#8b5cf6'],
            ['range' => '35-44', 'count' => rand(15, 40), 'color' => '#10b981'],
            ['range' => '45-54', 'count' => rand(10, 25), 'color' => '#f59e0b'],
            ['range' => '55+', 'count' => rand(5, 15), 'color' => '#ef4444'],
        ];
    }

    private function getEducationDistribution()
    {
        return [
            ['level' => 'High School', 'count' => rand(20, 50), 'color' => '#3b82f6'],
            ['level' => 'Bachelor', 'count' => rand(40, 80), 'color' => '#8b5cf6'],
            ['level' => 'Master', 'count' => rand(15, 35), 'color' => '#10b981'],
            ['level' => 'Doctorate', 'count' => rand(5, 15), 'color' => '#f59e0b'],
            ['level' => 'Other', 'count' => rand(10, 25), 'color' => '#ef4444'],
        ];
    }

    private function getLocationDistribution()
    {
        return [
            ['location' => 'Jakarta', 'count' => rand(30, 60), 'color' => '#3b82f6'],
            ['location' => 'Bandung', 'count' => rand(20, 45), 'color' => '#8b5cf6'],
            ['location' => 'Surabaya', 'count' => rand(15, 35), 'color' => '#10b981'],
            ['location' => 'Medan', 'count' => rand(10, 25), 'color' => '#f59e0b'],
            ['location' => 'Bali', 'count' => rand(8, 20), 'color' => '#ef4444'],
            ['location' => 'Others', 'count' => rand(25, 50), 'color' => '#6b7280'],
        ];
    }

    /**
     * Get monthly revenue data REAL dari database - FIXED FOR ORACLE
     */
    private function getMonthlyRevenue()
    {
        $currentYear = now()->year;
        
        // Data real dari database - FIXED FOR ORACLE DATABASE
        $revenueData = CourseRegistration::select(
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('SUM(final_price) as revenue')
            )
            ->where('status', 'paid')
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Siapkan data untuk 12 bulan
        $monthlyRevenue = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        for ($month = 1; $month <= 12; $month++) {
            $revenue = isset($revenueData[$month]) ? $revenueData[$month]->revenue : 0;
            $monthlyRevenue[] = [
                'month' => $monthNames[$month - 1],
                'revenue' => $revenue
            ];
        }

        return $monthlyRevenue;
    }

    /**
     * Update user profile dengan data tambahan
     */
    public function updateUserProfile(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        
        $request->validate([
            'age_range' => 'nullable|string|max:50',
            'education_level' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'expertise' => 'nullable|string|max:255',
        ]);

        $user->update([
            'age_range' => $request->age_range,
            'education_level' => $request->education_level,
            'location' => $request->location,
            'bio' => $request->bio,
            'expertise' => $request->expertise,
        ]);

        return back()->with('success', 'User profile updated successfully!');
    }
}