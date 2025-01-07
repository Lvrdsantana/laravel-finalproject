@extends('layouts.student')

@section('content')
<div class="container py-4">
    <!-- Header with filters and options -->
    <div class="header-section mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="text-primary mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>My Schedule
                </h3>
                <p class="text-muted mb-0">
                    Week from {{ now()->startOfWeek()->format('d/m/Y') }} to {{ now()->endOfWeek()->format('d/m/Y') }}
                </p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="printTimetable()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="far fa-file-pdf me-2"></i>PDF</a></li>
                            <li><a class="dropdown-item" href="#"><i class="far fa-file-excel me-2"></i>Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="far fa-calendar-plus me-2"></i>iCal</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Week navigation -->
            <div class="week-navigation mb-4 d-flex justify-content-between align-items-center">
                <button class="btn btn-link text-decoration-none">
                    <i class="fas fa-chevron-left"></i> Previous week
                </button>
                <div class="btn-group">
                    <button class="btn btn-outline-primary active">Week</button>
                    <button class="btn btn-outline-primary">Month</button>
                </div>
                <button class="btn btn-link text-decoration-none">
                    Next week <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- Timetable -->
            <div class="table-responsive">
                <table class="table timetable">
                    <thead>
                        <tr class="text-center">
                            <th class="time-column">Time</th>
                            @foreach($days as $day)
                                <th class="{{ $day->name === now()->format('l') ? 'current-day' : '' }}">
                                    <div class="day-header">
                                        <span class="day-name">{{ $day->name }}</span>
                                        <span class="day-date">{{ now()->startOfWeek()->addDays($loop->index)->format('d/m') }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($time_slots as $slot)
                            <tr>
                                <td class="time-slot">
                                    <span class="time">{{ $slot->start_time }}</span>
                                    <span class="time">{{ $slot->end_time }}</span>
                                </td>
                                @foreach($days as $day)
                                    <td class="lesson-cell">
                                        @foreach($timetables->where('day_id', $day->id)->where('time_slot_id', $slot->id) as $lesson)
                                            <div class="lesson-card" style="background-color: {{ $lesson->color }}20; border-left: 4px solid {{ $lesson->color }}">
                                                <div class="lesson-header">
                                                    <span class="course-name">{{ $lesson->course->name }}</span>
                                                    @if($lesson->course->name === 'Workshop' || $lesson->course->name === 'E-Learning')
                                                        <span class="badge bg-info">{{ $lesson->course->name }}</span>
                                                    @endif
                                                </div>
                                                <div class="lesson-info">
                                                    <span class="teacher-name">
                                                        <i class="fas fa-user-tie me-1"></i>
                                                        {{ $lesson->teacher->user->name }}
                                                    </span>
                                                    <span class="room-info">
                                                        <i class="fas fa-door-open me-1"></i>
                                                        Room {{ $lesson->room ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Legend -->
            <div class="legend mt-4">
                <h6 class="text-muted mb-3">Legend</h6>
                <div class="d-flex flex-wrap gap-3">
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: #4CAF50"></span>
                        Regular classes
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: #2196F3"></span>
                        Workshops
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: #9C27B0"></span>
                        E-Learning
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* General styles */
.timetable {
    border-collapse: separate;
    border-spacing: 0.5rem;
}

/* Day headers */
.day-header {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.day-name {
    font-weight: 600;
    color: #333;
}

.day-date {
    font-size: 0.8rem;
    color: #666;
}

.current-day {
    background-color: #e3f2fd;
    border-radius: 8px;
}

/* Time cells */
.time-column {
    width: 100px;
}

.time-slot {
    text-align: center;
    font-size: 0.9rem;
    color: #666;
}

.time {
    display: block;
}

/* Lesson cards */
.lesson-cell {
    min-width: 200px;
    height: 100px;
    padding: 0.5rem !important;
}

.lesson-card {
    height: 100%;
    padding: 0.75rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.lesson-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.lesson-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.course-name {
    font-weight: 600;
    color: #333;
}

.lesson-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    font-size: 0.85rem;
    color: #666;
}

/* Legend */
.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #666;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
}

/* Week navigation */
.week-navigation button {
    color: #666;
}

.week-navigation button:hover {
    color: #333;
}

/* Responsive */
@media (max-width: 768px) {
    .header-section .row {
        flex-direction: column;
        gap: 1rem;
    }
    
    .header-section .col-md-6:last-child {
        justify-content: flex-start;
    }
}
</style>

<script>
function printTimetable() {
    window.print();
}

// Add Bootstrap tooltips if needed
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection