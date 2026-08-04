@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Stock Closing Details</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.stock-closing.index', ['month' => $month, 'year' => $year]) }}">Stock Closing</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title">{{ $product->name }}</h3>
                                <div class="card-tools">
                                    <a href="{{ route('app.stock-closing.create', ['product' => $product->id, 'month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-light">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <strong>Period:</strong> {{ $periodStart->format('d M Y') }} - {{ $periodEnd->format('d M Y') }}
                                    <br>
                                    <strong>Recorded by:</strong> {{ $closing->user->name }}
                                    <br>
                                    <strong>Date:</strong> {{ $closing->created_at->format('d M Y H:i') }}
                                </div>

                                <h5>Stock Calculation</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Opening Qty</strong></td>
                                        <td class="text-right"><strong>{{ $closing->opening_qty }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Less: Total Dispensed</strong></td>
                                        <td class="text-right"><strong>{{ $closing->qty_dispensed }}</strong></td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><strong>Expected Closing</strong></td>
                                        <td class="text-right"><strong>{{ $closing->expected_closing }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Actual Closing</strong></td>
                                        <td class="text-right"><strong class="text-success">{{ $closing->closing_qty }}</strong></td>
                                    </tr>
                                    <tr class="table-{{ $closing->variance == 0 ? 'success' : ($closing->variance > 0 ? 'info' : 'danger') }}">
                                        <td><strong>Variance</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $closing->variance >= 0 ? '+' : '' }}{{ $closing->variance }}
                                                @if ($closing->variance == 0)
                                                    <span class="badge badge-success">✓ Balanced</span>
                                                @elseif ($closing->variance > 0)
                                                    <span class="badge badge-info">Surplus</span>
                                                @else
                                                    <span class="badge badge-danger">Shortage</span>
                                                @endif
                                            </strong>
                                        </td>
                                    </tr>
                                </table>

                                @if ($closing->notes)
                                    <div class="alert alert-warning">
                                        <strong>Notes:</strong>
                                        <p>{{ $closing->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar: Sales History -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h3 class="card-title">Dispense History</h3>
                                <span class="badge badge-light float-right">{{ $salesHistory->count() }} items</span>
                            </div>
                            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                                @forelse ($salesHistory as $sale)
                                    <div class="timeline-item">
                                        <i class="fas fa-receipt bg-blue"></i>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">
                                                <strong>Qty: {{ $sale->qty_changed }}</strong>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> {{ $sale->user->name ?? 'System' }}
                                            </small>
                                            <br>
                                            @if ($sale->buyer_name)
                                                <small class="text-muted">
                                                    <i class="fas fa-hospital-user"></i> {{ $sale->buyer_name }}
                                                    @if ($sale->buyer_dept)
                                                        ({{ $sale->buyer_dept }})
                                                    @endif
                                                </small>
                                                <br>
                                            @endif
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> {{ $sale->created_at->format('d M Y H:i') }}
                                            </small>
                                            <br>
                                            @if ($sale->invoice)
                                                <small class="badge badge-primary">{{ $sale->invoice }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-info">
                                        No dispenses recorded for this period
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
