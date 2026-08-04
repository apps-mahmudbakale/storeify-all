@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Stock Closing Report - {{ Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Stock Closing Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <a href="{{ route('app.stock-closing.index', ['month' => $month, 'year' => $year]) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button onclick="window.print()" class="btn btn-primary float-right">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-boxes"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Closings</span>
                                <span class="info-box-number">{{ $closings->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $totalVariance >= 0 ? 'success' : 'danger' }}"><i class="fas fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Variance</span>
                                <span class="info-box-number">{{ $totalVariance >= 0 ? '+' : '' }}{{ $totalVariance }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Shortages</span>
                                <span class="info-box-number">{{ $closings->where('variance', '<', 0)->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-plus-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Surpluses</span>
                                <span class="info-box-number">{{ $closings->where('variance', '>', 0)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detailed Stock Closing Report</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Opening</th>
                                    <th class="text-center">Dispensed</th>
                                    <th class="text-center">Expected</th>
                                    <th class="text-center">Actual</th>
                                    <th class="text-center">Variance</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($closings as $closing)
                                    <tr>
                                        <td>
                                            <a href="{{ route('app.stock-closing.show', ['product' => $closing->product_id, 'month' => $month, 'year' => $year]) }}">
                                                <strong>{{ $closing->product->name }}</strong>
                                            </a>
                                        </td>
                                        <td class="text-center">{{ $closing->opening_qty }}</td>
                                        <td class="text-center">{{ $closing->qty_dispensed }}</td>
                                        <td class="text-center">{{ $closing->expected_closing }}</td>
                                        <td class="text-center text-success"><strong>{{ $closing->closing_qty }}</strong></td>
                                        <td class="text-center">
                                            <strong class="text-{{ $closing->variance == 0 ? 'success' : ($closing->variance > 0 ? 'info' : 'danger') }}">
                                                {{ $closing->variance >= 0 ? '+' : '' }}{{ $closing->variance }}
                                            </strong>
                                        </td>
                                        <td class="text-center">
                                            @if ($closing->variance == 0)
                                                <span class="badge badge-success">✓ Balanced</span>
                                            @elseif ($closing->variance > 0)
                                                <span class="badge badge-info">Surplus</span>
                                            @else
                                                <span class="badge badge-danger">Shortage</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <em>No stock closings recorded for this period</em>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Discrepancies -->
                @if ($closings->where('variance', '!=', 0)->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header bg-warning">
                            <h3 class="card-title">Items with Variance</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th class="text-center">Amount</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($closings->where('variance', '!=', 0) as $closing)
                                        <tr>
                                            <td>{{ $closing->product->name }}</td>
                                            <td>
                                                @if ($closing->variance > 0)
                                                    <span class="badge badge-info">Surplus</span>
                                                @else
                                                    <span class="badge badge-danger">Shortage</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ $closing->variance >= 0 ? '+' : '' }}{{ $closing->variance }}</strong>
                                            </td>
                                            <td>{{ $closing->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
