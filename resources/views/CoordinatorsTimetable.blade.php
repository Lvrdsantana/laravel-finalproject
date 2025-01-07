<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable Management</title>
    <link rel="stylesheet" href="{{ asset('css/coordinatorNotif.css') }}">
    <style>
        :root {
            --primary-color: #4A6FA5;
            --secondary-color: #166088;
            --accent-color: #4E9F3D;
            --light-color: #F8F9FA;
            --dark-color: #1B262C;
            --danger-color: #D64045;
            --warning-color: #FFB100;
            --success-color: #4E9F3D;
            --border-radius: 8px;
            --box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        .timetable-container {
            display: flex;
            gap: 20px;
            padding: 20px;
            max-width: 1800px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .timetable-card {
            flex: 1;
            min-width: 800px;
        }

        .form-card {
            width: 400px;
            display: none;
        }

        .card-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .card-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .add-timetable-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .add-timetable-btn:hover {
            background: #45893A;
            transform: translateY(-2px);
        }

        .timetable {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 1rem 0;
        }

        .timetable th {
            background: var(--light-color);
            padding: 1rem;
            font-weight: 600;
            color: var(--dark-color);
            border-bottom: 2px solid #eee;
        }

        .time-slot {
            background: var(--light-color);
            padding: 1rem;
            color: var(--dark-color);
            font-weight: 500;
            border-right: 2px solid #eee;
        }

        .schedule-cell {
            padding: 0.75rem;
            border: 1px solid #eee;
            min-height: 120px;
            vertical-align: top;
        }

        .lesson {
            background: white;
            border-radius: var(--border-radius);
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .lesson:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .lesson-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .course-name {
            font-weight: 600;
            color: white;
            font-size: 0.95rem;
        }

        .lesson-details {
            margin-bottom: 0.5rem;
        }

        .lesson-details p {
            margin: 0.25rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .lesson-actions {
            display: flex;
            gap: 0.5rem;
            opacity: 0;
            transition: var(--transition);
        }

        .lesson:hover .lesson-actions {
            opacity: 1;
        }

        .lesson-actions button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.4rem;
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
        }

        .lesson-actions button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .attendance-badge {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.4rem;
            font-size: 0.75rem;
            text-align: center;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            backdrop-filter: blur(4px);
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .lesson:hover .attendance-badge {
            transform: translateY(0);
        }

        .attendance-badge i {
            margin-right: 0.4rem;
            color: var(--success-color);
        }

        /* Formulaire */
        .timetable-form {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .form-group select:focus,
        .form-group input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 111, 165, 0.1);
            outline: none;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .submit-btn,
        .cancel-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .submit-btn {
            background: var(--success-color);
            color: white;
        }

        .cancel-btn {
            background: var(--danger-color);
            color: white;
        }

        .submit-btn:hover,
        .cancel-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: var(--border-radius);
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            color: var(--dark-color);
            cursor: pointer;
            transition: var(--transition);
        }

        .close-modal:hover {
            color: var(--danger-color);
            transform: rotate(90deg);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .timetable-container {
                flex-direction: column;
            }

            .timetable-card {
                min-width: 100%;
            }

            .form-card {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .add-course-btn {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .add-course-btn:hover {
            background: #124e6c;
            transform: translateY(-2px);
        }

        .course-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .course-form textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .course-form textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 111, 165, 0.1);
            outline: none;
        }
    </style>
</head>
<body>
@extends('layouts.coordinator')
@section('title', 'Timetable Management')
@section('breadcrumb')
   > Timetable
@endsection
@section('content')
<div class="timetable-container">
    <!-- Timetable section -->
    <div class="card timetable-card">
        <div class="card-header">
            <div class="header-actions">
                <h3><i class="fas fa-calendar-alt"></i> Class Timetables</h3>
                <div class="action-buttons">
                    <button class="add-course-btn" onclick="showAddCourseModal()">
                        <i class="fas fa-book"></i> New Course
                    </button>
                    <button class="add-timetable-btn" onclick="showAddForm()">
                        <i class="fas fa-plus"></i> Add Session
                    </button>
                </div>
            </div>
        </div>
        <div class="timetable-wrapper">
            <table class="timetable">
                <thead>
                    <tr>
                        <th class="time-header">Time</th>
                        @foreach($days as $day)
                            <th class="day-header">{{ $day->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($time_slots as $slot)
                        <tr>
                            <td class="time-slot">{{ $slot->start_time }} - {{ $slot->end_time }}</td>
                            @foreach($days as $day)
                                <td class="schedule-cell" data-day="{{ $day->id }}" data-slot="{{ $slot->id }}">
                                    @php
                                        $lessons = $timetables->where('day_id', $day->id)
                                            ->where('time_slot_id', $slot->id);
                                    @endphp
                                    @foreach($lessons as $lesson)
                                        <div class="lesson" 
                                            style="background-color: {{ $lesson->color }}"
                                            @if(in_array($lesson->course->name, ['Workshop', 'E-Learning']))
                                                onclick="handleLessonClick(event, '{{ route('coordinator.attendance', ['timetable' => $lesson->id]) }}')"
                                            @endif
                                        >
                                            <div class="lesson-header">
                                                <span class="course-name">{{ $lesson->course->name }}</span>
                                                <div class="lesson-actions">
                                                    <button class="edit-lesson" onclick="editLesson({{ $lesson->id }})" data-id="{{ $lesson->id }}" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="delete-lesson" data-id="{{ $lesson->id }}" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="lesson-details">
                                                <p class="class-name"><i class="fas fa-users"></i> {{ $lesson->class->name }}</p>
                                                <p class="teacher-name"><i class="fas fa-chalkboard-teacher"></i> {{ $lesson->teacher->user->name }}</p>
                                            </div>
                                            @if(in_array($lesson->course->name, ['Workshop', 'E-Learning']))
                                                <div class="attendance-badge">
                                                    <i class="fas fa-clipboard-check"></i> Take Attendance
                                                </div>
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
    <!-- Add timetable form -->
    <div class="card form-card" id="add-form-card">
        <div class="card-header">
            <h3><i class="fas fa-plus-circle"></i> Add Session</h3>
            <button class="close-form-btn" onclick="hideAddForm()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="add-class-form" method="POST" action="{{ route('coordinators.timetable.store') }}" class="timetable-form">
            @csrf
            <div class="form-group">
                <label for="class-name">
                    <i class="fas fa-users"></i> Class:
                </label>
                <select id="class-name" name="class_id" required>
                    <option value="">Select a class</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="course-name">
                    <i class="fas fa-book"></i> Course:
                </label>
                <select id="course-name" name="course_id" required>
                    <option value="">Select a course</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="teacher-name">
                    <i class="fas fa-chalkboard-teacher"></i> Teacher:
                </label>
                <select id="teacher-name" name="teacher_id" required>
                    <option value="">Select a teacher</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="day">
                        <i class="fas fa-calendar-day"></i> Day:
                    </label>
                    <select id="day" name="day_id" required>
                        <option value="">Select a day</option>
                        @foreach ($days as $day)
                            <option value="{{ $day->id }}">{{ $day->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="time-slot">
                        <i class="fas fa-clock"></i> Time:
                    </label>
                    <select id="time-slot" name="time_slot_id" required>
                        <option value="">Select a time slot</option>
                        @foreach ($time_slots as $slot)
                            <option value="{{ $slot->id }}">{{ $slot->start_time }} - {{ $slot->end_time }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="color">
                    <i class="fas fa-palette"></i> Color:
                </label>
                <div class="color-picker">
                    <input type="color" id="color" name="color" value="#4a90e2">
                    <span class="color-preview"></span>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Save
                </button>
                <button type="button" class="cancel-btn" onclick="hideAddForm()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Edit Session</h2>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-id" name="id">
            
            <div class="form-group">
                <label for="edit-class-name">Class:</label>
                <select id="edit-class-name" name="class_id" required>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="edit-course-name">Course:</label>
                <select id="edit-course-name" name="course_id" required>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="edit-day">Day:</label>
                <select id="edit-day" name="day_id" required>
                    @foreach ($days as $day)
                        <option value="{{ $day->id }}">{{ $day->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="edit-time-slot">Time:</label>
                <select id="edit-time-slot" name="time_slot_id" required>
                    @foreach ($time_slots as $slot)
                        <option value="{{ $slot->id }}">{{ $slot->start_time }} - {{ $slot->end_time }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="edit-teacher">Teacher:</label>
                <select id="edit-teacher" name="teacher_id" required>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="edit-color">Color:</label>
                <input type="color" id="edit-color" name="color">
            </div>

            <button type="submit">Save</button>
        </form>
    </div>
</div>
<!-- Add Course Modal -->
<div id="addCourseModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="hideAddCourseModal()">&times;</span>
        <h2><i class="fas fa-book"></i> Add New Course</h2>
        <form id="add-course-form" method="POST" action="{{ route('courses.store') }}" class="course-form">
            @csrf
            <div class="form-group">
                <label for="course-name">
                    <i class="fas fa-bookmark"></i> Course Name:
                </label>
                <input type="text" id="course-name" name="name" required placeholder="Enter course name">
            </div>
            <div class="form-group">
                <label for="course-description">
                    <i class="fas fa-align-left"></i> Description:
                </label>
                <textarea id="course-description" name="description" rows="3" placeholder="Enter course description"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Save Course
                </button>
                <button type="button" class="cancel-btn" onclick="hideAddCourseModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function showAddForm() {
    document.getElementById('add-form-card').style.display = 'block';
}

function hideAddForm() {
    document.getElementById('add-form-card').style.display = 'none';
}

// Update color preview
document.getElementById('color').addEventListener('input', function(e) {
    document.querySelector('.color-preview').style.backgroundColor = e.target.value;
});

// Success/error message animations
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
});

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == document.getElementById('addCourseModal')) {
        hideAddCourseModal();
    }
}

function showAddCourseModal() {
    document.getElementById('addCourseModal').style.display = 'block';
}

function hideAddCourseModal() {
    document.getElementById('addCourseModal').style.display = 'none';
}
</script>
@endsection