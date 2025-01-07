@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Attendance Statistics - {{ $student->user->name }}</h1>

    <!-- Filters -->
    <div class="filters mb-4">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="course_id">Course</label>
                <select name="course_id" id="course_id" class="form-control">
                    <option value="">All courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected($course->id == request('course_id'))>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="period">Period</label>
                <select name="period" id="period" class="form-control">
                    <option value="week" @selected($period == 'week')>This week</option>
                    <option value="month" @selected($period == 'month')>This month</option>
                    <option value="semester" @selected($period == 'semester')>This semester</option>
                    <option value="year" @selected($period == 'year')>This year</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary mt-4">Filter</button>
            </div>
        </form>
    </div>

    <!-- General Statistics -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>General Statistics</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h4>Attendance Rate</h4>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $stats['attendance_rate'] }}%"
                             aria-valuenow="{{ $stats['attendance_rate'] }}" 
                             aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($stats['attendance_rate'], 1) }}%
                        </div>
                    </div>
                </div>
                @if($stats['attendance_grade'])
                <div class="col-md-4">
                    <h4>Attendance Grade</h4>
                    <div class="grade-display">
                        {{ number_format($stats['attendance_grade'], 1) }}/20
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if($stats['is_dropped'])
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        Warning: You have been dropped from this course due to insufficient attendance.
    </div>
    @endif
</div>

<style>
.grade-display {
    font-size: 2em;
    font-weight: bold;
    color: #2c3e50;
    text-align: center;
    padding: 10px;
    border-radius: 5px;
    background: #f8f9fa;
}

.progress {
    height: 25px;
}

.progress-bar {
    font-size: 0.9em;
    line-height: 25px;
}
</style>
@endsection 