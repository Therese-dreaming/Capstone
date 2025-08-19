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

class PaascuInventoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithMultipleSheets
{
    protected $assets;

    public function __construct($assets)
    {
        $this->assets = $assets;
    }

    public function collection()
    {
        return $this->assets;
    }

    public function headings(): array
    {
        return [
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
            'Last Maintenance',
            'Next Maintenance'
        ];
    }

    public function map($asset): array
    {
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
            $asset->last_maintenance ? \Carbon\Carbon::parse($asset->last_maintenance)->format('M d, Y') : 'N/A',
            $asset->next_maintenance ? \Carbon\Carbon::parse($asset->next_maintenance)->format('M d, Y') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:O1')->applyFromArray([
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
        $sheet->getStyle('A2:O' . ($this->assets->count() + 1))->applyFromArray([
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
        $sheet->getStyle('G:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L:O')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-filter
        $sheet->setAutoFilter('A1:O1');

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Serial Number
            'B' => 25, // Equipment Name
            'C' => 20, // Category
            'D' => 12, // Building
            'E' => 8,  // Floor
            'F' => 8,  // Room
            'G' => 15, // Purchase Price
            'H' => 15, // Acquisition Date
            'I' => 12, // Status
            'J' => 20, // Vendor
            'K' => 20, // Model
            'L' => 25, // Specifications
            'M' => 15, // Warranty Expiry
            'N' => 15, // Last Maintenance
            'O' => 15, // Next Maintenance
        ];
    }

    public function sheets(): array
    {
        return [
            'Computer Lab Inventory' => $this,
            'Summary' => new PaascuSummarySheet($this->assets),
        ];
    }
}

class PaascuSummarySheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $assets;

    public function __construct($assets)
    {
        $this->assets = $assets;
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

    public function headings(): array
    {
        return [
            'Type',
            'Name',
            'Total Count',
            'Total Value',
            'In Use',
            'Under Repair',
            'Disposed'
        ];
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
        // Style the header row
        $sheet->getStyle('A1:G1')->applyFromArray([
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
        $sheet->getStyle('A2:G' . ($this->collection()->count() + 1))->applyFromArray([
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
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-filter
        $sheet->setAutoFilter('A1:G1');

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Type
            'B' => 30, // Name
            'C' => 15, // Total Count
            'D' => 15, // Total Value
            'E' => 15, // In Use
            'F' => 20, // Under Repair
            'G' => 20, // Disposed
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
} 