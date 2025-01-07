@extends('layouts.coordinator')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Global Timetable History</h2>
                <div class="export-buttons">
                    <a href="{{ route('coordinator.timetable.history.export', ['format' => 'pdf']) }}" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </a>
                    <a href="{{ route('coordinator.timetable.history.export', ['format' => 'excel']) }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                </div>
            </div>

            <div class="filters mt-3">
                <form action="{{ route('coordinator.timetable.history.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Class</label>
                                <select name="class_id" class="form-control">
                                    <option value="">All classes</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Action Type</label>
                                <select name="action" class="form-control">
                                    <option value="">All actions</option>
                                    <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Creation</option>
                                    <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Update</option>
                                    <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deletion</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Period</label>
                                <select name="period" class="form-control">
                                    <option value="">All periods</option>
                                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This week</option>
                                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This month</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('coordinator.timetable.history.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body">
            <!-- Quick Stats -->
            <div class="quick-stats mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card bg-info text-white">
                            <div class="stat-card-body">
                                <i class="fas fa-clock"></i>
                                <h5>Today</h5>
                                <h3>{{ $todayCount ?? 0 }}</h3>
                                <p>changes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-success text-white">
                            <div class="stat-card-body">
                                <i class="fas fa-plus-circle"></i>
                                <h5>Created</h5>
                                <h3>{{ $createdCount ?? 0 }}</h3>
                                <p>timetables</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-warning text-white">
                            <div class="stat-card-body">
                                <i class="fas fa-edit"></i>
                                <h5>Updates</h5>
                                <h3>{{ $updatedCount ?? 0 }}</h3>
                                <p>changes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-danger text-white">
                            <div class="stat-card-body">
                                <i class="fas fa-trash"></i>
                                <h5>Deletions</h5>
                                <h3>{{ $deletedCount ?? 0 }}</h3>
                                <p>timetables</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="timeline">
                @forelse($histories as $history)
                    <div class="timeline-item">
                        <div class="timeline-marker {{ $history->action === 'created' ? 'created' : ($history->action === 'deleted' ? 'deleted' : 'updated') }}"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="date">{{ $history->created_at->format('d/m/Y H:i:s') }}</span>
                                <span class="action {{ $history->action }}">{{ ucfirst($history->action) }}</span>
                            </div>
                            <div class="timeline-body">
                                <div class="timetable-info">
                                    <p><strong>Class:</strong> {{ $history->class->name }}</p>
                                    <p><strong>Course:</strong> {{ $history->course->name }}</p>
                                    <p><strong>Teacher:</strong> {{ $history->teacher->name }}</p>
                                    @if($history->timetable)
                                        <p><strong>Time:</strong> {{ $history->timetable->start_time }} - {{ $history->timetable->end_time }}</p>
                                    @endif
                                </div>
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
                                @if($history->timetable)
                                    <div class="actions">
                                        <a href="{{ route('coordinator.timetable.history', $history->timetable_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-history"></i> View complete history
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="no-history">
                        <p>No history found</p>
                    </div>
                @endforelse
            </div>

            <div class="pagination-container">
                {{ $histories->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.filter-form {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    flex: 1;
}

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
    margin: 10px 0;
    padding: 10px;
    background: #fff;
    border-radius: 4px;
}

.timetable-info p {
    margin: 0;
}

.actions {
    margin-top: 15px;
}

.no-history {
    text-align: center;
    padding: 20px;
    color: #6c757d;
}

.pagination-container {
    margin-top: 20px;
}

.quick-stats .stat-card {
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.quick-stats .stat-card:hover {
    transform: translateY(-5px);
}

.quick-stats .stat-card-body {
    text-align: center;
}

.quick-stats .stat-card i {
    font-size: 2em;
    margin-bottom: 10px;
}

.quick-stats .stat-card h5 {
    margin: 0;
    font-size: 1em;
}

.quick-stats .stat-card h3 {
    margin: 10px 0;
    font-size: 2em;
}

.quick-stats .stat-card p {
    margin: 0;
    font-size: 0.9em;
}

.export-buttons {
    display: flex;
    gap: 10px;
}

.export-buttons .btn {
    display: flex;
    align-items: center;
    gap: 5px;
}

.filter-form {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.filter-form label {
    font-weight: 500;
    margin-bottom: 5px;
}

.timeline-item {
    transition: transform 0.2s;
}

.timeline-item:hover {
    transform: translateX(5px);
}

.actions {
    opacity: 0.7;
    transition: opacity 0.2s;
}

.timeline-item:hover .actions {
    opacity: 1;
}
</style>
@endsection 