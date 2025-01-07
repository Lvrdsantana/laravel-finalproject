@extends('layouts.teacher')

@section('content')
<div class="profile-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="profile-header">
        <div class="profile-cover"></div>
        <div class="profile-avatar-wrapper">
            <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('images/default-avatar.png') }}" alt="Profile picture" class="profile-avatar">
            <button class="edit-avatar-btn" data-bs-toggle="modal" data-bs-target="#avatarModal">
                <i class="fas fa-camera"></i>
            </button>
        </div>
        <div class="profile-header-info">
            <h1>{{ Auth::user()->name }}</h1>
            <p class="profile-title">Teacher</p>
            <button class="edit-profile-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="fas fa-edit"></i> Edit Profile
            </button>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-section">
            <div class="section-header">
                <i class="fas fa-user-circle"></i>
                <h2>Personal Information</h2>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Full Name</span>
                    <span class="info-value">{{ Auth::user()->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ Auth::user()->email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Registration Date</span>
                    <span class="info-value">{{ Auth::user()->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="info-value status-active">
                        <i class="fas fa-circle"></i> Active
                    </span>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <div class="section-header">
                <i class="fas fa-book"></i>
                <h2>Subjects Taught</h2>
            </div>
            <div class="subjects-grid">
                @if($timetables->count() > 0)
                    @foreach($timetables->groupBy('course_id') as $course_id => $lessons)
                        <div class="subject-card">
                            <div class="subject-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="subject-info">
                                <h3>{{ $lessons->first()->course->name }}</h3>
                                <p>{{ $lessons->count() }} sessions</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="no-subjects">
                        <i class="fas fa-books"></i>
                        <p>No subjects assigned yet</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="profile-section">
            <div class="section-header">
                <i class="fas fa-chart-line"></i>
                <h2>Statistics</h2>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-details">
                        <span class="stat-value">{{ $timetables->count() }}</span>
                        <span class="stat-label">Teaching Hours</span>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <span class="stat-value">{{ $timetables->groupBy('class_id')->count() }}</span>
                        <span class="stat-label">Classes</span>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-details">
                        <span class="stat-value">{{ $timetables->groupBy('course_id')->count() }}</span>
                        <span class="stat-label">Subjects</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit My Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('teacher.profile.update') }}" method="POST" class="edit-profile-form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="password-section mb-3">
                        <h6 class="section-title">Change Password</h6>
                        <div class="form-group mb-3">
                            <label for="current_password">Current Password</label>
                            <div class="password-input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                                <button type="button" class="toggle-password" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="new_password">New Password</label>
                            <div class="password-input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                                <button type="button" class="toggle-password" data-target="new_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Minimum 8 characters</small>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <div class="password-input-group">
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                                <button type="button" class="toggle-password" data-target="new_password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Avatar Change Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avatarModalLabel">Change Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('teacher.profile.avatar') }}" method="POST" enctype="multipart/form-data" class="avatar-form">
                @csrf
                <div class="modal-body">
                    <div class="avatar-upload-container">
                        <div class="current-avatar">
                            <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('images/default-avatar.png') }}" alt="Current Avatar" class="preview-avatar">
                        </div>
                        <div class="upload-section">
                            <label for="avatar" class="upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Choose an image</span>
                            </label>
                            <input type="file" id="avatar" name="avatar" class="avatar-input @error('avatar') is-invalid @enderror" accept="image/*" required>
                            <p class="upload-info">Accepted formats: JPG, PNG. Max 2MB</p>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show modal if there are errors
    @if($errors->any())
        var editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
        editProfileModal.show();
    @endif

    // Avatar preview handling
    const avatarInput = document.querySelector('.avatar-input');
    const previewAvatar = document.querySelector('.preview-avatar');
    
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewAvatar.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Password visibility toggle handling
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>
@endpush
@endsection