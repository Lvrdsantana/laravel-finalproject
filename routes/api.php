<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TeacherProfileController;
use App\Http\Controllers\API\TimetableController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\StatsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Routes pour les enseignants
    Route::middleware('ability:teacher')->group(function () {
        Route::get('/teacher/profile', [TeacherProfileController::class, 'show']);
        Route::put('/teacher/profile', [TeacherProfileController::class, 'update']);
        Route::post('/teacher/profile/avatar', [TeacherProfileController::class, 'updateAvatar']);
        
        // Gestion des présences pour les enseignants
        Route::get('/timetables/{timetable}/attendance', [AttendanceController::class, 'index']);
        Route::post('/timetables/{timetable}/attendance', [AttendanceController::class, 'store']);

        // Statistiques pour les enseignants
        Route::get('/stats/teacher', [StatsController::class, 'teacherStats']);
    });

    // Routes pour l'emploi du temps
    Route::get('/timetables', [TimetableController::class, 'index']);
    
    // Routes pour les coordinateurs uniquement
    Route::middleware('ability:coordinator')->group(function () {
        Route::post('/timetables', [TimetableController::class, 'store']);
        Route::put('/timetables/{timetable}', [TimetableController::class, 'update']);
        Route::delete('/timetables/{timetable}', [TimetableController::class, 'destroy']);
        
        // Justification des absences
        Route::post('/attendance/{presence}/justify', [AttendanceController::class, 'justify']);

        // Statistiques pour les coordinateurs
        Route::get('/stats/student/{student_id}', [StatsController::class, 'studentStats']);
        Route::get('/stats/class/{class_id}', [StatsController::class, 'classStats']);
        Route::get('/stats/teacher/{teacher_id}', [StatsController::class, 'teacherStats']);
    });

    // Routes pour les étudiants
    Route::middleware('ability:student')->group(function () {
        Route::get('/student/attendance', [AttendanceController::class, 'studentAttendance']);
        // Statistiques pour les étudiants
        Route::get('/stats/student', [StatsController::class, 'studentStats']);
    });

    // Routes pour les notifications (accessibles à tous les utilisateurs authentifiés)
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::delete('/', [NotificationController::class, 'clearAll']);
    });
});
