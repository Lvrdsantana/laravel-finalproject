<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>  
        /* Table Styles */
        .user-list-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin: 1.5rem 0;
            overflow: hidden;
        }

        .section-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-box {
            position: relative;
            max-width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: none;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: var(--transition);
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box input:focus {
            background: rgba(255, 255, 255, 0.2);
            outline: none;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
        }

        .table-responsive {
            padding: 1rem;
        }

        .user-list {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .user-list th {
            background: var(--light-color);
            padding: 1rem;
            font-weight: 600;
            color: var(--dark-color);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .user-list td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
            transition: var(--transition);
        }

        .user-list tbody tr {
            transition: var(--transition);
        }

        .user-list tbody tr:hover {
            background: rgba(74, 111, 165, 0.05);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .role-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .role-students {
            background: rgba(78, 159, 61, 0.1);
            color: var(--success-color);
        }

        .role-teachers {
            background: rgba(74, 111, 165, 0.1);
            color: var(--primary-color);
        }

        .role-coordinators {
            background: rgba(255, 177, 0, 0.1);
            color: var(--warning-color);
        }

        .role-parents {
            background: rgba(214, 64, 69, 0.1);
            color: var(--danger-color);
        }

        .user-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .btn-edit {
            background: rgba(74, 111, 165, 0.1);
            color: var(--primary-color);
        }

        .btn-delete {
            background: rgba(214, 64, 69, 0.1);
            color: var(--danger-color);
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-edit:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-delete:hover {
            background: var(--danger-color);
            color: white;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: flex-end;
            padding: 1rem;
            background: var(--light-color);
            border-top: 1px solid #eee;
        }

        .pagination .page-link {
            padding: 0.5rem 1rem;
            border: none;
            margin: 0 0.25rem;
            border-radius: var(--border-radius);
            color: var(--primary-color);
            transition: var(--transition);
        }

        .pagination .page-link:hover {
            background: var(--primary-color);
            color: white;
        }

        .pagination .active .page-link {
            background: var(--primary-color);
            color: white;
        }

        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: var(--light-color);
            border-top: 1px solid #eee;
        }

        .pagination-info {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            margin: 0;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.75rem;
            border-radius: var(--border-radius);
            color: var(--primary-color);
            background: white;
            border: 1px solid #eee;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination .page-item.disabled .page-link {
            background: #f8f9fa;
            color: #6c757d;
            border-color: #eee;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .pagination-wrapper {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .pagination {
                justify-content: center;
            }
        }

        .pagination .page-link[rel="prev"]::before {
            content: "\f104";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        }

        .pagination .page-link[rel="next"]::after {
            content: "\f105";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        }

        .pagination svg {
            display: none;
        }

        .pagination span[aria-hidden="true"] {
            display: none;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.2) 100%);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .stat-item {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 1;
        }

        .stat-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.1);
        }

        .stat-item:hover::before {
            opacity: 0.05;
        }

        .stat-icon {
            position: relative;
            z-index: 2;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            transform: rotate(10deg);
            transition: all 0.4s ease;
        }

        .stat-item:hover .stat-icon {
            transform: rotate(0deg) scale(1.1);
        }

        .stat-icon i {
            color: white;
            font-size: 2rem;
            transform: rotate(-10deg);
            transition: all 0.4s ease;
        }

        .stat-item:hover .stat-icon i {
            transform: rotate(0deg) scale(1.1);
        }

        .stat-value {
            position: relative;
            z-index: 2;
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 1rem 0;
            line-height: 1;
        }

        .stat-item div:last-child {
            position: relative;
            z-index: 2;
            color: #666;
            font-size: 1.1rem;
            font-weight: 500;
            margin-top: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        #stats.card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 2rem;
        }

        #stats.card h2 {
            font-size: 1.8rem;
            color: var(--dark-color);
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        #stats.card h2 i {
            font-size: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @media (max-width: 768px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .stats {
                grid-template-columns: 1fr;
            }
            
            .stat-value {
                font-size: 2.5rem;
            }
        }

        /* Style pour les select multiples */
        select[multiple] {
            min-height: 150px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            width: 100%;
            margin-bottom: 15px;
        }

        select[multiple] option {
            padding: 8px;
            margin: 2px 0;
            border-radius: 4px;
            cursor: pointer;
        }

        select[multiple] option:hover {
            background-color: rgba(74, 111, 165, 0.1);
        }

        select[multiple] option:checked {
            background-color: var(--primary-color) !important;
            color: white;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo"><i class="fas fa-graduation-cap"></i>ADMIN PANEL</div>
            <nav>
                <div class="nav-item"><a href="#dashboard"><i class="fas fa-home"></i>Dashboard</a></div>
                <div class="nav-item"><a href="{{ route('coordinator.attendance.index') }}"><i class="fas fa-user-check"></i>Attendance</a></div>
                <div class="nav-item"><a href="{{ route('stats.index') }}"><i class="fas fa-chart-bar"></i>Statistics</a></div>
                <div class="nav-item"><a href="{{ url('coordinators-timetable') }}"><i class="fas fa-calendar-alt"></i>Timetable</a></div>
                <div class="nav-item"><a href="#users"><i class="fas fa-users"></i>User Management</a></div>
            </nav>
        </aside>
        <main class="main-content">
            <header class="header">
                <h1 class="logo"> <img src="{{ asset('images/logo.png') }}" alt="Logo IFRAN" /> Welcome to your dashboard</h1>
                <div class="admin-info">
                    <img src="/placeholder.svg?height=40&width=40" alt="Admin profile picture" />
                    <span class="admin-name">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-link"><i class="fas fa-sign-out-alt"></i> Logout</button>
                    </form>
                </div>
            </header>
            <section id="stats" class="card">
                <h2><i class="fas fa-chart-bar"></i> Global Statistics</h2>
                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value">{{ App\Models\User::where('role', 'students')->count() }}</div>
                        <div>Number of Students</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-value">{{ App\Models\User::where('role', 'teachers')->count() }}</div>
                        <div>Number of Teachers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-value">{{ App\Models\classes::count() }}</div>
                        <div>Number of Classes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-value">{{ App\Models\courses::count() }}</div>
                        <div>Number of Courses</div>
                    </div>
                </div>
            </section>
            <section id="users" class="user-list-section">
                <div class="section-header">
                    <h2><i class="fas fa-users"></i> User List</h2>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="userSearch" placeholder="Search for a user...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="user-list">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $user->name }}</div>
                                            <small class="text-muted">ID: #{{ $user->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="role-badge role-{{ $user->role }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="user-actions">
                                    <button class="btn-action btn-edit" data-toggle="modal" data-target="#editUserModal" 
                                            data-id="{{ $user->id }}" 
                                            data-name="{{ $user->name }}" 
                                            data-email="{{ $user->email }}" 
                                            data-role="{{ $user->role }}"
                                            data-class-id="{{ $user->role === 'students' && $user->student ? $user->student->class_id : '' }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                    </div>
                    <div class="pagination">
                        {{ $users->links() }}
                    </div>
                </div>
            </section>
            <section id="create-user" class="card">
                <h2><i class="fas fa-user-plus"></i> Create User</h2>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <input type="text" name="first_name" placeholder="First Name" required />
                    <input type="text" name="last_name" placeholder="Last Name" required />
                    <input type="email" name="email" placeholder="Email" required />
                    <select name="role" id="roleSelect" required>
                        <option value="">Select a role</option>
                        <option value="students">Students</option>
                        <option value="teachers">Teachers</option>
                        <option value="parents">Parents</option>
                        <option value="coordinators">Coordinators</option>
                    </select>

                    <!-- Select Class - initially hidden -->
                    <select name="class_id" id="classSelect" style="display:none;">
                        <option value="">Select a class</option>
                        @foreach(App\Models\Classes::all() as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>

                    <!-- Select Students for Parents - initially hidden -->
                    <select name="student_ids[]" id="studentSelect" multiple style="display:none;">
                        @foreach(App\Models\Students::with('user')->get() as $student)
                            <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit"><i class="fas fa-user-plus"></i> Create User</button>
                </form>
                @if(session('generated_password'))
                <div class="generated-password">
                    <strong>Generated Password:</strong> {{ session('generated_password') }}
                </div>
                @endif
            </section>
        </main>
    </div>

    <!-- Modal -->
    <!-- Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="form-group">
                        <label for="editFirstName">First Name</label>
                        <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="editLastName">Last Name</label>
                        <input type="text" class="form-control" id="editLastName" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editRole">Role</label>
                        <select class="form-control" id="editRole" name="role" required>
                            <option value="students">Students</option>
                            <option value="teachers">Teachers</option>
                            <option value="parents">Parents</option>
                            <option value="coordinators">Coordinators</option>
                        </select>
                    </div>
                    
                    <!-- Class selection, hidden by default -->
                    <div class="form-group" id="editClassDiv" style="display: none;">
                        <label for="editClassSelect">Class</label>
                        <select class="form-control" id="editClassSelect" name="class_id">
                            <option value="">Select a class</option>
                            @foreach(App\Models\Classes::all() as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Student selection for parents, hidden by default -->
                    <div class="form-group" id="editStudentDiv" style="display: none;">
                        <label for="editStudentSelect">Assign Students</label>
                        <select class="form-control" id="editStudentSelect" name="student_ids[]" multiple>
                            @foreach(App\Models\Students::with('user')->get() as $student)
                                <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('userSearch');
            const tableBody = document.querySelector('.user-list tbody');
            const paginationWrapper = document.querySelector('.pagination-wrapper');
            
            if (!searchInput) return;

            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchValue = this.value.trim();
                    
                    // Show loading indicator
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    `;

                    // Make AJAX request
                    fetch(`/users/search?search=${encodeURIComponent(searchValue)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        let html = '';
                        
                        if (data.users.data.length === 0) {
                            html = `
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No users found
                                        </div>
                                    </td>
                                </tr>
                            `;
                        } else {
                            data.users.data.forEach(user => {
                                html += `
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    ${user.name.charAt(0).toUpperCase()}
                                                </div>
                                                <div>
                                                    <div class="user-name">${user.name}</div>
                                                    <small class="text-muted">ID: #${user.id}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${user.email}</td>
                                        <td>
                                            <span class="role-badge role-${user.role}">
                                                ${user.role}
                                            </span>
                                        </td>
                                        <td class="user-actions">
                                            <button class="btn-action btn-edit" data-toggle="modal" 
                                                    data-target="#editUserModal" 
                                                    data-id="${user.id}" 
                                                    data-name="${user.name}" 
                                                    data-email="${user.email}" 
                                                    data-role="${user.role}"
                                                    data-class-id="${user.role === 'students' && user.student ? user.student.class_id : ''}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form action="/users/${user.id}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-delete">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        
                        tableBody.innerHTML = html;
                        
                        // Update pagination
                        if (data.pagination) {
                            paginationWrapper.innerHTML = data.pagination;
                        }
                        
                        // Update pagination info
                        const paginationInfo = document.querySelector('.pagination-info');
                        if (paginationInfo) {
                            paginationInfo.textContent = `Showing ${data.users.from || 0} to ${data.users.to || 0} of ${data.users.total} users`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="4" class="text-center">
                                    <div class="alert alert-danger mb-0">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        An error occurred while searching
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }, 300);
            });

            // Search input styling
            searchInput.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            searchInput.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        $('select[name="role"]').change(function() {
            if ($(this).val() === 'students') {
                $('#classSelect').show();
                $('#studentSelect').hide();
            } else if ($(this).val() === 'parents') {
                $('#classSelect').hide();
                $('#studentSelect').show();
            } else {
                $('#classSelect').hide();
                $('#studentSelect').hide();
            }
        });

        $('#editRole').change(function() {
            if ($(this).val() === 'students') {
                $('#editClassDiv').show();
                $('#editStudentDiv').hide();
                $('#editClassSelect').attr('required', 'required');
                $('#editStudentSelect').removeAttr('required');
            } else if ($(this).val() === 'parents') {
                $('#editClassDiv').hide();
                $('#editStudentDiv').show();
                $('#editClassSelect').removeAttr('required');
                $('#editStudentSelect').attr('required', 'required');
            } else {
                $('#editClassDiv').hide();
                $('#editStudentDiv').hide();
                $('#editClassSelect').removeAttr('required');
                $('#editStudentSelect').removeAttr('required');
            }
        });

        $(document).ready(function () {
            // Gérer l'affichage du champ class_id en fonction du rôle sélectionné
            $('#editRole').change(function () {
                if ($(this).val() === 'students') {
                    $('#editClassDiv').show();
                    $('#editStudentDiv').hide();
                    $('#editClassSelect').attr('required', 'required');
                    $('#editStudentSelect').removeAttr('required');
                } else if ($(this).val() === 'parents') {
                    $('#editClassDiv').hide();
                    $('#editStudentDiv').show();
                    $('#editClassSelect').removeAttr('required');
                    $('#editStudentSelect').attr('required', 'required');
                } else {
                    $('#editClassDiv').hide();
                    $('#editStudentDiv').hide();
                    $('#editClassSelect').removeAttr('required');
                    $('#editStudentSelect').removeAttr('required');
                }
            });

            // Pré-remplir le formulaire de modification avec les données de l'utilisateur
            $('#editUserModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var userId = button.data('id');
                var userName = button.data('name');
                var userEmail = button.data('email');
                var userRole = button.data('role');
                var userClassId = button.data('class-id');
                var studentIds = button.data('student-ids');

                var modal = $(this);
                modal.find('#editUserId').val(userId);
                
                // Séparer le nom complet en prénom et nom
                var nameParts = userName.split(' ');
                modal.find('#editFirstName').val(nameParts[0]);
                modal.find('#editLastName').val(nameParts.slice(1).join(' '));
                
                modal.find('#editEmail').val(userEmail);
                modal.find('#editRole').val(userRole);

                // Gérer l'affichage des champs en fonction du rôle
                if (userRole === 'students') {
                    $('#editClassDiv').show();
                    $('#editStudentDiv').hide();
                    $('#editClassSelect').val(userClassId);
                } else if (userRole === 'parents') {
                    $('#editClassDiv').hide();
                    $('#editStudentDiv').show();
                    if (studentIds) {
                        $('#editStudentSelect').val(studentIds.split(','));
                    }
                } else {
                    $('#editClassDiv').hide();
                    $('#editStudentDiv').hide();
                }

                // Définir l'action du formulaire pour la mise à jour
                var formAction = "{{ url('users') }}/" + userId;
                $('#editUserForm').attr('action', formAction);
            });
        });
    </script>
</body>

</html>