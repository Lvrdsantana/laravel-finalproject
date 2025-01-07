@extends('layouts.teacher')

@section('content')
<!-- Container principal de l'emploi du temps -->
<div class="timetable-container">
    <!-- En-tête avec titre -->
    <div class="timetable-header">
        <h3>
            <i class="fas fa-calendar-alt"></i>
            My Timetable
        </h3>
    </div>
    <!-- Tableau responsive de l'emploi du temps -->
    <div class="table-responsive">
        <table class="timetable">
            <!-- En-tête du tableau avec les jours de la semaine -->
            <thead>
                <tr>
                    <th>Time</th>
                    @foreach($days as $day)
                        <th>{{ $day->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <!-- Corps du tableau avec les créneaux horaires -->
            <tbody>
                @foreach($time_slots as $slot)
                    <tr>
                        <!-- Affichage du créneau horaire -->
                        <td>{{ $slot->start_time }} - {{ $slot->end_time }}</td>
                        @foreach($days as $day)
                            <td>
                                <!-- Boucle sur les cours pour ce créneau et ce jour -->
                                @foreach($timetables->where('day_id', $day->id)->where('time_slot_id', $slot->id) as $lesson)
                                    @php
                                        // Vérifie si le cours est modifiable (moins de 2 semaines)
                                        $canEdit = now()->subWeeks(2)->lessThanOrEqualTo(\Carbon\Carbon::parse($lesson->date));
                                        // Vérifie si l'appel a déjà été fait
                                        $hasAttendance = $lesson->attendances()->exists();
                                        // Définit la classe CSS en fonction du statut
                                        $lessonClass = $hasAttendance ? 'attendance-done' : ($canEdit ? 'attendance-pending' : '');
                                    @endphp
                                    <!-- Case du cours avec lien vers la page de présence -->
                                    <div class="lesson {{ $lessonClass }}" 
                                         onclick="window.location.href='{{ route('teacher.attendance', $lesson->id) }}'"
                                         style="--lesson-color: {{ $lesson->color }}">
                                        <!-- Informations du cours -->
                                        <p class="course">{{ $lesson->course->name }}</p>
                                        <p class="class">{{ $lesson->class->name }}</p>
                                        <!-- Affichage du statut de présence si modifiable -->
                                        @if($canEdit)
                                            <span class="attendance-status">
                                                @if($hasAttendance)
                                                    Attendance taken
                                                @else
                                                    Attendance pending
                                                @endif
                                            </span>
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
@endsection 