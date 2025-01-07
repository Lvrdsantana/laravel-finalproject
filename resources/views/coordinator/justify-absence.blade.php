@extends('layouts.coordinator')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-clipboard-check"></i> Absence Justification</h3>
            <div class="student-info">
                <p><strong>Student:</strong> {{ $attendance->student->user->name }}</p>
                <p><strong>Course:</strong> {{ $attendance->timetable->course->name }}</p>
                <p><strong>Date:</strong> {{ $attendance->marked_at ? $attendance->marked_at->format('d/m/Y') : 'N/A' }}</p>
                <p><strong>Marked by:</strong> {{ $attendance->teacher->user->name ?? 'N/A' }}</p>
                <p><strong>Current status:</strong> 
                    <span class="badge {{ $attendance->status === 'absent' ? 'bg-danger' : 'bg-warning' }}">
                        {{ $attendance->status }}
                    </span>
                    @if($attendance->justification)
                        <span class="badge bg-info">
                            Justified on {{ $attendance->justification->justified_at->format('d/m/Y') }}
                            by {{ $attendance->justification->justifier->name }}
                        </span>
                    @endif
                </p>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('coordinator.justify-absence', $attendance->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="justification_reason" class="form-label">Justification reason</label>
                    <textarea name="justification_reason" id="justification_reason" 
                            class="form-control @error('justification_reason') is-invalid @enderror" 
                            rows="3" required>{{ old('justification_reason', $attendance->justification_reason) }}</textarea>
                    @error('justification_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="justified" 
                           name="justified" {{ $attendance->justified ? 'checked' : '' }}>
                    <label class="form-check-label" for="justified">
                        Mark as justified
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                    <a href="{{ route('coordinator.attendance.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 