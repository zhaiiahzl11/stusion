<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Counselor\CounselorController;
use App\Http\Controllers\Student\StudentController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'storeRegister']);

Route::prefix('admin')->name('admin.')->group(function() {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{role}/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{role}/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/schedule', [AdminController::class, 'schedule'])->name('schedule');
    Route::post('/schedule/assign', [AdminController::class, 'assignCounselor'])->name('schedule.assign');
    Route::post('/schedule/availability/{id}/approve', [AdminController::class, 'approveAvailability'])->name('schedule.approve');
    Route::post('/schedule/availability/{id}/reject', [AdminController::class, 'rejectAvailability'])->name('schedule.reject');
    Route::post('/schedule/walkin/{id}/approve', [AdminController::class, 'approveWalkIn'])->name('schedule.walkin.approve');
    Route::post('/schedule/blocked-time', [AdminController::class, 'storeBlockedTime'])->name('schedule.block.store');
    Route::delete('/schedule/blocked-time/{id}', [AdminController::class, 'deleteBlockedTime'])->name('schedule.block.destroy');
    Route::get('/reports/sessions', [AdminController::class, 'generateReport'])->name('reports.sessions');
});

Route::prefix('counselor')->name('counselor.')->group(function() {
    Route::get('/dashboard', [CounselorController::class, 'dashboard'])->name('dashboard');
    Route::get('/sessions', [CounselorController::class, 'sessions'])->name('sessions');
    Route::post('/sessions/{id}/complete', [CounselorController::class, 'completeSession'])->name('sessions.complete');
    Route::get('/availability', [CounselorController::class, 'availability'])->name('availability');
    Route::post('/availability', [CounselorController::class, 'storeAvailability'])->name('availability.store');
    Route::get('/walk-in', [CounselorController::class, 'walkIn'])->name('walkin');
    Route::post('/walk-in', [CounselorController::class, 'storeWalkIn'])->name('walkin.store');
    Route::get('/reports/sessions', [CounselorController::class, 'generateReport'])->name('reports.sessions');
});

Route::prefix('student')->name('student.')->group(function() {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/request', [StudentController::class, 'requestSession'])->name('request');
    Route::post('/request', [StudentController::class, 'storeRequest'])->name('request.store');
    Route::get('/sessions', [StudentController::class, 'sessions'])->name('sessions');
});
