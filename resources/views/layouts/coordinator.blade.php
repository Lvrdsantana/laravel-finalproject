<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Coordinator Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/coordinatorNotif.css') }}">
    <link rel="stylesheet" href="{{ asset('css/timetable.css') }}">
    <link rel="stylesheet" href="{{ asset('css/coordinator.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="{{ asset('js/timetable.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
</head>
<body>
    <style>
        :root {
    --primary-color: #4A6FA5;
    --secondary-color: #166088;
    --background-color: #f1faee;
    --card-color: #ffffff;
    --text-color: #1d3557;
    --light-color: #F8F9FA;
    --text-light: #ffffff;
    --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --success-color: #4E9F3D;
    --danger-color: #D64045;
    --warning-color: #FFB100;
    --dark-color: #1B262C;
    --border-radius: 8px;
    --box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    --transition: all 0.3s ease;
}
    </style>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">                 
                    <span><i class="fas fa-graduation-cap"></i> ADMIN PANEL</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <div class="user-profile">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <span class="user-role">Coordinator</span>
                    </div>
                </div>

                <ul class="nav-links">
                    <li class="{{ Request::routeIs('coordinators.timetable') ? 'active' : '' }}">
                        <a href="{{ route('coordinators.timetable') }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Timetable</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('coordinators.index') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-users"></i>
                            <span>User Management</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('classes.*') ? 'active' : '' }}">
                        <a href="{{ route('classes.index') }}">
                            <i class="fas fa-school"></i>
                            <span>Class Management</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('coordinator.attendance.*') ? 'active' : '' }}">
                        <a href="{{ route('coordinator.attendance.index') }}">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Attendance Management</span>
                            @if(isset($pendingJustifications) && $pendingJustifications > 0)
                                <span class="badge bg-warning">
                                    {{ $pendingJustifications }}
                                </span>
                            @endif
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('notifications.*') ? 'active' : '' }}">
                        <a href="{{ route('notifications.index') }}">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('stats.*') ? 'active' : '' }}">
                        <a href="{{ route('stats.index') }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Statistics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('coordinator.timetable.history.index') }}" class="nav-link {{ request()->routeIs('coordinator.timetable.history.*') ? 'active' : '' }}">
                            <i class="fas fa-history"></i>
                            <span>Timetable History</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-footer">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="main-content">
            <header class="main-header">
                <div class="header-left">
                    <button class="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>@yield('title', 'Coordinator Dashboard')</h1>
                </div>
                <div class="header-right">
                    <div class="dropdown me-3">
                        <button class="btn-notification" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="notification-count has-new">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            <li class="dropdown-header">
                                <i class="fas fa-bell me-2"></i>
                                Notifications
                            </li>
                            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                <li>
                                    <a class="dropdown-item {{ $notification->read_at ? '' : 'unread' }}" 
                                       href="#"
                                       onclick="event.preventDefault(); markAsRead('{{ $notification->id }}');">
                                        <div class="notification-content">
                                            <div class="notification-icon">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                            <div class="notification-text">
                                                <div class="notification-title">
                                                    Student Dropped
                                                </div>
                                                <div>
                                                    {{ $notification->data['student_name'] }} has been dropped from course {{ $notification->data['course_name'] }}
                                                </div>
                                                <div class="notification-time">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item text-muted">No notifications</span></li>
                            @endforelse
                            <li><hr class="dropdown-divider m-0"></li>
                            <li>
                                <a class="see-all" href="{{ route('notifications.index') }}">
                                    See all notifications
                                </a>
                            </li>
                        </ul>
                    </div>
                    <span>{{ Auth::user()->name }}</span>
                </div>
            </header>

            <!-- Notification messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                    <button class="close-alert"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                    <button class="close-alert"><i class="fas fa-times"></i></button>
                </div>
            @endif

            <section class="content">
                <div class="breadcrumb">
                    <a href="{{ route('coordinators.timetable') }}">Dashboard</a>
                    @yield('breadcrumb')
                </div>
                @yield('content')
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }

    // Fermer le dropdown quand on clique en dehors
    document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('.dropdown');
        const dropdownMenu = document.getElementById('notificationsDropdown');
        
        if (!dropdown.contains(event.target)) {
            const bsDropdown = bootstrap.Dropdown.getInstance(dropdownMenu);
            if (bsDropdown) {
                bsDropdown.hide();
            }
        }
    });

    // Fermeture des alertes
    document.querySelectorAll('.close-alert').forEach(button => {
        button.addEventListener('click', () => {
            button.closest('.alert').remove();
        });
    });

    // Toggle du menu latéral
    document.querySelector('.menu-toggle').addEventListener('click', () => {
        document.querySelector('.dashboard-wrapper').classList.toggle('sidebar-collapsed');
    });

    // Disparition automatique des alertes
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
    </script>
</body>
</html> 