<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VAT Report - {{ app(App\Settings\StoreSettings::class)->store_name }}</title>
    @livewireStyles
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body class="hold-transition skin-blue layout-top-nav">
<div class="invoice p-3 mb-3">

    <div class="row">
        <div class="col-lg-12 text-center">
            <img src="{{ !empty(app(App\Settings\StoreSettings::class)->store_logo) ? asset('storage/store/'.app(App\Settings\StoreSettings::class)->store_logo) : asset('logo.png') }}"
                alt="" width="100px" class="img img-rounded">
            <h3>{{ app(App\Settings\StoreSettings::class)->store_name }}</h3>
            <h4>VAT / TAX REPORT</h4>
            <small>Generated: {{ date('d/m/Y H:i') }}</small>
        </div>
    </div>

    <hr>

    <div class="col-sm-12">
        <form action="{{ route('app.vat.report.filter') }}" method="POST" class="row">
            @csrf
            <div class="col-md-3">
                From
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-md-3">
                To
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <br>
                <button type="submit" class="btn btn-success">Filter</button>
                <a href="{{ route('app.vat.report') }}" class="btn btn-default">Reset</a>
            </div>
            <div class="col-md-3 text-right">
                <br>
                <a href="{{ route('app.vat.report.excel') }}?from={{ request('from') }}&to={{ request('to') }}"
                   class="btn btn-warning d-print-none">
                    <i class="fa fa-file-excel"></i> Export Excel
                </a>
                <button onclick="window.print()" class="btn btn-info d-print-none">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </form>
    </div>

    <br>

    <div class="row">
        <div class="col-lg-12 table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Product</th>
                        <th>VAT %</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
                        <th>VAT Amount</th>
                        <th>Sold By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->invoice }}</td>
                        <td>{{ $sale->product }}</td>
                        <td><span class="badge badge-info">{{ $sale->vat_percentage }}%</span></td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($sale->price, 2) }}</td>
                        <td>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($sale->amount, 2) }}</td>
                        <td><strong>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($sale->vat_amount, 2) }}</strong></td>
                        <td>{{ $sale->user }}</td>
                        <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted">No taxable sales found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 offset-lg-6">
            <table class="table table-bordered">
                <tr>
                    <th>Total Taxable Sales:</th>
                    <td><strong>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($totalSales, 2) }}</strong></td>
                </tr>
                <tr class="table-warning">
                    <th>Total VAT Collected (7.5%):</th>
                    <td><strong>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($totalVat, 2) }}</strong></td>
                </tr>
                <tr>
                    <th>Net Sales (excl. VAT):</th>
                    <td><strong>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($totalSales - $totalVat, 2) }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row d-print-none">
        <div class="col-lg-12">
            <a href="{{ route('app.dashboard') }}" class="btn btn-primary">
                <i class="fa fa-home"></i> Go Home
            </a>
        </div>
    </div>

</div>
</body>
</html>
