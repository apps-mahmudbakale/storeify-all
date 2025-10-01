<?php

namespace App\Exports;

use App\Models\Sale;
use NumberFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EndDayReportExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $sales = Sale::whereRaw('Date(sales.created_at) = CURRENT_DATE')
            ->leftJoin('products', 'sales.product_id', '=', 'products.id')
            ->leftJoin('users', 'sales.user_id', '=', 'users.id')
            ->select('products.name as product', 'sales.amount', 'sales.created_at', 'sales.quantity', 'sales.invoice', 'users.name as user', 'sales.buyer_name', 'sales.buyer_dept')
            ->get();


        return view('reports.endDay_report_excel', ['sales' => $sales]);
    }
}
