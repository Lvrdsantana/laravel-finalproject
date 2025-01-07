@extends('layouts.teacher')

@section('title', 'Notifications')
@section('header', 'Notifications')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Notifications
                    </h5>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check-double me-1"></i>
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($notifications as $notification)
                        @if(isset($notification->data) && is_array($notification->data))
                            <div class="notification-item p-3 mb-3 rounded {{ $notification->read_at ? 'bg-light' : 'border-start border-4 border-primary' }}">
                                @if($notification->data['type'] === 'student_dropped')
                                    <div class="d-flex align-items-start">
                                        <div class="notification-icon me-3">
                                            <i class="fas fa-exclamation-triangle text-danger fa-lg"></i>
                                        </div>
                                        <div class="notification-content flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Alert: Student Dropped</h6>
                                                @unless($notification->read_at)
                                                    <span class="badge bg-primary">New</span>
                                                @endunless
                                            </div>
                                            <p class="mb-1">
                                                Student <strong>{{ $notification->data['student_name'] }}</strong> 
                                                has been dropped from course <strong>{{ $notification->data['course_name'] }}</strong>
                                            </p>
                                            <p class="mb-2">
                                                Attendance rate: <strong>{{ number_format($notification->data['attendance_rate'], 1) }}%</strong>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <p class="mb-0">You have no notifications</p>
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.notification-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.notification-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection