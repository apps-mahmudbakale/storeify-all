<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CategoryReportExport implements FromView
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $category = $this->filters['category'] ?? null;
        $from = $this->filters['from'] ?? null;
        $to = $this->filters['to'] ?? null;

        $stockQuery = Product::select('product_category as category')
            ->selectRaw('count(*) as total_items')
            ->selectRaw('sum(qty) as total_qty')
            ->selectRaw('sum(qty * buying_price) as total_cost_value');

        if ($category) {
            $stockQuery->where('product_category', $category);
        }

        $stockReport = $stockQuery->groupBy('product_category')->get();

        $salesQuery = DB::table('sales')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->select('products.product_category as category')
            ->selectRaw('count(sales.id) as total_sales')
            ->selectRaw('sum(sales.quantity) as items_sold')
            ->selectRaw('sum(sales.amount) as total_revenue');

        if ($from && $to) {
            $startDate = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
            $salesQuery->whereBetween('sales.created_at', [$startDate, $endDate]);
        }

        if ($category) {
            $salesQuery->where('products.product_category', $category);
        }

        $salesReport = $salesQuery->groupBy('products.product_category')->get();

        return view('reports.category_report_excel', compact('stockReport', 'salesReport'));
    }
}
