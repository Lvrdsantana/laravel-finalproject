<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historique des Emplois du Temps</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .action {
            font-weight: bold;
        }
        .action.created { color: #28a745; }
        .action.updated { color: #ffc107; }
        .action.deleted { color: #dc3545; }
        .changes {
            font-size: 11px;
            color: #666;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Historique des Emplois du Temps</h1>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Action</th>
                <th>Classe</th>
                <th>Cours</th>
                <th>Enseignant</th>
                <th>Modifié par</th>
                <th>Modifications</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $history)
                <tr>
                    <td>{{ $history->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="action {{ $history->action }}">
                        {{ ucfirst($history->action) }}
                    </td>
                    <td>{{ $history->class->name }}</td>
                    <td>{{ $history->course->name }}</td>
                    <td>{{ $history->teacher->name }}</td>
                    <td>{{ $history->modifier->name }}</td>
                    <td class="changes">
                        @if($history->changes)
                            @foreach($history->changes as $field => $change)
                                {{ ucfirst($field) }}:
                                @if(is_array($change))
                                    {{ $change[0] }} → {{ $change[1] }}
                                @else
                                    {{ $change }}
                                @endif
                                <br>
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Généré le {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html> 