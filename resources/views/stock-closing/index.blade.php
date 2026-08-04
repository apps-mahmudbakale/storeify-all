@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Stock Closing - {{ Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Stock Closing</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-3">
                                <label for="month">Month:</label>
                                <select name="month" id="month" class="form-control ml-2">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::createFromDate(2024, $m, 1)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group mr-3">
                                <label for="year">Year:</label>
                                <select name="year" id="year" class="form-control ml-2">
                                    @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('app.stock-closing.report', ['month' => $month, 'year' => $year]) }}" class="btn btn-info ml-2">View Report</a>
                        </form>
                    </div>
                </div>

                @if ($closings->count() > 0)
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong>{{ $closings->count() }}</strong> stock closings recorded for {{ Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                    </div>
                @endif

                <div class="row">
                    @forelse ($products as $product)
                        @php
                            $closing = $closings->firstWhere('product_id', $product->id);
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card {{ $closing ? 'border-success' : 'border-warning' }}">
                                <div class="card-header {{ $closing ? 'bg-success' : 'bg-warning' }} text-white">
                                    <h5 class="mb-0">{{ $product->name }}</h5>
                                </div>
                                <div class="card-body">
                                    @if ($closing)
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Opening:</small>
                                                <h6>{{ $closing->opening_qty }}</h6>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Dispensed:</small>
                                                <h6>{{ $closing->qty_dispensed }}</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Closing:</small>
                                                <h6 class="text-success">{{ $closing->closing_qty }}</h6>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Variance:</small>
                                                <h6 class="text-{{ $closing->variance == 0 ? 'success' : ($closing->variance > 0 ? 'info' : 'danger') }}">
                                                    {{ $closing->variance >= 0 ? '+' : '' }}{{ $closing->variance }}
                                                </h6>
                                            </div>
                                        </div>
                                        <hr>
                                        <a href="{{ route('app.stock-closing.show', ['product' => $product->id, 'month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-info">View Details</a>
                                        <a href="{{ route('app.stock-closing.create', ['product' => $product->id, 'month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-primary">Edit</a>
                                    @else
                                        <p class="text-muted mb-3">No closing recorded for this period</p>
                                        <a href="{{ route('app.stock-closing.create', ['product' => $product->id, 'month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-primary">Record Closing</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning">No products found</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@endsection
