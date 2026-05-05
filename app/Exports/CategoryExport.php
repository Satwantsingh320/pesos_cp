<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Customer;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CategoryExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    public $sortOrder = 'desc';
    public $sortEntity = 'customers.id';

    public function collection()
    {
        $export_request = Cache::get(env('EXPORT_CACHE_KEY'));
        $sortOrder  = $export_request['sortOrder'] ?? $this->sortOrder;
        $sortEntity = $export_request['sortEntity'] ?? $this->sortEntity;
        $keyword    = $export_request['keyword'] ?? null;
        $status     = $export_request['status'] ?? null;
        $from_date  = $export_request['from_date'] ?? null;
        $to_date    = $export_request['to_date'] ?? null;

        $baseQuery = Category::query();
            // ->join('companies', 'vendors.company_id', '=', 'companies.id')
            // ->join('plan_types', 'vendors.plan_type_id', '=', 'plan_types.id');

        if ($keyword) {
            $baseQuery->where(function($q) use ($keyword) {
                $q->where('category.name', 'LIKE', "%{$keyword}%");
            });
        }

        if ($status) {
            $baseQuery->where('status', $status);
        }


        if ($from_date && $to_date) {
            $baseQuery->whereBetween('category.created_at', [$from_date, $to_date]);
        }

        $query = (clone $baseQuery)
            ->select(
                'category.*',
            )
            ->orderBy($sortEntity, $sortOrder);

        $results = $query->get();
        return $results;
    }

    public function headings(): array
    {
        return [
            'Category Name',
            'Status',
            'Date & Time'
        ];
    }

    public function map($result): array
    {
        return [
            $result->name ?? 'N/A',
            ($result->is_active == 1) ? 'Active' : 'Inactive',
            $result->created_at->format('d-M-Y h:i A') ?? 'N/A',
        ];
    }
      public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                /** 🔒 Freeze header row */
                $event->sheet->freezePane('A2');

                /** 🎨 Header row styling */
                $event->sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '4F81BD', // blue
                        ],
                    ],
                ]);
            },
        ];
    }
}
