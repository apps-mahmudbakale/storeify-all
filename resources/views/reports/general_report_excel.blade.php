<table>
    <thead>
    <tr>
        <th>S/N</th>
        <th>Invoice</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Sold Rate</th>
        <th>Amount</th>
        <th>Sold By</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sales as $sale)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $sale->invoice }}</td>
            <td>{{ $sale->product }}</td>
            <td>{{ $sale->quantity }}</td>
            <td>{{ $sale->price }}</td>
            <td>{{ $sale->amount }}</td>
            <td>{{ $sale->user }}</td>
            <td>{{ \Carbon\Carbon::parse($sale->created_at)->toFormattedDayDateString() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<table>
    <tr>
        <th>Total:</th>
        <td>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($sum->total, 2) }}</td>
    </tr>
    <tr>
        <th>Amount In Words:</th>
        <td>{{ ucfirst($words) }}</td>
    </tr>
</table>

