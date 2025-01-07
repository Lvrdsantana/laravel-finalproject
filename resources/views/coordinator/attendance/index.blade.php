@extends('layouts.coordinator')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-clipboard-check text-primary"></i> 
                    Attendance Management
                </h3>
                <div class="d-flex align-items-center">
                    <div class="search-box me-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                    <div class="stats-badge">
                        <span class="badge bg-primary">
                            <i class="fas fa-clock"></i> Pending: {{ $pendingJustifications }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if($attendances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">
                                    <i class="fas fa-user text-muted me-1"></i> Student
                                </th>
                                <th class="text-nowrap">
                                    <i class="fas fa-book text-muted me-1"></i> Course
                                </th>
                                <th class="text-nowrap">
                                    <i class="fas fa-calendar text-muted me-1"></i> Date
                                </th>
                                <th class="text-nowrap">
                                    <i class="fas fa-info-circle text-muted me-1"></i> Status
                                </th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr class="searchable-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2 bg-primary">
                                                {{ strtoupper(substr($attendance->student->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $attendance->student->user->name }}</div>
                                                <small class="text-muted">ID: {{ $attendance->student->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="course-info">
                                            <div class="fw-bold">{{ $attendance->timetable->course->name }}</div>
                                            <small class="text-muted">
                                                {{ $attendance->timetable->class->name }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-info">
                                            {{ $attendance->marked_at ? $attendance->marked_at->format('d/m/Y') : 'N/A' }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $attendance->marked_at ? $attendance->marked_at->format('H:i') : '' }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($attendance->status === 'absent')
                                            <span class="badge bg-danger-soft text-danger">
                                                <i class="fas fa-times-circle"></i> Absent
                                            </span>
                                        @else
                                            <span class="badge bg-warning-soft text-warning">
                                                <i class="fas fa-clock"></i> Late
                                            </span>
                                        @endif
                                        @if($attendance->justification)
                                            <div class="mt-1">
                                                <span class="badge bg-info-soft text-info">
                                                    <i class="fas fa-check-circle"></i>
                                                    Justified on {{ $attendance->justification->justified_at->format('d/m/Y') }}
                                                    <br>
                                                    <small>by {{ $attendance->justification->justifier->name }}</small>
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$attendance->justification)
                                            <a href="{{ route('coordinator.show-justify-absence', $attendance->id) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> Justify
                                            </a>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-check"></i> Processed
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} 
                        of {{ $attendances->total() }} absences
                    </div>
                    {{ $attendances->links() }}
                </div>
            @else
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No absences to justify at the moment.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.bg-danger-soft {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.bg-warning-soft {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.bg-info-soft {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

.badge {
    padding: 0.5em 0.8em;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.search-box .input-group {
    width: 250px;
}

.stats-badge .badge {
    font-size: 0.9rem;
}
</style>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let searchText = this.value.toLowerCase();
    document.querySelectorAll('.searchable-row').forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
});
</script>
@endsection 