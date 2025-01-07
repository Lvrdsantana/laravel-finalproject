<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <link rel="stylesheet" href="{{ asset('css/coordinatorNotif.css') }}">
</head>
<body>

@extends(auth()->user()->role === 'coordinators' ? 'layouts.coordinator' : (auth()->user()->role === 'teachers' ? 'layouts.teacher' : 'layouts.student'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bell text-primary me-2"></i>
                        Notifications
                    </h5>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-check-double me-2"></i>
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>

                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        <div class="notification-item p-4 border-bottom {{ !$notification->read_at ? 'bg-light border-start border-4 border-primary' : '' }}">
                            <div class="d-flex">
                                <div class="notification-icon me-3">
                                    @if(isset($notification->data['type']) && $notification->data['type'] === 'student_dropped')
                                        <div class="icon-circle bg-danger text-white">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                    @else
                                        <div class="icon-circle bg-primary text-white">
                                            <i class="fas fa-info"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="notification-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 fw-bold">
                                            @if(isset($notification->data['type']) && $notification->data['type'] === 'student_dropped')
                                                @if(auth()->user()->role === 'coordinators')
                                                    Alert: Student Dropped
                                                @elseif(auth()->user()->role === 'teachers')
                                                    Alert: Student Dropped from your Course
                                                @else
                                                    Alert: Course Dropped
                                                @endif
                                            @else
                                                Notification
                                            @endif
                                        </h6>
                                        @unless($notification->read_at)
                                            <span class="badge bg-primary">New</span>
                                        @endunless
                                    </div>
                                    <p class="mb-2">
                                        @if(isset($notification->data['type']) && $notification->data['type'] === 'student_dropped')
                                            @if(auth()->user()->role === 'coordinators' || auth()->user()->role === 'teachers')
                                                Student <strong>{{ $notification->data['student_name'] ?? 'N/A' }}</strong> 
                                                has been dropped from course <strong>{{ $notification->data['course_name'] ?? 'N/A' }}</strong>
                                            @else
                                                You have been dropped from course <strong>{{ $notification->data['course_name'] ?? 'N/A' }}</strong>
                                            @endif
                                            @if(isset($notification->data['attendance_rate']))
                                                <br>
                                                Attendance rate: <strong>{{ number_format($notification->data['attendance_rate'], 1) }}%</strong>
                                            @endif
                                            @if(auth()->user()->role === 'teachers' && isset($notification->data['class_name']))
                                                <br>
                                                Class: <strong>{{ $notification->data['class_name'] }}</strong>
                                            @endif
                                        @else
                                            {{ $notification->data['message'] ?? 'Notification content not available' }}
                                        @endif
                                    </p>
                                    <div class="notification-meta d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                        <div>
                                            @if(!$notification->read_at)
                                                <button class="btn btn-sm btn-link text-primary p-0 me-3" 
                                                        onclick="markAsRead('{{ $notification->id }}')">
                                                    Mark as read
                                                </button>
                                            @endif
                                            @if((auth()->user()->role === 'coordinators' || auth()->user()->role === 'teachers') && isset($notification->data['student_id']))
                                                <a href="{{ route('attendance.student.dashboard', ['student' => $notification->data['student_id']]) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    View details
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-bell-slash fa-3x text-muted"></i>
                            </div>
                            <h6 class="text-muted">No notifications</h6>
                            <p class="text-muted small mb-0">You haven't received any notifications yet</p>
                        </div>
                    @endforelse

                    @if($notifications->count() > 0)
                        <div class="p-3">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.notification-content {
    min-width: 0;
}

.notification-meta {
    font-size: 0.875rem;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.border-primary {
    border-color: #0d6efd !important;
}

.pagination {
    margin-bottom: 0;
    justify-content: center;
}
</style>

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            window.location.reload();
        }
    });
}
</script>
@endpush
@endsection 