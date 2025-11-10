<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InstructorController;
use Illuminate\Support\Facades\Route;

// Home Route
Route::get('/', function () {
    return view('dashboard');
});

// Public Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/course', [CourseController::class, 'showCourse'])->name('course');
Route::get('/course/{id}', [CourseController::class, 'show'])->name('course.show');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected Routes (Require Authentication)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // User Course Routes
    Route::get('/course-dashboard', [CourseController::class, 'dashboard'])->name('course.dashboard');
    Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('my-courses');
    Route::get('/purchase-history', [CourseController::class, 'purchaseHistory'])->name('purchase.history');
    Route::post('/course/register', [CourseController::class, 'register'])->name('course.register');
    Route::delete('/course/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
});

// Instructor Routes (Require Instructor Privileges)
Route::middleware(['auth', 'instructor'])->prefix('instructor')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('instructor.dashboard');
    Route::get('/courses', [InstructorController::class, 'courses'])->name('instructor.courses');
    Route::get('/course/{id}', [InstructorController::class, 'showCourse'])->name('instructor.course.show');
    Route::get('/course/{id}/students', [InstructorController::class, 'courseStudents'])->name('instructor.course.students');
    
    // Course Materials
    Route::post('/course/{id}/materials', [InstructorController::class, 'storeMaterial'])->name('instructor.materials.store');
    Route::delete('/materials/{id}', [InstructorController::class, 'deleteMaterial'])->name('instructor.materials.delete');
    
    // Course Assignments
    Route::post('/course/{id}/assignments', [InstructorController::class, 'storeAssignment'])->name('instructor.assignments.store');
    Route::put('/assignments/{id}', [InstructorController::class, 'updateAssignment'])->name('instructor.assignments.update');
    Route::delete('/assignments/{id}', [InstructorController::class, 'deleteAssignment'])->name('instructor.assignments.delete');
    
    // Student Progress
    Route::put('/students/{id}/progress', [InstructorController::class, 'updateStudentProgress'])->name('instructor.students.progress');
    
    // Assignment Submissions
    Route::get('/assignments/{id}/submissions', [InstructorController::class, 'assignmentSubmissions'])->name('instructor.assignments.submissions');
    Route::put('/submissions/{id}/grade', [InstructorController::class, 'gradeSubmission'])->name('instructor.submissions.grade');
});

// Admin Routes (Require Admin Privileges)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::put('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::put('/users/{id}/profile', [AdminController::class, 'updateUserProfile'])->name('admin.users.profile');
    
    // Course Management
    Route::get('/courses', [AdminController::class, 'courses'])->name('admin.courses');
    Route::get('/courses/manage', [AdminController::class, 'manageCourses'])->name('admin.courses.manage');
    Route::post('/courses', [AdminController::class, 'createCourse'])->name('admin.courses.create');
    Route::put('/courses/{id}', [AdminController::class, 'updateCourse'])->name('admin.courses.update');
    Route::delete('/courses/{id}', [AdminController::class, 'deleteCourse'])->name('admin.courses.delete');
    Route::put('/courses/{id}/status', [AdminController::class, 'updateCourseStatus'])->name('admin.courses.status');
    Route::get('/courses/export', [AdminController::class, 'exportCourses'])->name('admin.courses.export');
    
    // Financial & Refund Management
    Route::get('/financial', [AdminController::class, 'financial'])->name('admin.financial');
    Route::get('/refund', [AdminController::class, 'refund'])->name('admin.refund');
    Route::post('/refund/{id}/process', [AdminController::class, 'processRefund'])->name('admin.refund.process');
});