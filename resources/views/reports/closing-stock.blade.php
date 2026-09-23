@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Closing Stock — {{ $start->format('F Y') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Reports</li>
                            <li class="breadcrumb-item active">Closing Stock</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @php
                    $q = ['category' => request('category'), 'search' => request('search')];
                    $prev = $start->copy()->subMonth()->format('Y-m');
                    $next = $start->copy()->addMonth()->format('Y-m');
                @endphp

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <h3 class="card-title">
                                @if ($isCurrent)
                                    Stock on hand as at today ({{ \Carbon\Carbon::now()->format('jS F Y') }})
                                @else
                                    Stock at end of {{ $start->format('F Y') }}
                                @endif
                            </h3>
                            <div>
                                <a href="{{ route('app.reports.closing-stock', array_merge($q, ['month' => $prev])) }}"
                                   class="btn btn-outline-secondary btn-sm mr-1"><i class="fa fa-chevron-left"></i> Prev</a>
                                <a href="{{ route('app.reports.closing-stock', array_merge($q, ['month' => $next])) }}"
                                   class="btn btn-outline-secondary btn-sm mr-2">Next <i class="fa fa-chevron-right"></i></a>
                                @if (!$isCurrent)
                                    <a href="{{ route('app.reports.closing-stock') }}" class="btn btn-outline-info btn-sm mr-2">This month</a>
                                @endif
                                <a href="{{ route('app.reports.closing-stock', array_merge($q, ['month' => $start->format('Y-m'), 'export' => 'csv'])) }}"
                                   class="btn btn-success btn-sm"><i class="fa fa-download"></i> Export CSV</a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('app.reports.closing-stock') }}" method="GET" class="form-inline mb-3">
                            <div class="form-group mr-2 mb-1">
                                <label class="sr-only">Month</label>
                                <input type="month" name="month" value="{{ $start->format('Y-m') }}" class="form-control form-control-sm">
                            </div>
                            <div class="form-group mr-2 mb-1">
                                <label class="sr-only">Category</label>
                                <select name="category" class="form-control form-control-sm">
                                    <option value="">All categories</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mr-2 mb-1">
                                <label class="sr-only">Search</label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product" class="form-control form-control-sm">
                            </div>
                            <button class="btn btn-primary btn-sm mb-1"><i class="fa fa-filter"></i> Apply</button>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th class="text-right">Opening</th>
                                        <th class="text-right">Received</th>
                                        <th class="text-right">Sold</th>
                                        <th class="text-right">Closing</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rows as $i => $r)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><a href="{{ route('app.products.history', $r->id) }}">{{ $r->name }}</a></td>
                                            <td>{{ $r->category }}</td>
                                            <td class="text-right">{{ number_format($r->opening) }}</td>
                                            <td class="text-right">{{ number_format($r->received) }}</td>
                                            <td class="text-right">{{ number_format($r->sold) }}</td>
                                            <td class="text-right"><strong>{{ number_format($r->closing) }}</strong></td>
                                            <td class="text-center">
                                                @if ($r->status === 'reconciled')
                                                    <span class="badge badge-success">OK</span>
                                                @elseif ($r->status === 'check')
                                                    <span class="badge badge-warning" title="Product stock qty differs from the total held in its batches">Check stock</span>
                                                @elseif ($r->status === 'estimate')
                                                    <span class="badge badge-info" title="Reconstructed from dated sales & receipts; approximate">≈ estimate</span>
                                                @elseif ($r->status === 'negative')
                                                    <span class="badge badge-danger" title="Computed closing is negative — data looks inconsistent">Negative</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No products found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="3" class="text-right">Total</td>
                                        <td class="text-right">{{ number_format($rows->sum('opening')) }}</td>
                                        <td class="text-right">{{ number_format($rows->sum('received')) }}</td>
                                        <td class="text-right">{{ number_format($rows->sum('sold')) }}</td>
                                        <td class="text-right">{{ number_format($rows->sum('closing')) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="text-muted small mt-2">
                            Closing = opening + received − sold. For past months this is reconstructed backwards from today's
                            stock using dated sales and batch receipts, so it is approximate. The current month is exact.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection