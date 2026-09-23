@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Product Batches</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Batches</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Batches</h3>
                        <div class="float-right">
                            <form action="{{ route('app.batches.index') }}" method="GET" class="form-inline" style="display:inline-flex;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-1" placeholder="Search product or batch no">
                                <button class="btn btn-default btn-sm"><i class="fa fa-search"></i></button>
                            </form>
                            <a href="{{ route('app.batches.create') }}" class="btn btn-success ml-1"><i class="fa fa-plus-circle"></i> New Batch</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Batch No.</th>
                                    <th>Product</th>
                                    <th>Initial Qty</th>
                                    <th>Remaining</th>
                                    <th>Cost (&#8358;)</th>
                                    <th>Expiry</th>
                                    <th>Received</th>
                                    <th style="width: 220px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($batches as $batch)
                                    <tr>
                                        <td><code>{{ $batch->batch_no }}</code></td>
                                        <td>
                                            <a href="{{ route('app.products.history', $batch->product_id) }}">{{ $batch->product->name }}</a>
                                        </td>
                                        <td>{{ $batch->initial_qty }}</td>
                                        <td>
                                            <span class="badge {{ $batch->qty_remaining > 0 ? 'badge-success' : 'badge-danger' }}">{{ $batch->qty_remaining }}</span>
                                        </td>
                                        <td>{{ number_format($batch->buying_price, 2) }}</td>
                                        <td>
                                            @if ($batch->expiry_date)
                                                {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($batch->received_at)->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <form action="{{ route('app.batches.stock') }}" method="POST" class="form-inline" style="flex:1;">
                                                    @csrf
                                                    <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                                                    <input type="number" name="qty" min="1" placeholder="Qty" class="form-control form-control-sm mr-1" style="width:70px;" required>
                                                    <button class="btn btn-info btn-sm" title="Add stock to this batch"><i class="fa fa-plus"></i></button>
                                                </form>
                                                <form action="{{ route('app.batches.destroy', $batch->id) }}" method="POST" class="ml-1" onsubmit="return confirm('Delete batch {{ $batch->batch_no }}? Remaining qty ({{ $batch->qty_remaining }}) will be removed from stock.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" title="Delete batch"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No batches found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="float-left">
                            <strong>Total stock in all batches: {{ $batches->sum('qty_remaining') }}</strong>
                        </div>
                        <div class="float-right">{{ $batches->links() }}</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection