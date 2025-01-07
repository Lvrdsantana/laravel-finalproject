@extends('layouts.student')

@section('content')
<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Profile header -->
    <div class="profile-header mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="profile-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="col">
                <h2 class="mb-1">{{ Auth::user()->name }}</h2>
                <p class="text-muted mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Student in {{ $student->class->name }}
                </p>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Personal Information -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle text-primary me-2"></i>
                        Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="profile-info">
                        <div class="info-item">
                            <label>Email</label>
                            <p>{{ Auth::user()->email }}</p>
                        </div>
                        <div class="info-item">
                            <label>Student Number</label>
                            <p>{{ $student->id }}</p>
                        </div>
                        <div class="info-item">
                            <label>Class</label>
                            <p>{{ $student->class->name }}</p>
                        </div>
                        <div class="info-item">
                            <label>Status</label>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Summary -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-primary me-2"></i>
                        Attendance Summary
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $attendanceRate = $student->getAttendanceRate();
                        $status = $attendanceRate >= 70 ? 'success' : ($attendanceRate >= 50 ? 'warning' : 'danger');
                    @endphp
                    <div class="attendance-summary text-center mb-4">
                        <div class="attendance-circle mb-3">
                            <div class="percentage">{{ number_format($attendanceRate, 1) }}%</div>
                            <div class="label">Attendance Rate</div>
                        </div>
                    </div>
                    <div class="attendance-stats">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card bg-success-soft">
                                    <div class="stat-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="value">{{ $student->attendances->where('status', 'present')->count() }}</span>
                                        <span class="label">Present</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-warning-soft">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="value">{{ $student->attendances->where('status', 'late')->count() }}</span>
                                        <span class="label">Late</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-danger-soft">
                                    <div class="stat-icon">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="value">{{ $student->attendances->where('status', 'absent')->count() }}</span>
                                        <span class="label">Absent</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-info-soft">
                                    <div class="stat-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="value">{{ $student->attendances->count() }}</span>
                                        <span class="label">Total Sessions</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('student.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', Auth::user()->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" name="current_password" id="current_password"
                               class="form-control @error('current_password') is-invalid @enderror">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password"
                               class="form-control @error('new_password') is-invalid @enderror">
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                               class="form-control">
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.modal-backdrop {
    z-index: 1040 !important;
    position: relative;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.profile-header {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profile-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: bold;
}

.info-item {
    margin-bottom: 1.5rem;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-item label {
    display: block;
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.info-item p {
    margin: 0;
    font-size: 1rem;
    color: #2c3e50;
    font-weight: 500;
}

.attendance-circle {
    width: 150px;
    height: 150px;
    margin: 0 auto;
    border-radius: 50%;
    background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
}

.attendance-circle .percentage {
    font-size: 2rem;
    font-weight: bold;
}

.attendance-circle .label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.stat-card {
    padding: 1rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.bg-success-soft {
    background-color: rgba(40, 167, 69, 0.1);
}

.bg-warning-soft {
    background-color: rgba(255, 193, 7, 0.1);
}

.bg-danger-soft {
    background-color: rgba(220, 53, 69, 0.1);
}

.bg-info-soft {
    background-color: rgba(23, 162, 184, 0.1);
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-info .value {
    font-size: 1.25rem;
    font-weight: bold;
    color: #2c3e50;
}

.stat-info .label {
    font-size: 0.875rem;
    color: #6c757d;
}

@media (max-width: 768px) {
    .profile-header {
        text-align: center;
    }

    .profile-avatar {
        margin: 0 auto 1rem;
    }

    .col-auto {
        width: 100%;
        text-align: center;
        margin-top: 1rem;
    }
}
</style>
@endsection 