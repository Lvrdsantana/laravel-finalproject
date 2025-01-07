<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TimetableHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $histories;

    public function __construct($histories)
    {
        $this->histories = $histories;
    }

    public function collection()
    {
        return $this->histories;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Action',
            'Classe',
            'Cours',
            'Enseignant',
            'Modifié par',
            'Modifications'
        ];
    }

    public function map($history): array
    {
        $changes = '';
        if ($history->changes) {
            foreach ($history->changes as $field => $change) {
                if (is_array($change)) {
                    $changes .= "{$field}: {$change[0]} → {$change[1]}\n";
                } else {
                    $changes .= "{$field}: {$change}\n";
                }
            }
        }

        return [
            $history->created_at->format('d/m/Y H:i:s'),
            ucfirst($history->action),
            $history->class->name,
            $history->course->name,
            $history->teacher->name,
            $history->modifier->name,
            $changes
        ];
    }
} 