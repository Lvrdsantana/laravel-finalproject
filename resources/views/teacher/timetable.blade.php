@extends('layouts.teacher')

@section('content')
<div class="timetable-container">
    <div class="timetable-header">
        <h3>
            <i class="fas fa-calendar-alt"></i>
            My Timetable
        </h3>
    </div>
    <div class="table-responsive">
        <table class="timetable">
            <thead>
                <tr>
                    <th>Time</th>
                    @foreach($days as $day)
                        <th>{{ $day->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($time_slots as $slot)
                    <tr>
                        <td>{{ $slot->start_time }} - {{ $slot->end_time }}</td>
                        @foreach($days as $day)
                            <td>
                                @foreach($timetables->where('day_id', $day->id)->where('time_slot_id', $slot->id) as $lesson)
                                    @php
                                        $canEdit = now()->subWeeks(2)->lessThanOrEqualTo(\Carbon\Carbon::parse($lesson->date));
                                        $hasAttendance = $lesson->attendances()->exists();
                                        $lessonClass = $hasAttendance ? 'attendance-done' : ($canEdit ? 'attendance-pending' : '');
                                    @endphp
                                    <div class="lesson {{ $lessonClass }}" 
                                         onclick="window.location.href='{{ route('teacher.attendance', $lesson->id) }}'"
                                         style="--lesson-color: {{ $lesson->color }}">
                                        <p class="course">{{ $lesson->course->name }}</p>
                                        <p class="class">{{ $lesson->class->name }}</p>
                                        @if($canEdit)
                                            <span class="attendance-status">
                                                @if($hasAttendance)
                                                    Attendance taken
                                                @else
                                                    Attendance pending
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection 