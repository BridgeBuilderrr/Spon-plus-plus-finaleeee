<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Forgot & Reset Password Routes (OTP Flow)
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendOtp'])->name('password.sendOtp');
    Route::get('/verify-otp', [PasswordResetController::class, 'showVerifyForm'])->name('password.verifyOtp');
    Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.verifyOtp.post');
    Route::post('/resend-otp', [PasswordResetController::class, 'resendOtp'])->name('password.resendOtp');
    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.resetForm');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/my-courses', [\App\Http\Controllers\ClassroomController::class, 'index'])->name('courses.index');
    Route::post('/classes/create', [\App\Http\Controllers\ClassroomController::class, 'store'])->name('courses.store');
    Route::post('/classes/join', [\App\Http\Controllers\ClassroomController::class, 'join'])->name('courses.join');

    Route::prefix('classes/{classroom}')->group(function () {
        Route::get('/', [\App\Http\Controllers\ClassroomController::class, 'show'])->name('courses.show');
        Route::get('/students', [\App\Http\Controllers\ClassroomController::class, 'students'])->name('courses.students');
        Route::post('/update', [\App\Http\Controllers\ClassroomController::class, 'update'])->name('courses.update');
        Route::post('/update-banner', [\App\Http\Controllers\ClassroomController::class, 'updateBanner'])->name('courses.update_banner');
        Route::post('/delete-banner', [\App\Http\Controllers\ClassroomController::class, 'deleteBanner'])->name('courses.delete_banner');
        Route::delete('/destroy', [\App\Http\Controllers\ClassroomController::class, 'destroy'])->name('courses.destroy');
        Route::post('/star', [\App\Http\Controllers\ClassroomController::class, 'toggleStar'])->name('courses.star');
        Route::post('/kick/{user}', [\App\Http\Controllers\ClassroomController::class, 'kick'])->name('courses.kick');
        Route::post('/exit', [\App\Http\Controllers\ClassroomController::class, 'exit'])->name('courses.exit');

        Route::post('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');

        Route::post('/materials', [\App\Http\Controllers\MaterialController::class, 'store'])->name('materials.store');
        Route::post('/materials/{material}/update', [\App\Http\Controllers\MaterialController::class, 'update'])->name('materials.update');
        Route::delete('/materials/{material}', [\App\Http\Controllers\MaterialController::class, 'destroy'])->name('materials.destroy');

        Route::get('/assignments/create', [\App\Http\Controllers\AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [\App\Http\Controllers\AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/assignments/{assignment}/edit', [\App\Http\Controllers\AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::post('/assignments/{assignment}/update', [\App\Http\Controllers\AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{assignment}', [\App\Http\Controllers\AssignmentController::class, 'destroy'])->name('assignments.destroy');

        Route::post('/assignments/{assignment}/submit', [\App\Http\Controllers\SubmissionController::class, 'store'])->name('submissions.store');
        Route::post('/assignments/{assignment}/submissions/{submission}/update', [\App\Http\Controllers\SubmissionController::class, 'update'])->name('submissions.update');
        Route::delete('/assignments/{assignment}/submissions/{submission}', [\App\Http\Controllers\SubmissionController::class, 'destroy'])->name('submissions.destroy');
        Route::post('/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
        Route::post('/comments/{comment}/update', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
    });

    Route::post('/upload', [\App\Http\Controllers\MaterialController::class, 'upload'])->name('upload');
    Route::get('/download-file', [\App\Http\Controllers\DownloadController::class, 'download'])->name('download.file');
    Route::get('/download/{path}', [\App\Http\Controllers\ClassroomController::class, 'download'])->name('download')->where('path', '.*');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/banner', [\App\Http\Controllers\ProfileController::class, 'updateBanner'])->name('profile.banner');
    Route::post('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'search'])->name('search');
    Route::post('/notifications/read-all', function () {
        \Illuminate\Support\Facades\Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read-all');

    Route::get('/notifications/unread', function () {
        if (!\Illuminate\Support\Facades\Auth::check()) return response()->json(['error' => 'Unauthorized'], 401);
        $user = \Illuminate\Support\Facades\Auth::user();
        $unreadCount = $user->unreadNotifications->count();
        $notifications = $user->notifications()->latest()->take(20)->get()->map(function($n) {
            return [
                'id' => $n->id,
                'unread' => $n->unread(),
                'classroom_id' => $n->data['classroom_id'] ?? '',
                'classroom_name' => $n->data['classroom_name'] ?? '',
                'message' => $n->data['message'] ?? '',
                'type' => $n->data['type'] ?? '',
                'icon' => match($n->data['type'] ?? '') {
                    'assignment' => 'file-text',
                    'material' => 'book-open',
                    'announcement' => 'megaphone',
                    default => 'bell'
                },
                'created_at_human' => $n->created_at->diffForHumans()
            ];
        });
        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    })->name('notifications.unread');
});
