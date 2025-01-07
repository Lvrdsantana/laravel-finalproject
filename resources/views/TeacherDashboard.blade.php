<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - IFRAN School</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/teacher-dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="{{ route('teacher.dashboard') }}" class="active">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacher.timetable') }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Schedule</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacher.profile') }}">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacher.notifications') }}">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="main-content">
            <!-- Header -->
            <div class="main-header">
                <div>
                    <h1>Welcome, {{ Auth::user()->name }}</h1>
                    <p class="text-muted">{{ now()->format('l d F Y') }}</p>
                </div>
                <div class="user-profile">
                    <span>{{ Auth::user()->name }}</span>
                    <img src="{{ asset('images/avatar.png') }}" alt="Avatar" class="avatar">
                </div>
            </div>

            <!-- Quick stats -->
            <div class="quick-stats">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-info">
                        <h4>Total Students</h4>
                        <p>{{ $totalStudents }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-book"></i>
                    <div class="stat-info">
                        <h4>Courses</h4>
                        <p>{{ $totalCourses }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <div class="stat-info">
                        <h4>Average Attendance Rate</h4>
                        <p>{{ number_format($averageAttendance, 1) }}%</p>
                    </div>
                </div>
            </div>

            <!-- Main grid -->
            <div class="main-grid">
                <!-- Today's schedule -->
                <div class="card today-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-calendar-day"></i>
                            Today's Classes
                            <span class="date-subtitle">{{ now()->format('d F Y') }}</span>
                        </h3>
                    </div>
                    <div class="today-schedule">
                        @forelse($todayClasses as $lesson)
                            <div class="lesson">
                                <div class="lesson-time">
                                    <i class="far fa-clock"></i>
                                    {{ $lesson->timeSlot->start_time }} - {{ $lesson->timeSlot->end_time }}
                                </div>
                                <div class="lesson-content" style="border-left: 4px solid {{ $lesson->course->color ?? '#4ea1ff' }}">
                                    <div class="lesson-main-info">
                                        <h4 class="course-name">{{ $lesson->course->name }}</h4>
                                        <div class="class-info">
                                            <i class="fas fa-users"></i>
                                            {{ $lesson->class->name }}
                                        </div>
                                    </div>
                                    <div class="lesson-actions">
                                        <a href="{{ route('attendance.show', $lesson->id) }}" class="attendance-btn">
                                            <i class="fas fa-clipboard-list"></i>
                                            Take Attendance
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="no-lessons">
                                <div class="no-lessons-content">
                                    <i class="fas fa-coffee"></i>
                                    <p>No classes today</p>
                                    <span class="free-day">Enjoy your day!</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Attendance statistics -->
                <div class="card">
                    <h3><i class="fas fa-chart-bar"></i> Attendance Rate by Course</h3>
                    <div class="attendance-stats">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>

                <!-- Upcoming classes -->
                <div class="card">
                    <h3><i class="fas fa-calendar-week"></i> Upcoming Classes</h3>
                    <div class="upcoming-classes">
                        @forelse($upcomingLessons as $lesson)
                            <div class="upcoming-lesson">
                                <div class="lesson-date">
                                    <span class="day">{{ $lesson->date->format('d') }}</span>
                                    <span class="month">{{ $lesson->date->format('M') }}</span>
                                </div>
                                <div class="lesson-info">
                                    <h4>{{ $lesson->course->name }}</h4>
                                    <p>{{ $lesson->timeSlot->start_time }} - {{ $lesson->class->name }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No upcoming classes</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Attendance chart configuration
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($attendanceStats)) !!},
                datasets: [{
                    label: 'Attendance Rate (%)',
                    data: {!! json_encode(array_values($attendanceStats)) !!},
                    backgroundColor: 'rgba(99, 102, 241, 0.5)',
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>
