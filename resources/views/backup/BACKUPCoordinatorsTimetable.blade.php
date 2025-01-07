<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des emplois du temps</title>
</head>
<body>
    <div class="container">
        <h1>Gestion des emplois du temps</h1>

        <!-- Formulaire de création -->
        <form action="{{ route('coordinator.timetable.store') }}" method="POST">
            @csrf
            <label for="class_id">Classe :</label>
            <select name="class_id" required>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>

            <label for="course_id">Cours :</label>
            <select name="course_id" required>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                @endforeach
            </select>

            <label for="teacher_id">Professeur :</label>
            <select name="teacher_id" required>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->user ? $teacher->user->name : $teacher->name}}</option>
                @endforeach
            </select>

            <label for="day_id">Jour :</label>
            <select name="day_id" required>
                @foreach($days as $day)
                    <option value="{{ $day->id }}">{{ $day->name }}</option>
                @endforeach
            </select>

            <label for="time_slot_id">Créneau horaire :</label>
            <select name="time_slot_id" required>
                @foreach($timeSlots as $slot)
                    <option value="{{ $slot->id }}">{{ $slot->start_time }}</option>
                @endforeach
            </select>

            <button type="submit">Créer</button>
        </form>

        <hr>

        <!-- Tableau des emplois du temps -->
        <table border="1">
            <thead>
                <tr>
                    <th>Classe</th>
                    <th>Cours</th>
                    <th>Professeur</th>
                    <th>Jour</th>
                    <th>Créneau horaire</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timetables as $timetable)
                    <tr>
                    <td>{{ $timetable->class_name }}</td>
            <td>{{ $timetable->course_name }}</td>
            <td>{{ $timetable->teacher_name }}</td>
            <td>{{ $timetable->day_name }}</td>
            <td>{{ $timetable->start_time }}</td>
            <td>
                            <!-- Formulaire de suppression -->
                            <form action="{{ route('coordinator.timetable.delete', $timetable->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Supprimer</button>
                            </form>

                            <!-- Bouton d'édition -->
                            <a href="{{ route('coordinator.timetable.edit', $timetable->id) }}">Modifier</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
