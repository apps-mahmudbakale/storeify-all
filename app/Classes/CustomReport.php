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
            ->select('sales.*', 'products.name as product', 'users.name as user')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->join('users', 'users.id', '=', 'sales.user_id');

        if ($request->filled('user')) {
            $query->where('users.id', $request->user);
        }

        if ($request->filled('product')) {
            $query->where('sales.product_id', $request->product);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('sales.created_at', [
                Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay(),
                Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay(),
            ]);
        }

        $sumQuery = DB::table('sales')
            ->selectRaw('sum(sales.amount) as total, sum(sales.quantity) as qty_sum')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->join('users', 'users.id', '=', 'sales.user_id');

        if ($request->filled('user')) {
            $sumQuery->where('users.id', $request->user);
        }

        if ($request->filled('product')) {
            $sumQuery->where('sales.product_id', $request->product);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $sumQuery->whereBetween('sales.created_at', [
                Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay(),
                Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay(),
            ]);
        }

        $sumResult = $sumQuery->first();
        $total     = $sumResult->total ?? 0;
        $qty_sum   = $sumResult->qty_sum ?? 0;

        $inWords = new NumberFormatter("En", NumberFormatter::SPELLOUT);
        $words   = $total > 0 ? $inWords->format($total) : 'zero';

        return [
            'filter'  => $query->get(),
            'words'   => $words,
            'sum'     => $total,
            'qty_sum' => $qty_sum,
        ];
    }
}
