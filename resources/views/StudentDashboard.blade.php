@extends('layouts.student')

@section('content')
<div class="container py-4">
    <!-- Welcome card -->
    <div class="welcome-card mb-4">
        <div class="card bg-gradient shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="welcome-content">
                            <div class="d-flex align-items-center mb-2">
                                <div class="welcome-avatar me-3">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h2 class="welcome-title mb-1">Welcome, {{ auth()->user()->name }}</h2>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        {{ now()->locale('en')->format('l d F Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="quick-stats d-flex justify-content-end gap-4">
                            <div class="stat-item text-center">
                                <div class="stat-circle bg-primary-soft mb-2">
                                    <i class="fas fa-clock text-primary"></i>
                                </div>
                                <div class="stat-value text-dark fw-bold mb-1">
                                    {{ $timetables->where('day_id', now()->dayOfWeek)->count() }}
                                </div>
                                <div class="stat-label text-muted small">Classes today</div>
                            </div>
                            <div class="stat-item text-center">
                                <div class="stat-circle bg-success-soft mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                                <div class="stat-value text-dark fw-bold mb-1">
                                    {{ auth()->user()->student->getAttendanceRate() }}%
                                </div>
                                <div class="stat-label text-muted small">Attendance rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left column -->
        <div class="col-lg-8">
            <!-- Upcoming classes -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-day text-primary me-2"></i>
                            Upcoming Classes
                        </h5>
                        <a href="{{ route('student.timetable') }}" class="btn btn-sm btn-outline-primary">
                            View all <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="upcoming-classes">
                        @forelse($timetables->where('day_id', now()->dayOfWeek)->sortBy('time_slot.start_time')->take(3) as $lesson)
                            <div class="class-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $lesson->course->name }}</h6>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-user-tie me-1"></i>
                                            {{ $lesson->teacher->user->name }}
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-door-open me-1"></i>
                                            Room {{ $lesson->room ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-dark">
                                            {{ $lesson->timeSlot->start_time }} - {{ $lesson->timeSlot->end_time }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center">
                                <img src="{{ asset('images/no-classes.svg') }}" alt="No classes" class="mb-3" style="width: 150px">
                                <p class="text-muted mb-0">No classes scheduled for today</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Attendance statistics -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Attendance Grades by Course
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @php
                            $student = auth()->user()->student;
                            $stats = $student ? $student->getAttendanceStats() : null;
                        @endphp
                        
                        @if($student && $student->class && $student->class->timetables)
                            @foreach($student->class->timetables->groupBy('course_id') as $courseId => $timetables)
                                @php
                                    $courseStats = $student->getAttendanceStats($courseId);
                                    $attendanceGrade = number_format($courseStats['attendance_grade'], 1);
                                    $status = $courseStats['is_dropped'] ? 'danger' : 
                                            ($attendanceGrade >= 14 ? 'success' : 
                                            ($attendanceGrade >= 10 ? 'warning' : 'danger'));
                                @endphp
                                <div class="col-md-6">
                                    <div class="course-stats p-3 rounded-3 border {{ $courseStats['is_dropped'] ? 'border-danger border-2' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <h6 class="mb-0">{{ $timetables->first()->course->name }}</h6>
                                                <small class="text-muted">Attendance grade</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="grade-badge bg-{{ $status }}">
                                                    {{ $attendanceGrade }}/20
                                                </span>
                                                @if($courseStats['is_dropped'])
                                                    <div class="dropped-badge mt-1">
                                                        <span class="badge bg-danger d-flex align-items-center gap-1">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            Dropped
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $status }}" 
                                                 role="progressbar" 
                                                 style="width: {{ ($attendanceGrade/20)*100 }}%" 
                                                 aria-valuenow="{{ $attendanceGrade }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="20">
                                            </div>
                                        </div>
                                        <div class="mt-2 d-flex justify-content-between">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $courseStats['total_sessions'] }} sessions
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-check-circle me-1"></i>
                                                {{ number_format($courseStats['attendance_rate'], 1) }}% attendance
                                            </small>
                                        </div>
                                        @if($courseStats['is_dropped'])
                                            <div class="alert alert-danger mt-2 mb-0 py-2 px-3">
                                                <small>
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    Your attendance is below 30%. Please contact your coordinator.
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No attendance data available at the moment.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right column -->
        <div class="col-lg-4">
            <!-- Recent notifications -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-bell text-primary me-2"></i>
                            Notifications
                        </h5>
                        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-primary">
                            View all
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="notifications-list">
                        @forelse(auth()->user()->notifications->take(5) as $notification)
                            <div class="notification-item p-3 border-bottom {{ !$notification->read_at ? 'unread' : '' }}">
                                <div class="d-flex gap-3">
                                    <div class="notification-icon">
                                        @if($notification->data['type'] === 'student_dropped')
                                            <i class="fas fa-exclamation-triangle text-danger"></i>
                                        @else
                                            <i class="fas fa-info-circle text-primary"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="mb-1">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center">
                                <i class="fas fa-bell-slash fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No notifications</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick links -->
            <div class="quick-links card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-link text-primary me-2"></i>
                        Quick Access
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.profile') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user me-2"></i>My Profile
                        </a>
                        <a href="{{ route('student.timetable') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-calendar-alt me-2"></i>Timetable
                        </a>
                        <a href="{{ route('attendance.student.dashboard', ['student' => auth()->user()->student->id]) }}" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-chart-bar me-2"></i>My Attendance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-card .card {
    background: linear-gradient(to right, #ffffff, #f8f9fa);
    border-radius: 15px;
}

.welcome-avatar {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
}

.welcome-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #2c3e50;
}

.stat-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.stat-circle i {
    font-size: 1.25rem;
}

.bg-primary-soft {
    background-color: rgba(13, 110, 253, 0.1);
}

.bg-success-soft {
    background-color: rgba(25, 135, 84, 0.1);
}

.stat-value {
    font-size: 1.25rem;
}

.stat-label {
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .quick-stats {
        justify-content: center;
        margin-top: 1.5rem;
    }
    
    .welcome-content {
        text-align: center;
    }
    
    .welcome-avatar {
        margin: 0 auto 1rem;
    }
    
    .d-flex.align-items-center {
        flex-direction: column;
    }
}
</style>
@endsection

