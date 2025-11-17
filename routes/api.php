<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstructorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Endpoints for Instructor (Protected)
Route::middleware(['auth'])->group(function () {
    // Get assignment details for editing
    Route::get('/assignments/{id}', [InstructorController::class, 'getAssignmentJson'])->name('assignments.json');
});