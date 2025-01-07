<div class="dropdown">
    <button class="btn btn-link dropdown-toggle" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
        @endif
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
        @forelse(auth()->user()->notifications as $notification)
            <li>
                <a class="dropdown-item {{ $notification->read_at ? 'text-muted' : 'fw-bold' }}" 
                   href="#"
                   onclick="event.preventDefault(); markAsRead('{{ $notification->id }}');">
                    @if(isset($notification->data['type']) && $notification->data['type'] === 'student_dropped')
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                        <div>
                            Vous avez été droppé du cours {{ $notification->data['course_name'] ?? 'N/A' }}
                            <small class="d-block text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                    @endif
                </a>
            </li>
        @empty
            <li><span class="dropdown-item">Aucune notification</span></li>
        @endforelse
        
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                Voir toutes les notifications
            </a>
        </li>
    </ul>
</div>

@push('scripts')
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
</script>
@endpush 