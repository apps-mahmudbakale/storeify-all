<!DOCTYPE html>
<html>
<head>
    <title>Category Stock & Sales Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ app(App\Settings\StoreSettings::class)->store_name }}</div>
        <div>Category Stock & Sales Report</div>
        <div style="font-size: 10px; color: #666;">Generated on: {{ date('d M Y, h:i A') }}</div>
    </div>

    <h3>Current Stock by Category</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-center">Items Count</th>
                <th class="text-center">Quantity</th>
                <th class="text-right">Value (Cost)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalCost = 0; @endphp
            @foreach($stockReport as $row)
                @php $totalCost += $row->total_cost_value; @endphp
                <tr>
                    <td>{{ $row->category ?: 'Uncategorized' }}</td>
                    <td class="text-center">{{ number_format($row->total_items) }}</td>
                    <td class="text-center">{{ number_format($row->total_qty) }}</td>
                    <td class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($row->total_cost_value) }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td>TOTAL</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($totalCost) }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Sales Performance by Category</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-center">Sales Count</th>
                <th class="text-center">Items Sold</th>
                <th class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($salesReport) && count($salesReport) > 0)
                @php $totalRevenue = 0; @endphp
                @foreach($salesReport as $row)
                    @php $totalRevenue += $row->total_revenue; @endphp
                    <tr>
                        <td>{{ $row->category ?: 'Uncategorized' }}</td>
                        <td class="text-center">{{ number_format($row->total_sales) }}</td>
                        <td class="text-center">{{ number_format($row->items_sold) }}</td>
                        <td class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($row->total_revenue) }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td>TOTAL</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($totalRevenue) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" class="text-center">No sales data available for this range/category.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
