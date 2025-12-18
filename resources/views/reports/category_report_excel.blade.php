<table>
    <thead>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold;">Current Stock by Category</th>
        </tr>
        <tr>
            <th>Category</th>
            <th>Items Count</th>
            <th>Quantity</th>
            <th>Value (Cost)</th>
        </tr>
    </thead>
    <tbody>
        @php $totalCost = 0; @endphp
        @foreach($stockReport as $row)
            @php $totalCost += $row->total_cost_value; @endphp
            <tr>
                <td>{{ $row->category ?: 'Uncategorized' }}</td>
                <td>{{ number_format($row->total_items) }}</td>
                <td>{{ number_format($row->total_qty) }}</td>
                <td>{{ number_format($row->total_cost_value) }}</td>
            </tr>
        @endforeach
        <tr>
            <th>TOTAL</th>
            <th>-</th>
            <th>-</th>
            <th>{{ number_format($totalCost) }}</th>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold;">Sales Performance by Category</th>
        </tr>
        <tr>
            <th>Category</th>
            <th>Sales Count</th>
            <th>Items Sold</th>
            <th>Revenue</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($salesReport))
            @php $totalRevenue = 0; @endphp
            @foreach($salesReport as $row)
                @php $totalRevenue += $row->total_revenue; @endphp
                <tr>
                    <td>{{ $row->category ?: 'Uncategorized' }}</td>
                    <td>{{ number_format($row->total_sales) }}</td>
                    <td>{{ number_format($row->items_sold) }}</td>
                    <td>{{ number_format($row->total_revenue) }}</td>
                </tr>
            @endforeach
            <tr>
                <th>TOTAL</th>
                <th>-</th>
                <th>-</th>
                <th>{{ number_format($totalRevenue) }}</th>
            </tr>
        @else
            <tr>
                <td colspan="4">No sales data included in this export.</td>
            </tr>
        @endif
    </tbody>
</table>
