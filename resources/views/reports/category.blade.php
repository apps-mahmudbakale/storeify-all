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
                    <h3 class="card-title">Filter Report</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('app.category.report') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category" class="form-control">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From (Sales Only)</label>
                                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To (Sales Only)</label>
                                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('app.category.report.view') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer no-print">
                    <div class="btn-group">
                        <a href="{{ route('app.category.report.excel', request()->all()) }}" class="btn btn-info shadow-sm">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel/CSV
                        </a>
                        <a href="{{ route('app.category.report.pdf', request()->all()) }}" class="btn btn-danger shadow-sm ml-2">
                            <i class="fas fa-file-pdf mr-1"></i> Export PDF
                        </a>
                        <button onclick="window.print()" class="btn btn-default shadow-sm ml-2">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                    </div>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalCost = 0;
                                    @endphp
                                    @foreach($stockReport as $row)
                                        @php
                                            $totalCost += $row->total_cost_value;
                                        @endphp
                                        <tr>
                                            <td>{{ $row->category ?: 'Uncategorized' }}</td>
                                            <td class="text-center">{{ number_format($row->total_items) }}</td>
                                            <td class="text-center">{{ number_format($row->total_qty) }}</td>
                                            <td class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($row->total_cost_value) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th>TOTAL</th>
                                        <th class="text-center">-</th>
                                        <th class="text-center">-</th>
                                        <th class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($totalCost) }}</th>
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
                            @php
                                $status = '(All Time)';
                                if (request('from') || request('to')) {
                                    $status = '(' . (request('from') ?: '...') . ' to ' . (request('to') ?: '...') . ')';
                                } elseif (isset($salesReport) && !request('category')) {
                                    $status = "(Today's Sales)";
                                }
                                
                                if (request('category')) {
                                    $status .= ' - Category: ' . request('category');
                                }
                            @endphp
                            <h3 class="card-title">Sales Performance by Category {{ $status }}</h3>
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
                                                <td colspan="4" class="text-center text-muted">No sales found for the selection.</td>
                                            </tr>
                                        @endforelse
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Please filter to see sales performance.</td>
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
