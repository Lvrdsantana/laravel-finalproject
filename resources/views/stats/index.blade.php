<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/stats.css') }}">
    <link rel="stylesheet" href="{{ asset('css/coordinatorNotif.css') }}">
    <link rel="stylesheet" href="{{ asset('css/timetable.css') }}">

</head>
<body>

@extends('layouts.coordinator')

@section('title', 'Statistics')

@section('content')
<div class="stats-container">
    <div class="stats-header">
        <h2><i class="fas fa-chart-line"></i> Statistical Dashboard</h2>
        <p>Overview of attendance and course volume</p>
    </div>

    <div class="stats-grid">
        <div class="stats-card">
            <div class="card-header">
                <h3>Attendance Rate by Student</h3>
            </div>
            <div class="card-body">
                <canvas id="studentChart"></canvas>
            </div>
        </div>

        <div class="stats-card">
            <div class="card-header">
                <h3>Attendance Rate by Class</h3>
            </div>
            <div class="card-body">
                <canvas id="classChart"></canvas>
            </div>
        </div>

        <div class="stats-card">
            <div class="card-header">
                <h3>Course Volume Delivered</h3>
            </div>
            <div class="card-body">
                <canvas id="courseChart"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
.stats-container {
    padding: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.stats-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 15px;
}

.card-body {
    height: 300px;
    position: relative;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Wait for DOM to be fully loaded
window.onload = function() {
    // Load student data
    fetch('/stats/data/student-attendance')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('studentChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.name),
                    datasets: [{
                        label: 'Attendance Rate (%)',
                        data: data.map(item => item.rate),
                        backgroundColor: data.map(item => item.color)
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });

    // Load class data
    fetch('/stats/data/class-attendance')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('classChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.name),
                    datasets: [{
                        label: 'Attendance Rate (%)',
                        data: data.map(item => item.rate),
                        backgroundColor: '#4CAF50'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });

    // Load course data
    fetch('/stats/data/course-volume')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('courseChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.map(item => item.name),
                    datasets: [{
                        data: data.map(item => item.sessions),
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
};
</script>
@endsection 