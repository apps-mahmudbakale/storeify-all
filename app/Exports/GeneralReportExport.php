<?php

namespace App\Exports;

use App\Models\Sale;
use NumberFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GeneralReportExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
    //  */
    // public function headings(): array
    // {
    //     return [
    //         'Product',
    //         'Quantity',
    //         'Amount',
    //         'Invoice',
    //         'Sold By',
    //     ];
    // }
    public function view(): View
    {
        $sales = Sale::leftJoin('products', 'sales.product_id', '=', 'products.id')
            ->leftJoin('users', 'sales.user_id', '=', 'users.id')
            ->select('products.name as product', 'sales.amount', 'sales.created_at', 'sales.quantity', 'sales.invoice', 'users.name as user', 'sales.buyer_name', 'sales.buyer_dept')
            ->get();
        $sum    = DB::table('sales')
            ->selectRaw('sum(amount) as total')
            ->first();
        $inWords = new NumberFormatter("En", NumberFormatter::SPELLOUT);
        $words = $inWords->format($sum->total);

        return view('reports.general_report_excel', ['sales' => $sales, 'sum' => $sum, 'words' => $words]);
    }
}
