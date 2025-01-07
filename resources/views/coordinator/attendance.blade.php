@extends('layouts.coordinator')

@section('content')
<div class="card">
    <h3>Attendance Entry - {{ $timetable->course->name }}</h3>
    <div class="lesson-info">
        <p><strong>Course:</strong> {{ $timetable->course->name }}</p>
        <p><strong>Class:</strong> {{ $timetable->class->name }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($timetable->date)->format('d/m/Y') }}</p>
        <p><strong>Time:</strong> {{ $timetable->timeSlot->start_time }} - {{ $timetable->timeSlot->end_time }}</p>
    </div>

    <form action="{{ route('coordinator.attendance.store', $timetable->id) }}" method="POST">
        @csrf
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td>
                            <img src="{{ asset('storage/' . $student->photo) }}" 
                                 alt="Photo of {{ $student->user->name }}"
                                 class="student-photo">
                        </td>
                        <td>{{ $student->user->last_name }}</td>
                        <td>{{ $student->user->first_name }}</td>
                        <td>
                            <select name="attendance[{{ $student->id }}]" class="attendance-select">
                                <option value="present" {{ $attendances[$student->id] ?? '' == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="late" {{ $attendances[$student->id] ?? '' == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="absent" {{ $attendances[$student->id] ?? '' == 'absent' ? 'selected' : '' }}>Absent</option>
                            </select>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="submit-btn">Save</button>
    </form>
</div>
@endsection 