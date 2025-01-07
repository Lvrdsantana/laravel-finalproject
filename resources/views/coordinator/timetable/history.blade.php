@extends('layouts.coordinator')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Timetable History</h2>
            <div class="timetable-info">
                <p><strong>Class:</strong> {{ $timetable->class->name }}</p>
                <p><strong>Course:</strong> {{ $timetable->course->name }}</p>
                <p><strong>Teacher:</strong> {{ $timetable->teacher->name }}</p>
                <p><strong>Time:</strong> {{ $timetable->start_time }} - {{ $timetable->end_time }}</p>
            </div>
        </div>

        <div class="card-body">
            <div class="timeline">
                @foreach($histories as $history)
                    <div class="timeline-item">
                        <div class="timeline-marker {{ $history->action === 'created' ? 'created' : ($history->action === 'deleted' ? 'deleted' : 'updated') }}"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="date">{{ $history->created_at->format('d/m/Y H:i:s') }}</span>
                                <span class="action {{ $history->action }}">{{ ucfirst($history->action) }}</span>
                            </div>
                            <div class="timeline-body">
                                <p><strong>Modified by:</strong> {{ $history->modifier->name }}</p>
                                @if($history->changes)
                                    <div class="changes">
                                        <strong>Changes:</strong>
                                        <ul>
                                            @foreach($history->changes as $field => $change)
                                                <li>
                                                    {{ ucfirst($field) }}:
                                                    @if(is_array($change))
                                                        <span class="old-value">{{ $change[0] }}</span>
                                                        <span class="arrow">→</span>
                                                        <span class="new-value">{{ $change[1] }}</span>
                                                    @else
                                                        {{ $change }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    display: flex;
    margin-bottom: 20px;
}

.timeline-marker {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    margin-right: 15px;
    margin-top: 5px;
}

.timeline-marker.created { background-color: #28a745; }
.timeline-marker.updated { background-color: #ffc107; }
.timeline-marker.deleted { background-color: #dc3545; }

.timeline-content {
    flex: 1;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.action {
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 0.875rem;
    font-weight: 500;
}

.action.created { background-color: #d4edda; color: #155724; }
.action.updated { background-color: #fff3cd; color: #856404; }
.action.deleted { background-color: #f8d7da; color: #721c24; }

.changes {
    margin-top: 10px;
    padding: 10px;
    background: #fff;
    border-radius: 3px;
}

.changes ul {
    list-style: none;
    padding-left: 0;
    margin: 10px 0 0;
}

.changes li {
    margin-bottom: 5px;
}

.old-value { color: #dc3545; }
.new-value { color: #28a745; }
.arrow {
    margin: 0 5px;
    color: #6c757d;
}

.timetable-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
}

.timetable-info p {
    margin: 0;
}
</style>
@endsection