@extends('layouts.teacher')

@section('content')
<div class="attendance-container">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-clipboard-check"></i> Attendance Entry</h3>
            <div class="lesson-info">
                <div class="info-item">
                    <i class="fas fa-book"></i>
                    <span><strong>Course:</strong> {{ $timetable->course->name }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-users"></i>
                    <span><strong>Class:</strong> {{ $timetable->class->name }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <span><strong>Date:</strong> {{ \Carbon\Carbon::parse($timetable->date)->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span><strong>Time:</strong> {{ $timetable->timeSlot->start_time }} - {{ $timetable->timeSlot->end_time }}</span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('teacher.attendance.store', $timetable->id) }}" method="POST" id="attendanceForm">
                @csrf
                <div class="attendance-controls mb-3">
                    <button type="button" class="btn btn-success btn-sm" onclick="markAllAs('present')">
                        <i class="fas fa-check"></i> All Present
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="markAllAs('late')">
                        <i class="fas fa-clock"></i> All Late
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="markAllAs('absent')">
                        <i class="fas fa-times"></i> All Absent
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table attendance-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Status</th>
                                <th>Last Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr class="student-row">
                                    <td>{{ $student->user->name }}</td>
                                    <td>
                                        <select name="attendance[{{ $student->id }}]" 
                                                class="attendance-select form-select"
                                                data-student-id="{{ $student->id }}">
                                            <option value="present" {{ $attendances[$student->id] ?? '' == 'present' ? 'selected' : '' }}>
                                                Present
                                            </option>
                                            <option value="late" {{ $attendances[$student->id] ?? '' == 'late' ? 'selected' : '' }}>
                                                Late
                                            </option>
                                            <option value="absent" {{ $attendances[$student->id] ?? '' == 'absent' ? 'selected' : '' }}>
                                                Absent
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            @if(isset($attendances[$student->id]))
                                                {{ \Carbon\Carbon::parse($timetable->marked_at)->format('d/m/Y H:i') }}
                                            @else
                                                Not marked
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-sm edit-attendance"
                                                data-student-id="{{ $student->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                    <a href="{{ route('teacher.timetable') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.attendance-container {
    padding: 20px;
}

.lesson-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-item i {
    color: #007bff;
}

.attendance-controls {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.attendance-table {
    margin-top: 20px;
}

.attendance-select {
    width: 100%;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.form-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.student-row {
    transition: background-color 0.3s;
}

.student-row:hover {
    background-color: #f8f9fa;
}

.edit-attendance {
    opacity: 0.5;
    transition: opacity 0.3s;
}

.student-row:hover .edit-attendance {
    opacity: 1;
}
</style>

<script>
function markAllAs(status) {
    document.querySelectorAll('.attendance-select').forEach(select => {
        select.value = status;
    });
}

document.querySelectorAll('.edit-attendance').forEach(button => {
    button.addEventListener('click', function() {
        const studentId = this.dataset.studentId;
        const select = document.querySelector(`select[data-student-id="${studentId}"]`);
        select.focus();
    });
});

// Auto-save after modification
document.querySelectorAll('.attendance-select').forEach(select => {
    select.addEventListener('change', function() {
        const form = document.getElementById('attendanceForm');
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => {
            if (response.ok) {
                // Show success notification
                const row = this.closest('tr');
                row.style.backgroundColor = '#d4edda';
                setTimeout(() => {
                    row.style.backgroundColor = '';
                }, 1000);
            }
        });
    });
});
</script>
@endsection