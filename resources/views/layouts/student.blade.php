<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/student-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/timetable.css') }}">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --sidebar-width: 250px;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            position: fixed;
            height: 100vh;
            transition: all 0.3s ease;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
        }

        .sidebar-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav li {
            margin: 0.5rem 0;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar-nav a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav li.active a {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid white;
        }

        .sidebar-nav i {
            width: 20px;
            margin-right: 10px;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
        }

        .main-header {
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .user-info {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        /* Dropdown Styles */
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fc;
        }

        /* Notification Styles */
        .dropdown-menu {
            min-width: 300px;
        }

        .notification-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e3e6f0;
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background-color: #f8f9fc;
        }

        .notification-item.unread {
            background-color: rgba(78, 115, 223, 0.05);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        /* Button Styles */
        .btn-link {
            color: var(--secondary-color);
            text-decoration: none;
            padding: 0.375rem;
        }

        .btn-link:hover {
            color: var(--primary-color);
        }

        /* Badge Styles */
        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
        }

        /* User Avatar Styles */
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Notification Styles */
        .notification-btn {
            position: relative;
            padding: 0.5rem;
            transition: all 0.2s ease;
        }

        .notification-btn:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 50%;
        }

        .notification-dropdown {
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 50%;
        }

        .notification-content {
            flex: 1;
        }

        .notification-content p {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .notification-content small {
            font-size: 0.75rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .notification-dropdown {
                width: 280px;
            }

            .main-header {
                padding: 0.75rem 1rem;
            }
        }

        /* Scrollbar Styles */
        .notification-dropdown::-webkit-scrollbar {
            width: 6px;
        }

        .notification-dropdown::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .notification-dropdown::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .notification-dropdown::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Student Menu</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="{{ request()->routeIs('studentDashboard') ? 'active' : '' }}">
                        <a href="{{ route('studentDashboard') }}">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('student.timetable') ? 'active' : '' }}">
                        <a href="{{ route('student.timetable') }}">
                            <i class="fas fa-calendar-alt"></i> Schedule
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                        <a href="{{ route('notifications.index') }}">
                            <i class="fas fa-bell"></i> Notifications
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('student.profile') ? 'active' : '' }}">
                        <a href="{{ route('student.profile') }}">
                            <i class="fas fa-user"></i> My Profile
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-link d-md-none" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="user-info">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-3">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="me-3 d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <!-- Notifications menu -->
                            <div class="dropdown me-3">
                                <button class="btn btn-link text-dark position-relative notification-btn" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown">
                                    @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
                                        <li>
                                            <a class="dropdown-item {{ !$notification->read_at ? 'unread' : '' }}" 
                                               href="#"
                                               onclick="event.preventDefault(); markAsRead('{{ $notification->id }}');">
                                                <div class="d-flex align-items-center">
                                                    <div class="notification-icon me-3">
                                                        @if($notification->data['type'] === 'student_dropped')
                                                            <i class="fas fa-exclamation-triangle text-danger"></i>
                                                        @else
                                                            <i class="fas fa-info-circle text-primary"></i>
                                                        @endif
                                                    </div>
                                                    <div class="notification-content">
                                                        <p class="mb-1">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li><span class="dropdown-item text-center py-3">No notifications</span></li>
                                    @endforelse
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                                            View all notifications
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link text-dark">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span class="d-none d-md-inline ms-1">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                @yield('content')
            </div>
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

    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });
    </script>
    @stack('scripts')
</body>
</html>