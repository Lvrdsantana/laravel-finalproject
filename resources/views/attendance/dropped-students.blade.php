@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Étudiants droppés</h1>

    <!-- Filtre par cours -->
    <div class="filters mb-4">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="course_id">Cours</label>
                <select name="course_id" id="course_id" class="form-control">
                    <option value="">Sélectionner un cours</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected($course->id == $courseId)>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary mt-4">Filtrer</button>
            </div>
        </form>
    </div>

    <!-- Liste des étudiants droppés -->
    <div class="card">
        <div class="card-header">
            <h3>Liste des étudiants droppés</h3>
        </div>
        <div class="card-body">
            @if(count($droppedStudents) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Classe</th>
                                <th>Taux de présence</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($droppedStudents as $dropped)
                            <tr>
                                <td>{{ $dropped['student']->user->name }}</td>
                                <td>{{ $dropped['student']->class->name }}</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" 
                                             role="progressbar" 
                                             style="width: {{ $dropped['attendance_rate'] }}%">
                                            {{ number_format($dropped['attendance_rate'], 1) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('attendance.student.dashboard', ['student' => $dropped['student']->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-chart-line"></i> Détails
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Aucun étudiant droppé pour le moment.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.progress {
    height: 25px;
}

.progress-bar {
    font-size: 0.9em;
    line-height: 25px;
}
</style>
@endsection 