<?php

namespace App\Classes;

use Carbon\Carbon;
use NumberFormatter;
use Illuminate\Support\Facades\DB;

class CustomReport
{

    public function filter($request)
    {
        $query = DB::table('sales')
            ->select('sales.*', 'products.name as product', 'users.name as user', 'sales.buyer_name', 'sales.buyer_dept')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->join('users', 'users.id', '=', 'sales.user_id');

        if ($request->has('user') && !empty($request->user)) {
            $query->where('users.id', $request->user);
        }

        if ($request->has('buyer_name') && !empty($request->buyer_name)) {
            $query->where('sales.buyer_name', 'LIKE', '%' . $request->buyer_name . '%');
        }

        if ($request->has('buyer_dept') && !empty($request->buyer_dept)) {
            $query->where('sales.buyer_dept', 'LIKE', '%' . $request->buyer_dept . '%');
        }

        if ($request->has('from') && !empty($request->from) && $request->has('to') && !empty($request->to)) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay();
            $query->whereBetween('sales.created_at', array($startDate, $endDate));
        }

        // Build sum query
        $sum = DB::table('sales')
            ->selectRaw('sum(sales.amount) as total')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->join('users', 'users.id', '=', 'sales.user_id');

        // Apply date filter to sum if provided
        if ($request->has('from') && !empty($request->from) && $request->has('to') && !empty($request->to)) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay();
            $sum->whereBetween('sales.created_at', array($startDate, $endDate));
        }

        if ($request->has('buyer_name') && !empty($request->buyer_name)) {
            $sum->where('sales.buyer_name', 'LIKE', '%' . $request->buyer_name . '%');
        }

        if ($request->has('buyer_dept') && !empty($request->buyer_dept)) {
            $sum->where('sales.buyer_dept', 'LIKE', '%' . $request->buyer_dept . '%');
        }

        $sumResult = $sum->first();
        $total = $sumResult->total ?? 0;
        
        $inWords = new NumberFormatter("En", NumberFormatter::SPELLOUT);
        $words = $total > 0 ? $inWords->format($total) : 'zero';
        
        return  [
            'filter' =>  $query->get(),
            'words' => $words,
            'sum' => $total
        ];
    }
}
