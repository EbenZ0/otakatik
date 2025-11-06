<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Response;

class CourseController extends Controller
{
    /**
     * Show course registration page
     */
    public function showCourse()
    {
        return view('course');
    }

    /**
     * Show course dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $courses = CourseRegistration::where('user_id', $user->id)
            ->with('user')
            ->latest()
            ->get();

        return view('course-dashboard', compact('user', 'courses'));
    }

    /**
     * Show user's courses
     */
    public function myCourses()
    {
        $user = Auth::user();
        $courses = CourseRegistration::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('my-courses', compact('courses'));
    }

    /**
     * Handle course registration
     */
    public function register(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'ttl' => 'required|string|max:255',
            'tempat_tinggal' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'course' => 'required|in:Starter,Pro Learner,Expert Mode',
            'sub_course1' => 'required|string',
            'sub_course2' => 'required|string',
            'kelas' => 'required|string',
        ]);

        // Tambahkan user_id dari user yang sedang login
        $validated['user_id'] = Auth::id();
        
        // Set harga berdasarkan paket yang dipilih
        $prices = [
            'Starter' => 99000,
            'Pro Learner' => 179000,
            'Expert Mode' => 299000
        ];
        $validated['price'] = $prices[$validated['course']];
        
        // Set status default
        $validated['status'] = 'pending';
        
        // Simpan ke database
        CourseRegistration::create($validated);
        
        return redirect()->route('my-courses')->with('success', 'Pendaftaran course berhasil! Kami akan menghubungi Anda segera.');
    }

    /**
     * Show all courses for admin
     */
    public function index()
    {
        $courses = CourseRegistration::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.courses', compact('courses'));
    }

    /**
     * Update course status
     */
    public function updateStatus(Request $request, $id)
    {
        $course = CourseRegistration::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled'
        ]);

        $course->update(['status' => $request->status]);

        return back()->with('success', 'Status course berhasil diupdate!');
    }

    /**
     * Show course details
     */
    public function show($id)
    {
        $course = CourseRegistration::with('user')->findOrFail($id);
        
        // Check if user is authorized to view this course
        if (Auth::id() !== $course->user_id && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('course-detail', compact('course'));
    }

    /**
     * Delete course registration
     */
    public function destroy($id)
    {
        $course = CourseRegistration::findOrFail($id);
        
        // Check if user is authorized to delete this course
        if (Auth::id() !== $course->user_id && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $course->delete();

        return redirect()->route('my-courses')->with('success', 'Pendaftaran course berhasil dihapus!');
    }

    /**
     * Export courses to CSV
     */
    public function export()
    {
        $courses = CourseRegistration::with('user')->get();
        
        $filename = "courses_export_" . date('Y-m-d') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $handle = fopen('php://output', 'w');
        
        // Add BOM for Excel compatibility
        fwrite($handle, "\xEF\xBB\xBF");
        
        fputcsv($handle, ['Nama User', 'Email', 'Nama Lengkap', 'TTL', 'Tempat Tinggal', 'Gender', 'Course', 'Sub Course 1', 'Sub Course 2', 'Kelas', 'Harga', 'Status', 'Tanggal Registrasi']);

        foreach ($courses as $course) {
            fputcsv($handle, [
                $course->user->name,
                $course->user->email,
                $course->nama_lengkap,
                $course->ttl,
                $course->tempat_tinggal,
                $course->gender,
                $course->course,
                $course->sub_course1,
                $course->sub_course2,
                $course->kelas,
                $course->price,
                $course->status,
                $course->created_at->format('Y-m-d H:i:s')
            ]);
        }

        fclose($handle);

        return Response::make('', 200, $headers);
    }
}