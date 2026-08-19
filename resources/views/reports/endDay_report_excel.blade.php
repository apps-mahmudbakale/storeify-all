<table class="table table-striped">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Invoice</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Sold Rate</th>
            <th>Amount</th>
            <th>Sold By</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php $sum = 0; ?>
       @foreach($sales as $sale)
        <?php $sum += $sale->amount;?>
        <tr>
        <td>{{$loop->iteration}}</td>
        <td>{{$sale->invoice}}</td>
        <td>{{$sale->product}}</td>
        <td>{{$sale->quantity}}</td>
        <td>{!! app(App\Settings\StoreSettings::class)->currency !!}  {{number_format($sale->price)}}</td>
        <td>{!! app(App\Settings\StoreSettings::class)->currency !!}  {{number_format($sale->amount)}}</td>
        <td>{{$sale->user}}</td>
        {{-- <td>{{$sale->station}}</td> --}}
        <td>{{\Carbon\Carbon::parse($sale->created_at)->toFormattedDayDateString()}}</td>
        </tr>
       @endforeach
    </tbody>
</table>
<tr>
<p></p>
</tr>
<tr>
<p></p>
</tr>
<table class="table">
    <tr>
        <th>Total:</th>
        <td>{!! app(App\Settings\StoreSettings::class)->currency !!}  {{number_format($sum,2)}}</td>
    </tr>
    <tr>
        <th>Amount In Words:</th>
        <?php $inWords = new NumberFormatter("En", NumberFormatter::SPELLOUT);?>
        <td>{{ucfirst($inWords->format($sum))}}</td>
    </tr>
</table>

