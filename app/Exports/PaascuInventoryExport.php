<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class PaascuInventoryExport implements FromCollection, WithMapping, WithStyles, WithMultipleSheets
{
    protected $assets;
    protected $filters;

    public function __construct($assets, $filters = [])
    {
        $this->assets = $assets;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->assets;
    }

    public function map($asset): array
    {
        // Get the last maintenance date for this asset
        $lastMaintenanceDate = 'N/A';
        if ($asset->location_id) {
            $lastMaintenance = \App\Models\Maintenance::where('location_id', $asset->location_id)
                ->where('status', 'completed')
                ->where(function($query) use ($asset) {
                    $query->whereNull('excluded_assets')
                        ->orWhere(function($q) use ($asset) {
                            $q->whereJsonDoesntContain('excluded_assets', $asset->id);
                        });
                })
                ->orderBy('completed_at', 'desc')
                ->first();
            
            if ($lastMaintenance && $lastMaintenance->completed_at) {
                $lastMaintenanceDate = \Carbon\Carbon::parse($lastMaintenance->completed_at)->format('M d, Y');
            }
        }

        return [
            $asset->serial_number ?: 'N/A',
            $asset->name,
            $asset->category->name ?? 'N/A',
            $asset->location ? $asset->location->building : 'N/A',
            $asset->location ? $asset->location->floor : 'N/A',
            $asset->location ? $asset->location->room_number : 'N/A',
            $asset->purchase_price ? '₱' . number_format($asset->purchase_price, 2) : 'N/A',
            $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') : 'N/A',
            $asset->status,
            $asset->vendor->name ?? 'N/A',
            $asset->model ?: 'N/A',
            $asset->specification ?: 'N/A',
            $asset->warranty_period ?: 'N/A',
            $lastMaintenanceDate
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Add header section
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'PAASCU COMPUTER LABORATORY INVENTORY REPORT');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
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

        // Add subtitle with generation date
        $sheet->mergeCells('A2:N2');
        $sheet->setCellValue('A2', 'Generated on ' . \Carbon\Carbon::now()->format('F d, Y \a\t h:i A'));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '374151'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'], // Gray-100
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Add date range filter information if available
        $currentRow = 3;
        if (!empty($this->filters['start_date']) || !empty($this->filters['end_date'])) {
            $sheet->mergeCells('A' . $currentRow . ':N' . $currentRow);
            $dateRangeText = 'Date Range: ';
            
            if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
                $dateRangeText .= \Carbon\Carbon::parse($this->filters['start_date'])->format('M d, Y') . ' to ' . \Carbon\Carbon::parse($this->filters['end_date'])->format('M d, Y');
            } elseif (!empty($this->filters['start_date'])) {
                $dateRangeText .= 'From ' . \Carbon\Carbon::parse($this->filters['start_date'])->format('M d, Y') . ' onwards';
            } elseif (!empty($this->filters['end_date'])) {
                $dateRangeText .= 'Up to ' . \Carbon\Carbon::parse($this->filters['end_date'])->format('M d, Y');
            }
            
            $sheet->setCellValue('A' . $currentRow, $dateRangeText);
            $sheet->getStyle('A' . $currentRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => '1F2937'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'], // Gray-200
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $currentRow++;
        }

        // Add other filter information if available
        $filterTexts = [];
        if (!empty($this->filters['category_id'])) {
            $category = \App\Models\Category::find($this->filters['category_id']);
            if ($category) {
                $filterTexts[] = 'Category: ' . $category->name;
            }
        }
        if (!empty($this->filters['status'])) {
            $filterTexts[] = 'Status: ' . $this->filters['status'];
        }
        if (!empty($this->filters['vendor_id'])) {
            $vendor = \App\Models\Vendor::find($this->filters['vendor_id']);
            if ($vendor) {
                $filterTexts[] = 'Vendor: ' . $vendor->name;
            }
        }

        if (!empty($filterTexts)) {
            $sheet->mergeCells('A' . $currentRow . ':N' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Filters Applied: ' . implode(' | ', $filterTexts));
            $sheet->getStyle('A' . $currentRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => '1F2937'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'], // Gray-200
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $currentRow++;
        }

        // Add summary row
        $sheet->mergeCells('A' . $currentRow . ':N' . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'Total Assets: ' . $this->assets->count() . ' | Total Value: ₱' . number_format($this->assets->sum('purchase_price'), 2));
        $sheet->getStyle('A' . $currentRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => '1F2937'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E7EB'], // Gray-200
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $currentRow++;

        // Manually set the column headers in the correct row
        $headerRow = $currentRow;
        $headers = [
            'Serial Number',
            'Equipment Name',
            'Category',
            'Building',
            'Floor',
            'Room',
            'Purchase Price',
            'Purchase Date',
            'Status',
            'Vendor',
            'Model',
            'Specifications',
            'Warranty Expiry',
            'Last Maintenance'
        ];

        // Set header values
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . $headerRow, $header);
        }

        // Style the column headers
        $sheet->getStyle('A' . $headerRow . ':N' . $headerRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
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
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '374151'],
                ],
            ],
        ]);

        // Manually place the data starting from the next row
        $dataStartRow = $headerRow + 1;
        $rowIndex = $dataStartRow;
        
        foreach ($this->assets as $asset) {
            $data = $this->map($asset);
            foreach ($data as $colIndex => $value) {
                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue($column . $rowIndex, $value);
                
                // Add color coding for status column (column I, index 8)
                if ($colIndex == 8) { // Status column
                    $statusColor = $this->getStatusColor($value);
                    if ($statusColor) {
                        $sheet->getStyle($column . $rowIndex)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $statusColor],
                            ],
                            'font' => [
                                'color' => ['rgb' => 'FFFFFF'],
                                'bold' => true,
                            ],
                        ]);
                    }
                }
            }
            $rowIndex++;
        }

        // Style all data cells
        $lastRow = $rowIndex - 1;
        if ($lastRow >= $dataStartRow) {
            $sheet->getStyle('A' . $dataStartRow . ':N' . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
                'font' => [
                    'size' => 10,
                ],
            ]);
        }

        // Center align specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L:N')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-filter for data rows only
        $sheet->setAutoFilter('A' . $headerRow . ':N' . $headerRow);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);
        for ($i = 3; $i < $currentRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(25);
        }
        $sheet->getRowDimension($headerRow)->setRowHeight(20);

        // Auto-size columns based on content
        foreach (range('A', 'N') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }

    private function getStatusColor($status)
    {
        switch (strtoupper($status)) {
            case 'IN USE':
                return '10B981'; // Green-500
            case 'UNDER REPAIR':
                return 'F59E0B'; // Yellow-500
            case 'DISPOSED':
                return 'EF4444'; // Red-500
            case 'MAINTENANCE':
                return '8B5CF6'; // Purple-500
            default:
                return null;
        }
    }


    public function sheets(): array
    {
        return [
            'Computer Lab Inventory' => $this,
            'Summary' => new PaascuSummarySheet($this->assets, $this->filters),
        ];
    }
}

class PaascuSummarySheet implements FromCollection, WithMapping, WithStyles, WithTitle
{
    protected $assets;
    protected $filters;

    public function __construct($assets, $filters = [])
    {
        $this->assets = $assets;
        $this->filters = $filters;
    }

    public function collection()
    {
        // Group assets by category and location
        $summary = collect();
        
        // Category summary
        $categorySummary = $this->assets->groupBy('category.name')->map(function ($items, $category) {
            return [
                'type' => 'Category',
                'name' => $category,
                'count' => $items->count(),
                'value' => $items->sum('purchase_price'),
                'in_use' => $items->where('status', 'IN USE')->count(),
                'under_repair' => $items->where('status', 'UNDER REPAIR')->count(),
                'disposed' => $items->where('status', 'DISPOSED')->count(),
            ];
        });

        // Location summary
        $locationSummary = $this->assets->groupBy(function ($item) {
            return $item->location ? $item->location->building . '-' . $item->location->floor . '-' . $item->location->room_number : 'Unknown';
        })->map(function ($items, $location) {
            return [
                'type' => 'Location',
                'name' => $location,
                'count' => $items->count(),
                'value' => $items->sum('purchase_price'),
                'in_use' => $items->where('status', 'IN USE')->count(),
                'under_repair' => $items->where('status', 'UNDER REPAIR')->count(),
                'disposed' => $items->where('status', 'DISPOSED')->count(),
            ];
        });

        return $categorySummary->merge($locationSummary);
    }

    public function map($item): array
    {
        return [
            $item['type'],
            $item['name'],
            $item['count'],
            '₱' . number_format($item['value'], 2),
            $item['in_use'],
            $item['under_repair'],
            $item['disposed']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Add header section
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'INVENTORY SUMMARY REPORT');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
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

        // Add subtitle with generation date
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Generated on ' . \Carbon\Carbon::now()->format('F d, Y \a\t h:i A'));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => '374151'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'], // Gray-100
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Add date range filter information if available
        $currentRow = 3;
        if (!empty($this->filters['start_date']) || !empty($this->filters['end_date'])) {
            $sheet->mergeCells('A' . $currentRow . ':G' . $currentRow);
            $dateRangeText = 'Date Range: ';
            
            if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
                $dateRangeText .= \Carbon\Carbon::parse($this->filters['start_date'])->format('M d, Y') . ' to ' . \Carbon\Carbon::parse($this->filters['end_date'])->format('M d, Y');
            } elseif (!empty($this->filters['start_date'])) {
                $dateRangeText .= 'From ' . \Carbon\Carbon::parse($this->filters['start_date'])->format('M d, Y') . ' onwards';
            } elseif (!empty($this->filters['end_date'])) {
                $dateRangeText .= 'Up to ' . \Carbon\Carbon::parse($this->filters['end_date'])->format('M d, Y');
            }
            
            $sheet->setCellValue('A' . $currentRow, $dateRangeText);
            $sheet->getStyle('A' . $currentRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => '1F2937'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'], // Gray-200
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $currentRow++;
        }

        // Manually set the column headers in the correct row
        $headerRow = $currentRow;
        $headers = [
            'Type',
            'Name',
            'Total Count',
            'Total Value',
            'In Use',
            'Under Repair',
            'Disposed'
        ];

        // Set header values
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . $headerRow, $header);
        }

        // Style the column headers
        $sheet->getStyle('A' . $headerRow . ':G' . $headerRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
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
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '374151'],
                ],
            ],
        ]);

        // Manually place the data starting from the next row
        $dataStartRow = $headerRow + 1;
        $rowIndex = $dataStartRow;
        
        foreach ($this->collection() as $item) {
            $data = $this->map($item);
            foreach ($data as $colIndex => $value) {
                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue($column . $rowIndex, $value);
            }
            $rowIndex++;
        }

        // Style all data cells
        $lastRow = $rowIndex - 1;
        if ($lastRow >= $dataStartRow) {
            $sheet->getStyle('A' . $dataStartRow . ':G' . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
                'font' => [
                    'size' => 10,
                ],
            ]);
        }

        // Center align specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-filter for data rows only
        $sheet->setAutoFilter('A' . $headerRow . ':G' . $headerRow);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);
        if ($currentRow > 3) {
            $sheet->getRowDimension(3)->setRowHeight(20);
        }
        $sheet->getRowDimension($headerRow)->setRowHeight(20);

        // Auto-size columns based on content
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }


    public function title(): string
    {
        return 'Summary';
    }
} 