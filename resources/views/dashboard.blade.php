<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Métadonnées de base -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management Dashboard</title>

    <!-- Feuilles de style CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-usersmanagement.css') }}">
</head>

<body>
    <div class="dashboard">
        <!-- Barre latérale avec navigation -->
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

        <!-- Contenu principal -->
        <main class="main-content">
            <!-- En-tête avec logo et infos admin -->
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

            <!-- Section des statistiques globales -->
            <section id="stats" class="card">
                <h2><i class="fas fa-chart-bar"></i> Global Statistics</h2>
                <div class="stats">
                    <!-- Statistiques des étudiants -->
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value">{{ App\Models\User::where('role', 'students')->count() }}</div>
                        <div>Number of Students</div>
                    </div>
                    <!-- Statistiques des enseignants -->
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-value">{{ App\Models\User::where('role', 'teachers')->count() }}</div>
                        <div>Number of Teachers</div>
                    </div>
                    <!-- Statistiques des classes -->
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-value">{{ App\Models\classes::count() }}</div>
                        <div>Number of Classes</div>
                    </div>
                    <!-- Statistiques des cours -->
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-value">{{ App\Models\courses::count() }}</div>
                        <div>Number of Courses</div>
                    </div>
                </div>
            </section>

            <!-- Section liste des utilisateurs -->
            <section id="users" class="user-list-section">
                <div class="section-header">
                    <h2><i class="fas fa-users"></i> User List</h2>
                    <!-- Barre de recherche -->
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="userSearch" placeholder="Search for a user...">
                    </div>
                </div>

                <!-- Tableau des utilisateurs -->
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
                                    <!-- Boutons d'action (éditer/supprimer) -->
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

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                    </div>
                    <div class="pagination">
                        {{ $users->links() }}
                    </div>
                </div>
            </section>

            <!-- Section création d'utilisateur -->
            <section id="create-user" class="card">
                <h2><i class="fas fa-user-plus"></i> Create User</h2>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <!-- Champs du formulaire -->
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

                    <!-- Sélection de classe (caché par défaut) -->
                    <select name="class_id" id="classSelect" style="display:none;">
                        <option value="">Select a class</option>
                        @foreach(App\Models\Classes::all() as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>

                    <!-- Sélection d'étudiants pour les parents (caché par défaut) -->
                    <select name="student_ids[]" id="studentSelect" multiple style="display:none;">
                        @foreach(App\Models\Students::with('user')->get() as $student)
                        <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit"><i class="fas fa-user-plus"></i> Create User</button>
                </form>
                <!-- Affichage du mot de passe généré -->
                @if(session('generated_password'))
                <div class="generated-password">
                    <strong>Generated Password:</strong> {{ session('generated_password') }}
                </div>
                @endif
            </section>
        </main>
    </div>

    <!-- Modal d'édition d'utilisateur -->
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
                        <!-- Champs du formulaire d'édition -->
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
                        <!-- Sélection de classe pour l'édition -->
                        <div class="form-group" id="editClassDiv" style="display: none;">
                            <label for="editClassSelect">Class</label>
                            <select class="form-control" id="editClassSelect" name="class_id">
                                <option value="">Select a class</option>
                                @foreach(App\Models\Classes::all() as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Sélection d'étudiants pour les parents -->
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
    <!-- Scripts JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/dashboard-usersmanagement.js') }}"></script>
</body>

</html>