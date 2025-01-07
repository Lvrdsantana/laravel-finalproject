<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des emplois du temps</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div>
        <h1>Gestion des emplois du temps</h1>

        <!-- Formulaire de création -->
        <form action="{{ route('timetables.store') }}" method="POST">
            @csrf
            <label for="class_id">Classe :</label>
            <select name="class_id" required>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>

            <label for="teacher_id">Professeur :</label>
            <select name="teacher_id" required>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                @endforeach
            </select>

            <label for="course_id">Cours :</label>
            <select name="course_id" required>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                @endforeach
            </select>

            <label for="day_id">Jour :</label>
            <select name="day_id" required>
                @foreach ($days as $day)
                    <option value="{{ $day->id }}">{{ $day->name }}</option>
                @endforeach
            </select>

            <label for="time_slot_id">Créneau horaire :</label>
            <select name="time_slot_id" required>
                @foreach ($timeSlots as $timeSlot)
                    <option value="{{ $timeSlot->id }}">{{ $timeSlot->start_time }}</option>
                @endforeach
            </select>

            <label for="color">Couleur :</label>
            <input type="color" name="color" value="#ffffff">

            <button type="submit">Ajouter</button>
        </form>

        <!-- Tableau des emplois du temps -->
        <table>
            <thead>
                <tr>
                    <th>Classe</th>
                    <th>Cours</th>
                    <th>Professeur</th>
                    <th>Jour</th>
                    <th>Créneau horaire</th>
                    <th>Couleur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($timetables as $timetable)
                    <tr>
                        <td>{{ $timetable->class->name }}</td>
                        <td>{{ $timetable->course->name }}</td>
                        <td>{{ $timetable->teacher->name }}</td>
                        <td>{{ $timetable->day->name }}</td>
                        <td>{{ $timetable->timeSlot->start_time }}</td>
                        <td style="background-color: '{{ $timetable->color }}'">{{ $timetable->color }}</td>
                        <td>
                            <form action="{{ route('timetables.destroy', $timetable->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Voulez-vous vraiment supprimer cet emploi du temps ?');">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
