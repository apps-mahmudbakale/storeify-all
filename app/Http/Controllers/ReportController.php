<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Monthly closing stock report.
     *
     * Closing stock at the end of a month is reconstructed backwards from
     * today's on-hand quantity:
     *
     *   closing(D) = products.qty
     *              + sales after D
     *              - batch receipts after D
     *
     * Works for past months because both `sales.created_at` and
     * `product_batches.received_at` are date-stamped. Opening for the month is
     * then closing + sold + received for that month (i.e. the previous
     * month-end close). Approximate for past months; exact for the current
     * month, where closing should always equal `products.qty`.
     */
    public function closingStock(Request $request)
    {
        $month = $this->resolveMonth($request->query('month'));
        $start = $month->copy()->startOfMonth();
        $next = $start->copy()->addMonth();
        $isCurrent = $start->isSameMonth(Carbon::now());

        $category = $request->query('category');
        $search = $request->query('search');

        $products = DB::table('products')
            ->select('id', 'name', 'product_category', 'qty', 'created_at')
            ->when($category, function ($q) use ($category) {
                return $q->where('product_category', $category);
            })
            ->when($search, function ($q) use ($search) {
                return $q->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->get();

        $monthSales = $this->salesByProduct($start, $next);
        $monthReceipts = $this->receiptsByProduct($start, $next);
        $afterSales = $this->salesByProduct($next);
        $afterReceipts = $this->receiptsByProduct($next);
        $batchRemaining = DB::table('product_batches')
            ->select('product_id')->selectRaw('SUM(qty_remaining) s')
            ->groupBy('product_id')
            ->pluck('s', 'product_id');

        $rows = $products->map(function ($p) use ($monthSales, $monthReceipts, $afterSales, $afterReceipts, $batchRemaining, $isCurrent, $start) {
            $afterSales = (int) ($afterSales[$p->id] ?? 0);
            $afterReceipts = (int) ($afterReceipts[$p->id] ?? 0);
            $closing = (int) $p->qty + $afterSales - $afterReceipts;
            $sold = (int) ($monthSales[$p->id] ?? 0);
            $received = (int) ($monthReceipts[$p->id] ?? 0);
            $opening = $closing + $sold - $received;

            if ($isCurrent) {
                $status = 'reconciled';
                if ($closing < 0) {
                    $status = 'negative';
                } elseif ((int) ($batchRemaining[$p->id] ?? 0) !== (int) $p->qty) {
                    $status = 'check';
                }
            } else {
                $status = 'estimate';
                if ($closing < 0) {
                    $status = 'negative';
                }
            }

            return (object) [
                'id' => $p->id,
                'name' => $p->name,
                'category' => $p->product_category,
                'opening' => $opening,
                'received' => $received,
                'sold' => $sold,
                'closing' => $closing,
                'status' => $status,
            ];
        });

        if ($request->query('export') === 'csv') {
            return $this->exportCsv($rows, $start);
        }

        $categories = DB::table('products')
            ->whereNotNull('product_category')
            ->distinct()
            ->orderBy('product_category')
            ->pluck('product_category');

        return view('reports.closing-stock', compact(
            'rows',
            'categories',
            'start',
            'isCurrent'
        ));
    }

    private function resolveMonth(?string $value): Carbon
    {
        if ($value && preg_match('/^\d{4}-\d{2}$/', $value)) {
            try {
                return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
            } catch (\Exception $e) {
                // fall through to current month
            }
        }

        return Carbon::now()->startOfMonth();
    }

    private function salesByProduct(Carbon $from, ?Carbon $to = null)
    {
        return DB::table('sales')
            ->select('product_id')->selectRaw('SUM(quantity) s')
            ->where('created_at', '>=', $from)
            ->when($to, function ($q) use ($to) {
                return $q->where('created_at', '<', $to);
            })
            ->groupBy('product_id')
            ->pluck('s', 'product_id');
    }

    private function receiptsByProduct(Carbon $from, ?Carbon $to = null)
    {
        return DB::table('product_batches')
            ->select('product_id')->selectRaw('SUM(initial_qty) s')
            ->where('received_at', '>=', $from->toDateString())
            ->when($to, function ($q) use ($to) {
                return $q->where('received_at', '<', $to->toDateString());
            })
            ->groupBy('product_id')
            ->pluck('s', 'product_id');
    }

    private function exportCsv($rows, Carbon $start)
    {
        $filename = 'closing-stock-' . $start->format('Y-m') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Product', 'Category', 'Opening', 'Received', 'Sold', 'Closing']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->name,
                    $r->category,
                    $r->opening,
                    $r->received,
                    $r->sold,
                    $r->closing,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}