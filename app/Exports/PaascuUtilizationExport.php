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

class PaascuUtilizationExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithMultipleSheets
{
    protected $usageData;
    protected $summary;
    protected $primaryPurpose;
    protected $remarks;
    protected $notes;

    public function __construct($usageData, $summary, $primaryPurpose = '', $remarks = '', $notes = '')
    {
        $this->usageData = $usageData;
        $this->summary = $summary;
        $this->primaryPurpose = $primaryPurpose;
        $this->remarks = $remarks;
        $this->notes = $notes;
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
            $utilizationRate
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:H1')->applyFromArray([
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
        $sheet->getStyle('A2:H' . ($this->usageData->count() + 1))->applyFromArray([
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

        // Auto-filter
        $sheet->setAutoFilter('A1:H1');

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
            'H' => 18, // Utilization Rate
        ];
    }

    public function sheets(): array
    {
        return [
            'Laboratory Utilization' => $this,
            'Summary' => new PaascuUtilizationSummarySheet($this->usageData, $this->summary, $this->primaryPurpose, $this->remarks, $this->notes),
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