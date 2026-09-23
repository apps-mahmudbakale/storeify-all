@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Create Batch</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.products.index') }}">Products</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.batches.index') }}">Batches</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">New Batch</h3>
                    </div>
                    <form action="{{ route('app.batches.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label>Product</label>
                                <select name="product_id" class="form-control" required>
                                    <option value="">-- Select product --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Batch No.</label>
                                        <input type="text" name="batch_no" class="form-control" value="{{ old('batch_no') }}" placeholder="e.g. A1, B10" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Batch Quantity</label>
                                        <input type="number" name="qty" min="1" class="form-control" value="{{ old('qty') }}" placeholder="e.g. 25" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Buying Price (per unit)</label>
                                        <input type="number" step="0.01" min="0" name="buying_price" class="form-control" value="{{ old('buying_price') }}" placeholder="Defaults to product cost">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Expiry Date</label>
                                        <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Received Date</label>
                                        <input type="date" name="received_at" class="form-control" value="{{ old('received_at', date('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Create Batch</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection