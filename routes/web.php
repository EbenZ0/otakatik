<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InstructorController;

// TEST ROUTES
Route::get('/test-simple', function () {
    return "Simple route works!";
});

Route::get('/test-controller', [InstructorController::class, 'courses']);

// Public Routes
Route::get('/', function () {
    return view('dashboard');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Course Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/course', [CourseController::class, 'showCourse'])->name('course.show');
    Route::post('/course/register', [CourseController::class, 'register'])->name('course.register');
    Route::get('/course/{id}', [CourseController::class, 'show'])->name('course.show.detail');
    Route::delete('/course/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
    Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('my.courses');
    Route::get('/purchase-history', [CourseController::class, 'purchaseHistory'])->name('purchase.history');
    Route::put('/course-progress/{id}', [CourseController::class, 'updateProgress'])->name('course.progress.update');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::put('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/manage', [AdminController::class, 'manageCourses'])->name('courses.manage');
    Route::post('/courses/create', [AdminController::class, 'createCourse'])->name('courses.create');
    Route::get('/courses/{id}/edit', [AdminController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{id}', [AdminController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{id}', [AdminController::class, 'deleteCourse'])->name('courses.delete');
    Route::put('/courses/{id}/toggle', [AdminController::class, 'toggleCourse'])->name('courses.toggle');
    Route::put('/courses/{id}/status', [AdminController::class, 'updateCourseStatus'])->name('courses.status.update');
    Route::put('/courses/{id}/active-status', [AdminController::class, 'updateCourseActiveStatus'])->name('courses.status');
    Route::get('/courses/export', [AdminController::class, 'exportCourses'])->name('courses.export');
    
    Route::get('/financial', [AdminController::class, 'financial'])->name('financial');
    Route::get('/refund', [AdminController::class, 'refund'])->name('refund');
    Route::put('/refund/{id}/process', [AdminController::class, 'processRefund'])->name('refund.process');
});

// Instructor Routes - PASTIKAN SEMUA InstructorController::class
Route::middleware(['auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [InstructorController::class, 'courses'])->name('courses');
    Route::get('/courses/{id}', [InstructorController::class, 'showCourse'])->name('courses.show');
    Route::get('/courses/{id}/students', [InstructorController::class, 'courseStudents'])->name('courses.students');
    Route::post('/courses/{id}/materials', [InstructorController::class, 'storeMaterial'])->name('materials.store');
    Route::delete('/materials/{id}', [InstructorController::class, 'deleteMaterial'])->name('materials.delete');
    Route::post('/courses/{id}/assignments', [InstructorController::class, 'storeAssignment'])->name('assignments.store');
    Route::put('/assignments/{id}', [InstructorController::class, 'updateAssignment'])->name('assignments.update');
    Route::delete('/assignments/{id}', [InstructorController::class, 'deleteAssignment'])->name('assignments.delete');
    Route::get('/assignments/{id}/submissions', [InstructorController::class, 'assignmentSubmissions'])->name('submissions');
    Route::put('/submissions/{id}/grade', [InstructorController::class, 'gradeSubmission'])->name('submissions.grade');
    Route::put('/students/{id}/progress', [InstructorController::class, 'updateStudentProgress'])->name('students.progress');
});