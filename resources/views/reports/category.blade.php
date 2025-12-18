@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Category Stock & Sales Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Category Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Filter Row -->
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Filter Sales by Date</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('app.category.report') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>From</label>
                                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>To</label>
                                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary">Filter Sales</button>
                                    <a href="{{ route('app.category.report.view') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <!-- Current Stock Table -->
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Current Stock by Category</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-center">Items</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Value (Cost)</th>
                                        <th class="text-right">Value (Retail)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalCost = 0;
                                        $totalRetail = 0;
                                    @endphp
                                    @foreach($stockReport as $row)
                                        @php
                                            $totalCost += $row->total_cost_value;
                                            $totalRetail += $row->total_retail_value;
                                        @endphp
                                        <tr>
                                            <td>{{ $row->category ?: 'Uncategorized' }}</td>
                                            <td class="text-center">{{ number_format($row->total_items) }}</td>
                                            <td class="text-center">{{ number_format($row->total_qty) }}</td>
                                            <td class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($row->total_cost_value) }}</td>
                                            <td class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($row->total_retail_value) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th>TOTAL</th>
                                        <th class="text-center">-</th>
                                        <th class="text-center">-</th>
                                        <th class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($totalCost) }}</th>
                                        <th class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($totalRetail) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sales Performance Table -->
                <div class="col-md-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Sales Performance by Category {{ isset($salesReport) ? '(Filtered)' : '(All Time)' }}</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-center">Sales Count</th>
                                        <th class="text-center">Items Sold</th>
                                        <th class="text-right">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($salesReport))
                                        @php $totalRevenue = 0; @endphp
                                        @forelse($salesReport as $row)
                                            @php $totalRevenue += $row->total_revenue; @endphp
                                            <tr>
                                                <td>{{ $row->category ?: 'Uncategorized' }}</td>
                                                <td class="text-center">{{ number_format($row->total_sales) }}</td>
                                                <td class="text-center">{{ number_format($row->items_sold) }}</td>
                                                <td class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($row->total_revenue) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No sales found for the selected period.</td>
                                            </tr>
                                        @endforelse
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Please use the date filter to see sales performance.</td>
                                        </tr>
                                    @endif
                                </tbody>
                                @if(isset($salesReport) && $salesReport->count() > 0)
                                <tfoot>
                                    <tr class="bg-light">
                                        <th>TOTAL</th>
                                        <th class="text-center">-</th>
                                        <th class="text-center">-</th>
                                        <th class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($totalRevenue) }}</th>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
