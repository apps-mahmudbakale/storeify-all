<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VatReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DB::table('sales')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->where('products.vat_percentage', '>', 0)
            ->select(
                'sales.invoice',
                'products.name as product',
                'products.vat_percentage',
                'sales.quantity',
                'sales.price',
                'sales.amount',
                DB::raw('ROUND(sales.amount * products.vat_percentage / 100, 2) as vat_amount'),
                'users.name as user',
                'sales.created_at'
            );

        if (!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $query->whereBetween('sales.created_at', [
                \Carbon\Carbon::parse($this->filters['from'])->startOfDay(),
                \Carbon\Carbon::parse($this->filters['to'])->endOfDay(),
            ]);
        }

        return $query->orderBy('sales.created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return ['Invoice', 'Product', 'VAT %', 'Qty', 'Unit Price', 'Amount', 'VAT Amount', 'Sold By', 'Date'];
    }

    public function map($row): array
    {
        return [
            $row->invoice,
            $row->product,
            $row->vat_percentage . '%',
            $row->quantity,
            number_format($row->price, 2),
            number_format($row->amount, 2),
            number_format($row->vat_amount, 2),
            $row->user,
            \Carbon\Carbon::parse($row->created_at)->format('d/m/Y'),
        ];
    }
}
