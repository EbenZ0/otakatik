<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Home Route
Route::get('/', function () {
    return view('dashboard');
});

// Public Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/course', [CourseController::class, 'showCourse'])->name('course');

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
    Route::post('/course/register', [CourseController::class, 'register'])->name('course.register');
    Route::get('/course/{id}', [CourseController::class, 'show'])->name('course.show');
    Route::delete('/course/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
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
    Route::put('/courses/{id}/status', [AdminController::class, 'updateCourseStatus'])->name('admin.courses.status');
    Route::get('/courses/export', [AdminController::class, 'exportCourses'])->name('admin.courses.export');
    
    // Financial & Refund Management
    Route::get('/financial', [AdminController::class, 'financial'])->name('admin.financial');
    Route::get('/refund', [AdminController::class, 'refund'])->name('admin.refund');
    Route::post('/refund/{id}/process', [AdminController::class, 'processRefund'])->name('admin.refund.process');
});