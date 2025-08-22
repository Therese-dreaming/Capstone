<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;
use App\Models\Laboratory;

class PaascuUtilizationExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithMultipleSheets
{
    protected $usageData;
    protected $summary;
    protected $primaryPurpose;
    protected $remarks;
    protected $notes;
    protected $filters;

    public function __construct($usageData, $summary, $primaryPurpose = '', $remarks = '', $notes = '', $filters = [])
    {
        $this->usageData = $usageData;
        $this->summary = $summary;
        $this->primaryPurpose = $primaryPurpose;
        $this->remarks = $remarks;
        $this->notes = $notes;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->usageData;
    }

    public function headings(): array
    {
        return [
            'Period',
            'Department',
            'Laboratory',
            'Total Sessions',
            'Total Hours',
            'Average Duration (hours)',
            'Unique Users',
            'Purpose',
            'Utilization Rate (%)'
        ];
    }

    public function map($data): array
    {
        // Calculate utilization rate (assuming 8 hours per day, 5 days per week)
        $utilizationRate = 0;
        if ($data->total_hours > 0) {
            // This is a simplified calculation - you might want to adjust based on your actual requirements
            $utilizationRate = min(100, round(($data->total_hours / 40) * 100, 1)); // 40 hours per week
        }

        return [
            $data->period,
            $data->department_name,
            $data->lab_name,
            $data->total_sessions,
            number_format($data->total_hours, 1),
            number_format($data->avg_duration, 1),
            $data->unique_users,
            $data->purpose ?? '',
            $utilizationRate
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC2626'], // Red-800
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style all data cells
        $sheet->getStyle('A2:I' . ($this->usageData->count() + 1))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);

        // Center align specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-filter
        $sheet->setAutoFilter('A1:I1');

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Period
            'B' => 25, // Department
            'C' => 20, // Laboratory
            'D' => 15, // Total Sessions
            'E' => 15, // Total Hours
            'F' => 20, // Average Duration
            'G' => 15, // Unique Users
            'H' => 20, // Purpose
            'I' => 18, // Utilization Rate
        ];
    }

    public function sheets(): array
    {
        return [
            'Laboratory Utilization' => $this,
            'Summary' => new PaascuUtilizationSummarySheet($this->usageData, $this->summary, $this->primaryPurpose, $this->remarks, $this->notes),
            'Lab Weekly Utilization' => new PaascuLabWeeklyUtilizationSheet($this->filters),
        ];
    }
}

class PaascuUtilizationSummarySheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $usageData;
    protected $summary;
    protected $primaryPurpose;
    protected $remarks;
    protected $notes;

    public function __construct($usageData, $summary, $primaryPurpose, $remarks, $notes)
    {
        $this->usageData = $usageData;
        $this->summary = $summary;
        $this->primaryPurpose = $primaryPurpose;
        $this->remarks = $remarks;
        $this->notes = $notes;
    }

    public function collection()
    {
        $summaryData = collect();

        // Add summary statistics
        $summaryData->push([
            'type' => 'Overall Summary',
            'metric' => 'Total Sessions',
            'value' => $this->summary->total_sessions ?? 0,
            'details' => ''
        ]);

        $summaryData->push([
            'type' => 'Overall Summary',
            'metric' => 'Total Hours',
            'value' => number_format($this->summary->total_hours ?? 0, 1),
            'details' => 'hours'
        ]);

        $summaryData->push([
            'type' => 'Overall Summary',
            'metric' => 'Average Duration',
            'value' => number_format($this->summary->avg_duration ?? 0, 1),
            'details' => 'hours per session'
        ]);

        $summaryData->push([
            'type' => 'Overall Summary',
            'metric' => 'Unique Users',
            'value' => $this->summary->unique_users ?? 0,
            'details' => 'users'
        ]);

        // Add department summary
        $departmentSummary = $this->usageData->groupBy('department_name')->map(function ($items, $department) {
            return [
                'type' => 'Department Summary',
                'metric' => $department,
                'value' => $items->sum('total_sessions'),
                'details' => 'sessions, ' . number_format($items->sum('total_hours'), 1) . ' hours'
            ];
        });

        // Add laboratory summary
        $labSummary = $this->usageData->groupBy('lab_name')->map(function ($items, $lab) {
            return [
                'type' => 'Laboratory Summary',
                'metric' => $lab,
                'value' => $items->sum('total_sessions'),
                'details' => 'sessions, ' . number_format($items->sum('total_hours'), 1) . ' hours'
            ];
        });

        return $summaryData->merge($departmentSummary)->merge($labSummary);
    }

    public function headings(): array
    {
        return [
            'Type',
            'Metric',
            'Value',
            'Details'
        ];
    }

    public function map($item): array
    {
        return [
            $item['type'],
            $item['metric'],
            $item['value'],
            $item['details']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'], // Gray-800
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style all data cells
        $sheet->getStyle('A2:D' . ($this->collection()->count() + 1))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);

        // Center align specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-filter
        $sheet->setAutoFilter('A1:D1');

        // Add additional information at the bottom
        $row = $this->collection()->count() + 3;
        
        if ($this->primaryPurpose) {
            $sheet->setCellValue("A{$row}", 'Primary Purpose:');
            $sheet->setCellValue("B{$row}", $this->primaryPurpose);
            $sheet->mergeCells("B{$row}:D{$row}");
            $row++;
        }

        if ($this->remarks) {
            $sheet->setCellValue("A{$row}", 'Remarks:');
            $sheet->setCellValue("B{$row}", $this->remarks);
            $sheet->mergeCells("B{$row}:D{$row}");
            $row++;
        }

        if ($this->notes) {
            $sheet->setCellValue("A{$row}", 'Notes:');
            $sheet->setCellValue("B{$row}", $this->notes);
            $sheet->mergeCells("B{$row}:D{$row}");
        }

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Type
            'B' => 30, // Metric
            'C' => 15, // Value
            'D' => 40, // Details
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
} 

class PaascuLabWeeklyUtilizationSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // Compute per-lab weekly totals within filters
        $query = DB::table('lab_logs')
            ->select(
                'lab_logs.laboratory as lab_name',
                DB::raw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours_used')
            );

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('time_in', [$this->filters['start_date'], $this->filters['end_date'] . ' 23:59:59']);
        }
        if (!empty($this->filters['department_id'])) {
            $query->join('users', 'lab_logs.user_id', '=', 'users.id')
                ->where('users.department', $this->filters['department_id']);
        }
        if (!empty($this->filters['lab_id'])) {
            $query->where('lab_logs.laboratory', $this->filters['lab_id']);
        }
        if (!empty($this->filters['purpose'])) {
            $query->where('lab_logs.purpose', $this->filters['purpose']);
        }

        $rows = $query->groupBy('lab_logs.laboratory')->get();

        // Enrich with lab meta (location) and compute utilization
        $labsMeta = Laboratory::all()->keyBy('number');
        $collection = collect();
        foreach ($rows as $r) {
            $labNumber = $r->lab_name; // stored as number per controller convention
            $lab = $labsMeta->get($labNumber);
            $roomLocation = $lab ? trim(($lab->building ? $lab->building . ' ' : '') . ($lab->floor ? 'Floor ' . $lab->floor . ' ' : '') . ($lab->room_number ?? '')) : '';
            $totalHoursAvailable = 40; // per requirement
            $totalUsed = (float)($r->total_hours_used ?? 0);
            $utilRate = $totalHoursAvailable > 0 ? round(min(100, ($totalUsed / $totalHoursAvailable) * 100), 1) : 0;

            // Compute primary purpose for this lab within filters
            $pq = DB::table('lab_logs')
                ->select('purpose', DB::raw('COUNT(*) as cnt'))
                ->where('laboratory', $labNumber)
                ->whereNotNull('purpose');
            if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
                $pq->whereBetween('time_in', [$this->filters['start_date'], $this->filters['end_date'] . ' 23:59:59']);
            }
            if (!empty($this->filters['department_id'])) {
                $pq->join('users', 'lab_logs.user_id', '=', 'users.id')
                   ->where('users.department', $this->filters['department_id']);
            }
            if (!empty($this->filters['purpose'])) {
                $pq->where('lab_logs.purpose', $this->filters['purpose']);
            }
            $primaryPurpose = $pq->groupBy('purpose')->orderByDesc('cnt')->limit(1)->value('purpose');

            $collection->push((object) [
                'lab_name' => 'Laboratory ' . $labNumber,
                'room_location' => $roomLocation,
                'total_hours_available' => $totalHoursAvailable,
                'total_hours_used' => round($totalUsed, 1),
                'utilization_rate' => $utilRate,
                'primary_purpose' => $primaryPurpose ?? ''
            ]);
        }

        return $collection;
    }

    public function headings(): array
    {
        return [
            'Laboratory Name / Code',
            'Room Location',
            'Total Hours Available per Week',
            'Total Hours Used per Week',
            'Utilization Rate (%)',
            'Primary Purpose',
        ];
    }

    public function map($row): array
    {
        return [
            $row->lab_name,
            $row->room_location,
            $row->total_hours_available,
            $row->total_hours_used,
            $row->utilization_rate,
            $row->primary_purpose,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A2:F' . ($this->collection()->count() + 1))->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ]);
        $sheet->getStyle('C:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setAutoFilter('A1:F1');
        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 35,
            'C' => 28,
            'D' => 28,
            'E' => 20,
            'F' => 28,
        ];
    }

    public function title(): string
    {
        return 'Lab Weekly Utilization';
    }
} 