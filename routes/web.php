<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\AttendanceStatsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TeacherProfileController;
use App\Http\Controllers\CoordinatorTimetableController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\CourseController;



Route::get('/', function () {
    return view('welcome');
});
// login page homepage
Route::get('/', function () {
    return view('login');
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/student/dashboard', [AuthController::class, 'studentDashboard'])->name('studentDashboard');
    Route::get('/teacher/dashboard', [AuthController::class, 'teacherDashboard'])->name('teacherdashboard');
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/coordinators-timetable', [CoordinatorController::class, 'showTimetable'])->name('coordinators.timetable');
    Route::resource('parents', ParentController::class);
    // Coordinators
    Route::resource('coordinators', CoordinatorController::class);

    // Teachers
    Route::resource('teachers', TeacherController::class);

    // Students
    Route::resource('students', StudentController::class);
    Route::resource('classes', ClassController::class);

    
    // route timetable VRAI ROUTE
    Route::get('/coordinators-timetable', [TimetableController::class, 'index'])->name('coordinators.timetable');
    Route::post('/coordinators-timetable', [TimetableController::class, 'store'])->name('coordinators.timetable.store');
    Route::post('/coordinator/timetable/update/{id}', [TimetableController::class, 'update'])->name('coordinator.timetable.update');
    Route::delete('/coordinator/timetable/delete/{id}', [TimetableController::class, 'delete'])->name('coordinator.timetable.delete');

    // Routes pour les étudiants
    Route::prefix('student')->group(function () {
        Route::get('/timetable', [StudentDashboardController::class, 'timetable'])->name('student.timetable');
        Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('student.profile');
    });

    // Routes pour les enseignants
    Route::prefix('teacher')->group(function () {
        Route::get('/', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
        Route::get('/dashboard', [AuthController::class, 'teacherDashboard'])->name('teacher.dashboard');
        Route::get('/timetable', [TeacherDashboardController::class, 'timetable'])->name('teacher.timetable');
        Route::get('/profile', [TeacherDashboardController::class, 'profile'])->name('teacher.profile');
        Route::get('/attendance/{timetable}', [TeacherDashboardController::class, 'showAttendance'])->name('teacher.attendance');
        Route::post('/attendance/{timetable}', [TeacherDashboardController::class, 'storeAttendance'])->name('teacher.attendance.store');
        Route::get('/notifications', [TeacherDashboardController::class, 'notifications'])
            ->name('teacher.notifications');
    });

    
    // Routes pour les notifications
    Route::get('/notifications', function () {
        $notifications = auth()->user()->notifications()->paginate(10);
        return view('notifications.index', compact('notifications'));
    })->name('notifications.index');

    Route::post('/notifications/{notification}/mark-as-read', function ($notification) {
        auth()->user()->notifications()->findOrFail($notification)->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.mark-as-read');

    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    })->name('notifications.mark-all-read');
});

// Routes pour l'emploi du temps sans middleware
Route::group([], function () {
    Route::post('/coordinators/timetable', [TimetableController::class, 'store'])->name('coordinators.timetable.store');
    Route::put('/coordinators/timetable/{id}', [TimetableController::class, 'update'])->name('coordinators.timetable.update');
    Route::delete('/coordinators/timetable/{id}', [TimetableController::class, 'destroy'])->name('coordinators.timetable.destroy');
    Route::get('/coordinators/timetable/{id}/edit', [TimetableController::class, 'edit'])->name('coordinators.timetable.edit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/coordinator/attendance/{timetable}', [CoordinatorController::class, 'showAttendance'])
        ->name('coordinator.attendance');
    Route::post('/coordinator/attendance/{timetable}', [CoordinatorController::class, 'storeAttendance'])
        ->name('coordinator.attendance.store');
});

// Routes pour les statistiques de présence
Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/student/{student}', [AttendanceStatsController::class, 'studentDashboard'])
        ->name('attendance.student.dashboard');
    Route::get('/attendance/class/{class}', [AttendanceStatsController::class, 'classDashboard'])
        ->name('attendance.class.dashboard');
    Route::get('/attendance/dropped', [AttendanceStatsController::class, 'droppedStudents'])
        ->name('attendance.dropped');
    Route::get('/attendance/export', [AttendanceStatsController::class, 'exportStats'])
        ->name('attendance.export');
});

// Ajouter dans le groupe middleware auth
Route::prefix('stats')->name('stats.')->middleware('auth')->group(function () {
    Route::get('/', [StatsController::class, 'index'])->name('index');
    Route::get('/student-attendance', [StatsController::class, 'studentAttendance'])->name('student-attendance');
    Route::get('/class-attendance', [StatsController::class, 'classAttendance'])->name('class-attendance');
    Route::get('/course-volume', [StatsController::class, 'courseVolume'])->name('course-volume');
    Route::get('/data/student-attendance', [StatsController::class, 'getStudentAttendanceData'])->name('data.student-attendance');
    Route::get('/data/class-attendance', [StatsController::class, 'getClassAttendanceData'])->name('data.class-attendance');
    Route::get('/data/course-volume', [StatsController::class, 'getCourseVolumeData'])->name('data.course-volume');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/{attendance}/justify', [CoordinatorController::class, 'showJustifyAbsence'])
        ->name('coordinator.show-justify-absence');
    Route::post('/attendance/{attendance}/justify', [CoordinatorController::class, 'justifyAbsence'])
        ->name('coordinator.justify-absence');
    Route::get('/attendance', [CoordinatorController::class, 'attendanceIndex'])
        ->name('coordinator.attendance.index');
});

Route::middleware(['auth',])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('studentDashboard');
    Route::get('/student/profile', [StudentDashboardController::class, 'profile'])->name('student.profile');
    Route::put('/student/profile/update', [StudentController::class, 'updateProfile'])->name('student.profile.update');
});

Route::get('/users/search', [UserController::class, 'search'])->name('users.search');

// Routes pour le profil enseignant
Route::middleware(['auth',])->group(function () {
    Route::put('/teacher/profile/update', [TeacherProfileController::class, 'update'])->name('teacher.profile.update');
    Route::post('/teacher/profile/avatar', [TeacherProfileController::class, 'updateAvatar'])->name('teacher.profile.avatar');
});

Route::middleware(['auth', ])->group(function () {
    // Routes pour l'historique des emplois du temps
    Route::get('/coordinator/timetable/history', [CoordinatorTimetableController::class, 'historyIndex'])
         ->name('coordinator.timetable.history.index');
    Route::get('/coordinator/timetable/{timetable}/history', [CoordinatorTimetableController::class, 'history'])
         ->name('coordinator.timetable.history');
    Route::get('/coordinator/timetable/history/filter', [CoordinatorTimetableController::class, 'filterHistory'])
         ->name('coordinator.timetable.history.filter');
    Route::get('/coordinator/timetable/history/export', [CoordinatorTimetableController::class, 'exportHistory'])
         ->name('coordinator.timetable.history.export');
});

Route::middleware(['auth', ])->group(function () {
    Route::get('/parent/dashboard', [ParentDashboardController::class, 'index'])->name('parent.dashboard');
});

// Routes pour l'enseignant
Route::middleware(['auth', ])->group(function () {
    Route::get('/teacher/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::get('/teacher/timetable', [TeacherDashboardController::class, 'timetable'])->name('teacher.timetable');
    Route::get('/teacher/profile', [TeacherDashboardController::class, 'profile'])->name('teacher.profile');
    Route::get('/teacher/notifications', [TeacherDashboardController::class, 'notifications'])->name('teacher.notifications');
    
    // Routes pour la gestion des présences
    Route::get('/attendance/{timetable}', [TeacherDashboardController::class, 'showAttendance'])->name('attendance.show');
    Route::post('/attendance/{timetable}', [TeacherDashboardController::class, 'storeAttendance'])->name('attendance.store');
});

Route::middleware(['auth',])->group(function () {
    Route::resource('classes', ClassController::class);
});

// Course routes
Route::middleware(['auth',])->group(function () {
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
});
