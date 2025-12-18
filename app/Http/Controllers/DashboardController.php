<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
use NumberFormatter;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Classes\CustomReport;
use App\Exports\EndDayReportExport;
use Illuminate\Support\Facades\DB;
use App\Exports\GeneralReportExport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::count();
        $products = Product::count();
        $sales = Sale::count();
        $today_sales = Sale::whereDate('created_at', Carbon::today())->count();
        $today_cash = Sale::whereDate('created_at', Carbon::today())->sum('amount');
        $sales_cash = Sale::sum('amount');
        $products_cash_cost = Product::all()->sum(function ($t) {
            return $t->buying_price * $t->qty;
        });
        $products_cash_selling = Product::all()->sum(function ($t) {
            return $t->selling_price * $t->qty;
        });
        $query = DB::table('sales')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->select(DB::raw('SUM(products.selling_price * sales.quantity) - SUM(products.buying_price * sales.quantity) as profit'))->first();
        $profit = $query->profit;
        // dd($query->profit);
        $expiry_threshold = Carbon::now()->addDays(7);

    $expiring_products = DB::table('products')
        ->whereNotNull('expiry_date')
        ->whereDate('expiry_date', '<=', $expiry_threshold)
        ->orderBy('expiry_date', 'asc')
        ->paginate(5);

    $low_stock_products = Product::whereRaw('qty <= min_qty')
        ->orderBy('qty')
        ->paginate(5);
        return view('home', compact('users', 'products', 'sales', 'today_sales', 'today_cash', 'sales_cash', 'products_cash_cost', 'products_cash_selling', 'profit', 'expiring_products', 'low_stock_products'));
    }

    public function generalReport()
    {
        $sales = DB::table('sales')
            ->select('sales.*', 'products.name as product', 'users.name as user', 'sales.buyer_name', 'sales.buyer_dept')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->orderBy('sales.created_at', 'asc')
            ->get();
        $sum    = DB::table('sales')
            ->selectRaw('sum(amount) as total')
            ->first();
        $inWords = new NumberFormatter("En", NumberFormatter::SPELLOUT);
        $words = $inWords->format($sum->total);
        return view('reports.general', compact('sales', 'sum', 'words'));
    }

    public function exportGeneralReportExcel()
    {
        return Excel::download(new GeneralReportExport, 'K7-Pharmacy-General-Rport.xlsx');
    }

    public function exportGeneralReportPdf()
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
        $pdf = PDF::loadView('reports.general_report_pdf', compact('sales', 'sum', 'words'));
        return $pdf->download('K7-Pharmacy-GeneralReport.pdf');

    }

    public function endOfDayReport()
    {
        $sales = DB::table('sales')
            ->select('sales.*', 'products.name as product', 'users.name as user', 'sales.buyer_name', 'sales.buyer_dept')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->whereRaw('Date(sales.created_at) = CURRENT_DATE')
            ->get();
        return view('reports.endDay', compact('sales'));
    }

    public function exportEndOfDayReportExcel()
    {
        return Excel::download(new EndDayReportExport, 'K7-Pharmacy-End-of-Day-Report-'.date('d-m-Y').'.xlsx');
    }

    public function exportEndOfDayReportPdf()
    {
        $sales = DB::table('sales')
        ->select('sales.*', 'products.name as product', 'users.name as user', 'sales.buyer_name', 'sales.buyer_dept')
        ->join('products', 'products.id', '=', 'sales.product_id')
        ->join('users', 'users.id', '=', 'sales.user_id')
        ->whereRaw('Date(sales.created_at) = CURRENT_DATE')
        ->get();
        $pdf = PDF::loadView('reports.endDay_report_pdf', compact('sales'));
        return $pdf->download('K7-Pharmacy-EndOfDayReport-'.date('d-m-Y').'.pdf');
    }

    public function customReportView()
    {
        $products = Product::get();
        $users = User::where('name', '!=', 'Admin')->get();
        return view('reports.custom', compact('products', 'users'));
    }
    public function customReport(Request $request, CustomReport $report)
    {
        $reports = $report->filter($request);
        $words = $reports['words'];
        $sales = $reports['filter'];
        $sum = $reports['sum'];
        return view('reports.custom', compact('sales', 'words', 'sum'));
    }
    public function customReportExcel($data)
    {

    }
    public function showChangePasswordGet()
    {
        return view('change-password');
    }
    public function changePasswordPost(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);
        #Match The Old Password
        if (Hash::check($request->current_password, auth()->user()->password)) {
            // dd(Hash::make($request->new_password));
            DB::table('users')
                ->where('id', auth()->user()->id)
                ->update([
                    'password' => Hash::make($request->new_password)
                ]);
            return redirect()->back()->with("success", "Password successfully changed!");
        } else {
            return back()->with("error", "Old Password Doesn't match!");
        }
    }

    public function sync()
    {
        return view('sync');
    }

    public function categoryReportView()
    {
        $categories = Product::select('product_category')
            ->whereNotNull('product_category')
            ->distinct()
            ->pluck('product_category');

        $stockReport = Product::select('product_category as category')
            ->selectRaw('count(*) as total_items')
            ->selectRaw('sum(qty) as total_qty')
            ->selectRaw('sum(qty * buying_price) as total_cost_value')
            ->groupBy('product_category')
            ->get();

        $salesReport = DB::table('sales')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->select('products.product_category as category')
            ->selectRaw('count(sales.id) as total_sales')
            ->selectRaw('sum(sales.quantity) as items_sold')
            ->selectRaw('sum(sales.amount) as total_revenue')
            ->whereDate('sales.created_at', Carbon::today())
            ->groupBy('products.product_category')
            ->get();

        return view('reports.category', compact('stockReport', 'salesReport', 'categories'));
    }

    public function categoryReport(Request $request)
    {
        $categories = Product::select('product_category')
            ->whereNotNull('product_category')
            ->distinct()
            ->pluck('product_category');

        $stockQuery = Product::select('product_category as category')
            ->selectRaw('count(*) as total_items')
            ->selectRaw('sum(qty) as total_qty')
            ->selectRaw('sum(qty * buying_price) as total_cost_value');

        if ($request->has('category') && !empty($request->category)) {
            $stockQuery->where('product_category', $request->category);
        }

        $stockReport = $stockQuery->groupBy('product_category')->get();

        $salesQuery = DB::table('sales')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->select('products.product_category as category')
            ->selectRaw('count(sales.id) as total_sales')
            ->selectRaw('sum(sales.quantity) as items_sold')
            ->selectRaw('sum(sales.amount) as total_revenue');

        if ($request->has('from') && !empty($request->from) && $request->has('to') && !empty($request->to)) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay();
            $salesQuery->whereBetween('sales.created_at', [$startDate, $endDate]);
        }

        if ($request->has('category') && !empty($request->category)) {
            $salesQuery->where('products.product_category', $request->category);
        }

        $salesReport = $salesQuery->groupBy('products.product_category')->get();

        return view('reports.category', compact('stockReport', 'salesReport', 'categories'));
    }

    public function exportCategoryReportExcel(Request $request)
    {
        return Excel::download(new \App\Exports\CategoryReportExport($request->all()), 'Category-Report.xlsx');
    }

    public function exportCategoryReportPdf(Request $request)
    {
        $category = $request->category ?? null;
        $from = $request->from ?? null;
        $to = $request->to ?? null;

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

        $pdf = PDF::loadView('reports.category_report_pdf', compact('stockReport', 'salesReport'));
        return $pdf->download('Category-Report-' . date('d-m-Y') . '.pdf');
    }
}
