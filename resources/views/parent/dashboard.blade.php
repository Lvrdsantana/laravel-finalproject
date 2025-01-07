@extends('layouts.app')

@section('title', 'Parent Dashboard')

@section('content')
<div class="container py-4">
    <!-- Dashboard header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-home text-primary me-2"></i>
                    Dashboard
                </h1>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <i class="fas fa-user me-2"></i>
                        My Profile
                    </button>
                    <div>
                        <span class="text-muted me-2">Last update:</span>
                        <span class="badge bg-light text-dark">{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="profileModalLabel">
                        <i class="fas fa-user-circle me-2"></i>
                        My Profile
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <h4 class="mb-1">{{ Auth::user()->name }}</h4>
                        <span class="badge bg-primary">Parent</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <i class="fas fa-envelope me-2"></i>
                                        Email
                                    </h6>
                                    <p class="card-text">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <i class="fas fa-user-graduate me-2"></i>
                                        Associated Students
                                    </h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($studentsData as $data)
                                            <div class="list-group-item bg-transparent px-0 py-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-3" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                                        {{ strtoupper(substr($data['student']->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $data['student']->user->name }}</h6>
                                                        <small class="text-muted">{{ $data['student']->class->name ?? 'No Class Assigned' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <i class="fas fa-clock me-2"></i>
                                        Account Created On
                                    </h6>
                                    <p class="card-text">{{ Auth::user()->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    @foreach($studentsData as $data)
    <div class="student-section mb-4">
        <!-- Student section header -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3">
                            {{ strtoupper(substr($data['student']->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="h5 mb-1">{{ $data['student']->user->name }}</h3>
                            <span class="badge bg-primary">{{ $data['student']->class->name ?? 'No Class Assigned' }}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-{{ $data['absences']['unjustified']->count() > 0 ? 'danger' : 'success' }} d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            {{ $data['absences']['unjustified']->count() }} unjustified absence(s)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick stats -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Attendance Rate</h6>
                                <h3 class="mb-0">{{ number_format($data['stats']['attendance_rate'], 1) }}%</h3>
                            </div>
                            <div class="icon-circle bg-primary text-white">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Absences</h6>
                                <h3 class="mb-0">{{ $data['stats']['total_absences'] }}</h3>
                            </div>
                            <div class="icon-circle bg-warning text-white">
                                <i class="fas fa-user-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Classes This Week</h6>
                                <h3 class="mb-0">{{ $data['stats']['weekly_courses'] }}</h3>
                            </div>
                            <div class="icon-circle bg-success text-white">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Next Class</h6>
                                <h3 class="mb-0 h5">{{ $data['stats']['next_course'] ?? 'None' }}</h3>
                            </div>
                            <div class="icon-circle bg-info text-white">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main grid -->
        <div class="row">
            <!-- Timetable -->
            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="h6 mb-0 text-primary">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Timetable
                            </h4>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-clock text-primary me-1"></i>
                                    Current Week
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 timetable-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 text-uppercase">
                                            <i class="fas fa-calendar-day text-primary me-2"></i>
                                            Day
                                        </th>
                                        <th class="text-uppercase">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            Time
                                        </th>
                                        <th class="text-uppercase">
                                            <i class="fas fa-book text-primary me-2"></i>
                                            Course
                                        </th>
                                        <th class="pe-3 text-uppercase">
                                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                                            Teacher
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentDay = strtolower(date('l'));
                                    @endphp
                                    @foreach($data['timetables'] as $timetable)
                                    <tr class="timetable-row {{ strtolower($timetable->day->name) === $currentDay ? 'current-day' : '' }}">
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="day-indicator me-2">
                                                    {{ substr($timetable->day->name, 0, 3) }}
                                                </div>
                                                <span class="day-name">{{ $timetable->day->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="time-slot">
                                                <span class="start-time">{{ $timetable->timeSlot->start_time }}</span>
                                                <span class="separator">-</span>
                                                <span class="end-time">{{ $timetable->timeSlot->end_time }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="course-badge" style="background-color: {{ $timetable->color ?? '#2563eb' }}">
                                                <i class="fas fa-book-open me-1"></i>
                                                {{ $timetable->course->name }}
                                            </div>
                                        </td>
                                        <td class="pe-3">
                                            <div class="teacher-info">
                                                <div class="teacher-avatar">
                                                    {{ strtoupper(substr($timetable->teacher->user->name, 0, 2)) }}
                                                </div>
                                                <span class="teacher-name">{{ $timetable->teacher->user->name }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Absences -->
            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="h6 mb-0 text-primary">
                                <i class="fas fa-user-clock me-2"></i>
                                Absences
                            </h4>
                            <div class="d-flex gap-2">
                                <span class="badge bg-danger">
                                    {{ $data['absences']['unjustified']->count() }} unjustified
                                </span>
                                <span class="badge bg-success">
                                    {{ $data['absences']['justified']->count() }} justified
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Absences accordion -->
                        <div class="accordion" id="absencesAccordion{{ $data['student']->id }}">
                            <!-- Unjustified absences -->
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#unjustifiedCollapse{{ $data['student']->id }}">
                                        <i class="fas fa-exclamation-circle text-danger me-2"></i>
                                        <span>Unjustified Absences</span>
                                        <span class="badge bg-danger ms-auto">{{ $data['absences']['unjustified']->count() }}</span>
                                    </button>
                                </h2>
                                <div id="unjustifiedCollapse{{ $data['student']->id }}" class="accordion-collapse collapse show">
                                    <div class="accordion-body p-0">
                                        @if($data['absences']['unjustified']->isEmpty())
                                            <div class="alert alert-success m-3 mb-0">
                                                <i class="fas fa-check-circle me-2"></i>
                                                No unjustified absences
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="ps-3">Date</th>
                                                            <th>Course</th>
                                                            <th class="pe-3">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data['absences']['unjustified'] as $absence)
                                                        <tr>
                                                            <td class="ps-3">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-calendar text-danger me-2"></i>
                                                                    {{ $absence->created_at ? $absence->created_at->format('d/m/Y H:i') : 'N/A' }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary">{{ $absence->timetable->course->name }}</span>
                                                            </td>
                                                            <td class="pe-3">
                                                                <span class="badge bg-danger">{{ ucfirst($absence->status) }}</span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Justified absences -->
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#justifiedCollapse{{ $data['student']->id }}">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Justified Absences</span>
                                        <span class="badge bg-success ms-auto">{{ $data['absences']['justified']->count() }}</span>
                                    </button>
                                </h2>
                                <div id="justifiedCollapse{{ $data['student']->id }}" class="accordion-collapse collapse">
                                    <div class="accordion-body p-0">
                                        @if($data['absences']['justified']->isEmpty())
                                            <div class="alert alert-info m-3 mb-0">
                                                <i class="fas fa-info-circle me-2"></i>
                                                No justified absences
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="ps-3">Date</th>
                                                            <th>Course</th>
                                                            <th>Reason</th>
                                                            <th class="pe-3">Justified On</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data['absences']['justified'] as $absence)
                                                        <tr>
                                                            <td class="ps-3">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-calendar text-success me-2"></i>
                                                                    {{ $absence->date ? $absence->date->format('d/m/Y') : 'N/A' }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary">{{ $absence->timetable->course->name }}</span>
                                                            </td>
                                                            <td>{{ $absence->justification->reason }}</td>
                                                            <td class="pe-3">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-clock text-muted me-2"></i>
                                                                    {{ $absence->justification->justified_at ? $absence->justification->justified_at->format('d/m/Y') : 'N/A' }}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<style>
/* Fond dégradé */
body {
    background: linear-gradient(135deg, #f6f8ff 0%, #e9f0ff 100%);
    min-height: 100vh;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-5px);
    }
    100% {
        transform: translateY(0px);
    }
}

/* Application des animations */
.student-section {
    animation: fadeInUp 0.6s ease-out;
}

.card {
    transition: all 0.3s ease;
    animation: slideInRight 0.6s ease-out;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.icon-circle {
    animation: float 3s ease-in-out infinite;
}

.avatar-circle {
    transition: all 0.3s ease;
}

.avatar-circle:hover {
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.1);
}

.course-badge {
    transition: all 0.3s ease;
}

.course-badge:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Animation du bouton profil */
.btn-outline-primary {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-outline-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
}

.btn-outline-primary::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 300%;
    height: 300%;
    background: rgba(37, 99, 235, 0.1);
    border-radius: 50%;
    transform: translate(-50%, -50%) scale(0);
    transition: transform 0.6s ease-out;
}

.btn-outline-primary:hover::after {
    transform: translate(-50%, -50%) scale(1);
}

/* Animation des accordéons */
.accordion-button {
    transition: all 0.3s ease;
}

.accordion-button:not(.collapsed) {
    transform: translateX(5px);
}

/* Animation de la table */
.timetable-row {
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.timetable-row:nth-child(1) { animation-delay: 0.1s; }
.timetable-row:nth-child(2) { animation-delay: 0.2s; }
.timetable-row:nth-child(3) { animation-delay: 0.3s; }
.timetable-row:nth-child(4) { animation-delay: 0.4s; }
.timetable-row:nth-child(5) { animation-delay: 0.5s; }

/* Animation du modal profil */
.modal.fade .modal-dialog {
    transform: scale(0.8);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: scale(1);
}

.modal-content {
    animation: fadeInUp 0.4s ease-out;
}

/* Amélioration des statistiques */
.card .card-body {
    position: relative;
    overflow: hidden;
}

.card .card-body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
    transform: translateX(-100%);
    transition: transform 0.6s ease-out;
}

.card:hover .card-body::before {
    transform: translateX(0);
}

.avatar-circle {
    width: 45px;
    height: 45px;
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
}

.card {
    border-radius: 0.75rem;
    border: none;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.1);
    background-color: white;
}

.table-hover tbody tr:hover {
    background-color: rgba(37, 99, 235, 0.05);
}

.accordion-button {
    padding: 1rem 1.25rem;
    background-color: white;
    border: none;
}

.accordion-button:not(.collapsed) {
    background-color: rgba(37, 99, 235, 0.05);
    color: var(--primary-color);
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(37, 99, 235, 0.2);
}

.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}

.alert {
    margin: 0;
    border: none;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
}

.table td {
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .avatar-circle {
        width: 35px;
        height: 35px;
        font-size: 1rem;
    }

    .card-header h3 {
        font-size: 1rem;
    }

    .badge {
        font-size: 0.75rem;
    }

    .table {
        font-size: 0.875rem;
    }
}

/* Styles pour l'emploi du temps */
.timetable-table {
    border-spacing: 0 0.5rem;
    border-collapse: separate;
    margin: 0;
}

.timetable-table thead th {
    background-color: #f8fafc;
    border: none;
    padding: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
}

.timetable-row {
    transition: all 0.2s ease;
    background-color: white;
}

.timetable-row:hover {
    transform: translateX(5px);
    background-color: #f8fafc !important;
}

.current-day {
    background-color: #eff6ff !important;
}

.day-indicator {
    background-color: #e0e7ff;
    color: var(--primary-color);
    padding: 0.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.75rem;
    min-width: 3rem;
    text-align: center;
}

.time-slot {
    display: inline-flex;
    align-items: center;
    background-color: #f1f5f9;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
}

.time-slot .separator {
    margin: 0 0.25rem;
    color: #94a3b8;
}

.course-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    color: white;
    font-weight: 500;
    font-size: 0.875rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.teacher-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.teacher-avatar {
    width: 2rem;
    height: 2rem;
    background-color: #e0e7ff;
    color: var(--primary-color);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

.teacher-name {
    font-size: 0.875rem;
    color: #1e293b;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .timetable-table {
        font-size: 0.875rem;
    }

    .day-indicator {
        padding: 0.25rem;
        font-size: 0.7rem;
        min-width: 2.5rem;
    }

    .time-slot {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    .course-badge {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    .teacher-avatar {
        width: 1.75rem;
        height: 1.75rem;
        font-size: 0.7rem;
    }

    .teacher-name {
        font-size: 0.8rem;
    }
}

/* Styles pour les statistiques rapides */
.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

/* Styles pour les filtres */
.form-select {
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    padding: 0.5rem;
    font-size: 0.875rem;
}

.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

.form-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .icon-circle {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des statistiques au scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.card').forEach(card => {
        observer.observe(card);
    });
});
</script>

@endsection

