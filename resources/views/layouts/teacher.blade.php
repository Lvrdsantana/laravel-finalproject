<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Teacher Dashboard')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom styles -->
    <link rel="stylesheet" href="{{ asset('css/teacher-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/timetable.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <link rel="stylesheet" href="{{ asset('css/teacher-profile.css') }}">
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
                        <a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacher.timetable') }}" class="{{ request()->routeIs('teacher.timetable') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i> <span>Schedule</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacher.profile') }}" class="{{ request()->routeIs('teacher.profile') ? 'active' : '' }}">
                            <i class="fas fa-user"></i> <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacher.notifications') }}" class="{{ request()->routeIs('teacher.notifications') ? 'active' : '' }}">
                            <i class="fas fa-bell"></i> <span>Notifications</span>
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
                                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="main-content">
            <header class="main-header">
                <h1>@yield('header', 'Dashboard')</h1>
                <div class="user-info">
                    <div class="dropdown">
                        <button class="btn-notification" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="notification-count">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                <li>
                                    <a class="dropdown-item {{ $notification->read_at ? '' : 'unread' }}" 
                                       href="#"
                                       onclick="event.preventDefault(); markAsRead('{{ $notification->id }}');">
                                        <div class="notification-content">
                                            @if(isset($notification->data['type']))
                                                @if($notification->data['type'] === 'student_dropped')
                                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                                    <div class="notification-text">
                                                        <p>Student {{ $notification->data['student_name'] }} has been dropped from course {{ $notification->data['course_name'] }}</p>
                                                        <small class="text-muted">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <i class="fas fa-info-circle text-primary"></i>
                                                    <div class="notification-text">
                                                        <p>{{ $notification->data['message'] ?? 'New notification' }}</p>
                                                        <small class="text-muted">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                @endif
                                            @else
                                                <i class="fas fa-info-circle text-primary"></i>
                                                <div class="notification-text">
                                                    <p>New notification</p>
                                                    <small class="text-muted">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item">No notifications</span></li>
                            @endforelse
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center see-all" href="{{ route('teacher.notifications') }}">
                                    See all notifications
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="user-profile">
                        <span>{{ Auth::user()->name }}</span>
                        <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar" class="avatar">
                    </div>
                </div>
            </header>

            @yield('content')

            <!-- Notification messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show notification-toast" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show notification-toast" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </main>
    </div>

    <!-- Scripts -->
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
    </script>
</body>
</html>