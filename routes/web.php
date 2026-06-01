<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/my-courses', [\App\Http\Controllers\ClassroomController::class, 'index'])->name('courses.index');
    Route::post('/classes/create', [\App\Http\Controllers\ClassroomController::class, 'store'])->name('courses.store');
    Route::post('/classes/join', [\App\Http\Controllers\ClassroomController::class, 'join'])->name('courses.join');
    
    Route::prefix('classes/{classroom}')->group(function() {
        Route::get('/', [\App\Http\Controllers\ClassroomController::class, 'show'])->name('courses.show');
        Route::get('/people', [\App\Http\Controllers\ClassroomController::class, 'people'])->name('courses.people');
        Route::post('/update', [\App\Http\Controllers\ClassroomController::class, 'update'])->name('courses.update');
        Route::post('/update-banner', [\App\Http\Controllers\ClassroomController::class, 'updateBanner'])->name('courses.update_banner');
        Route::post('/delete-banner', [\App\Http\Controllers\ClassroomController::class, 'deleteBanner'])->name('courses.delete_banner');
        Route::delete('/destroy', [\App\Http\Controllers\ClassroomController::class, 'destroy'])->name('courses.destroy');
        Route::post('/star', [\App\Http\Controllers\ClassroomController::class, 'toggleStar'])->name('courses.star');
        Route::post('/kick/{user}', [\App\Http\Controllers\ClassroomController::class, 'kick'])->name('courses.kick');
        Route::post('/exit', [\App\Http\Controllers\ClassroomController::class, 'exit'])->name('courses.exit');

        Route::post('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
        Route::post('/materials', [\App\Http\Controllers\MaterialController::class, 'store'])->name('materials.store');
        Route::post('/assignments', [\App\Http\Controllers\AssignmentController::class, 'store'])->name('assignments.store');
        Route::post('/assignments/{assignment}/submit', [\App\Http\Controllers\SubmissionController::class, 'store'])->name('submissions.store');
        Route::post('/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
        Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
    });

    Route::post('/upload', [\App\Http\Controllers\MaterialController::class, 'upload'])->name('upload');
    
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/banner', [\App\Http\Controllers\ProfileController::class, 'updateBanner'])->name('profile.banner');
});
