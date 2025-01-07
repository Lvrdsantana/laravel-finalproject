@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Statistiques de classe - {{ $class->name }}</h1>

    <!-- Filtres de période -->
    <div class="filters mb-4">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="period">Période</label>
                <select name="period" id="period" class="form-control">
                    <option value="week" @selected($period == 'week')>Cette semaine</option>
                    <option value="month" @selected($period == 'month')>Ce mois</option>
                    <option value="semester" @selected($period == 'semester')>Ce semestre</option>
                    <option value="year" @selected($period == 'year')>Cette année</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary mt-4">Filtrer</button>
            </div>
        </form>
    </div>

    <!-- Moyenne de la classe -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Moyenne de présence de la classe</h3>
        </div>
        <div class="card-body">
            <div class="class-average">
                <div class="progress">
                    <div class="progress-bar {{ $classAverage < 50 ? 'bg-danger' : ($classAverage < 75 ? 'bg-warning' : 'bg-success') }}" 
                         role="progressbar" 
                         style="width: {{ $classAverage }}%"
                         aria-valuenow="{{ $classAverage }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        {{ number_format($classAverage, 1) }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des étudiants -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Statistiques par étudiant</h3>
            <div class="export-buttons">
                <a href="{{ route('attendance.export', ['class_id' => $class->id, 'type' => 'pdf']) }}" 
                   class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Exporter en PDF
                </a>
                <a href="{{ route('attendance.export', ['class_id' => $class->id, 'type' => 'excel']) }}" 
                   class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Exporter en Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Taux de présence</th>
                            <th>Note d'assiduité moyenne</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentStats as $stat)
                        <tr>
                            <td>{{ $stat['student']->user->name }}</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar {{ $stat['stats']['attendance_rate'] < 30 ? 'bg-danger' : 'bg-success' }}" 
                                         role="progressbar" 
                                         style="width: {{ $stat['stats']['attendance_rate'] }}%">
                                        {{ number_format($stat['stats']['attendance_rate'], 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($stat['stats']['attendance_grade'])
                                    {{ number_format($stat['stats']['attendance_grade'], 1) }}/20
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($stat['stats']['is_dropped'])
                                    <span class="badge bg-danger">Droppé</span>
                                @else
                                    <span class="badge bg-success">Actif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('attendance.student.dashboard', ['student' => $stat['student']->id]) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-chart-line"></i> Détails
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.progress {
    height: 25px;
    margin-bottom: 10px;
}

.progress-bar {
    font-size: 0.9em;
    line-height: 25px;
}

.class-average .progress {
    height: 40px;
}

.class-average .progress-bar {
    font-size: 1.2em;
    line-height: 40px;
}

.export-buttons .btn {
    margin-left: 10px;
}

.badge {
    font-size: 0.9em;
    padding: 8px 12px;
}
</style>
@endsection 