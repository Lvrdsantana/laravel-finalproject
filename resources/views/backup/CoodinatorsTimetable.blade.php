<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable Management</title>
    <link rel="stylesheet" href="{{ asset('css/coordinatorsTimetable.css') }}">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo">EduDash</div>
            <nav>
                <div class="nav-item"><a href="#dashboard">Dashboard</a></div>
                <div class="nav-item"><a href="#timetable">Timetable</a></div>
                <div class="nav-item"><a href="#timetable-history">Timetable History</a></div>
                <div class="nav-item"><a href="#courses">Courses</a></div>
                <div class="nav-item"><a href="#teachers">Teachers</a></div>
                <div class="nav-item"><a href="#classes">Classes</a></div>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Create Timetable</h1>
            <form action="{{ route('coordinator.timetable.store') }}" method="POST">
                @csrf
                <label for="course_id">Course:</label>
                <select id="course_id" name="course_id" required>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
                
                <label for="class_id">Class:</label>
                <select id="class_id" name="class_id" required>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                
                <label for="teacher_id">Teacher:</label>
                <select id="teacher_id" name="teacher_id" required>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->user ? $teacher->user->name : $teacher->name }}</option>
                    @endforeach
                </select>
                
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
                
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required>
                
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required>
                
                <button type="submit">Create</button>
            </form>

            <h2>Timetable</h2>
            @foreach($classes as $class)
                <h3>{{ $class->name }}</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Teacher</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timetables[$class->id] ?? [] as $timetable)
                            <tr>
                                <td>{{ $timetable->course->name }}</td>
                                <td>{{ $timetable->teacher->name }}</td>
                                <td>{{ $timetable->date }}</td>
                                <td>{{ $timetable->start_time }}</td>
                                <td>{{ $timetable->end_time }}</td>
                                <td>
                                    <a href="{{ route('coordinator.timetable.history', $timetable->id) }}" class="history-btn">View History</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach

            <div id="timetable-history" class="history-section" style="display: none;">
                <h2>Timetable History</h2>
                <div class="history-filters">
                    <select id="history-class-filter">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" id="history-date-filter">
                    <button onclick="filterHistory()">Filter</button>
                </div>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Class</th>
                            <th>Course</th>
                            <th>Teacher</th>
                            <th>Action</th>
                            <th>Modified By</th>
                            <th>Changes</th>
                        </tr>
                    </thead>
                    <tbody id="history-content">
                        @foreach($timetableHistories ?? [] as $history)
                            <tr>
                                <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $history->class->name }}</td>
                                <td>{{ $history->course->name }}</td>
                                <td>{{ $history->teacher->name }}</td>
                                <td>{{ ucfirst($history->action) }}</td>
                                <td>{{ $history->modifier->name }}</td>
                                <td>
                                    @if($history->changes)
                                        <ul>
                                            @foreach($history->changes as $field => $change)
                                                <li>{{ $field }}: {{ is_array($change) ? $change[0] . ' → ' . $change[1] : $change }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Afficher/masquer la section historique
        document.querySelector('a[href="#timetable-history"]').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.history-section').style.display = 'block';
            document.querySelector('#timetable').style.display = 'none';
        });

        // Fonction de filtrage de l'historique
        function filterHistory() {
            const classId = document.getElementById('history-class-filter').value;
            const date = document.getElementById('history-date-filter').value;
            
            // Appel AJAX pour filtrer l'historique
            fetch(`/coordinator/timetable/history/filter?class_id=${classId}&date=${date}`)
                .then(response => response.json())
                .then(data => {
                    const historyContent = document.getElementById('history-content');
                    historyContent.innerHTML = ''; // Vider le contenu actuel
                    
                    // Remplir avec les nouvelles données
                    data.forEach(history => {
                        historyContent.innerHTML += `
                            <tr>
                                <td>${history.created_at}</td>
                                <td>${history.class_name}</td>
                                <td>${history.course_name}</td>
                                <td>${history.teacher_name}</td>
                                <td>${history.action}</td>
                                <td>${history.modifier_name}</td>
                                <td>${formatChanges(history.changes)}</td>
                            </tr>
                        `;
                    });
                });
        }

        // Fonction pour formater les changements
        function formatChanges(changes) {
            if (!changes) return '';
            
            let html = '<ul>';
            for (const [field, change] of Object.entries(changes)) {
                if (Array.isArray(change)) {
                    html += `<li>${field}: ${change[0]} → ${change[1]}</li>`;
                } else {
                    html += `<li>${field}: ${change}</li>`;
                }
            }
            html += '</ul>';
            return html;
        }
    </script>
    <style>
        .history-section {
            margin-top: 2rem;
            padding: 1rem;
        }

        .history-filters {
            margin-bottom: 1rem;
            display: flex;
            gap: 1rem;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .history-table th,
        .history-table td {
            padding: 0.5rem;
            border: 1px solid #ddd;
            text-align: left;
        }

        .history-table th {
            background-color: #f5f5f5;
        }

        .history-btn {
            padding: 0.25rem 0.5rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .history-btn:hover {
            background-color: #0056b3;
        }
    </style>
</body>
</html>