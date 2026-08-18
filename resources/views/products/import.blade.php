@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Products</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Import Products</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                
                <!-- Success Message -->
                @if (session('import_success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Success!</strong> {{ session('import_success') }}
                    </div>
                @endif
                
                <!-- Error Messages -->
                @if (session('import_errors'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Import Errors:</strong>
                        <ul class="mt-2 mb-0">
                            @foreach (session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Import Form -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Import Products from CSV</h3>
                    </div>
                    <!-- /.card-header -->
                    <form action="{{route('app.import.products')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- form start -->
                        <div class="card-body">
                            <div class="form-group">
                                <label for="csv">CSV File <span class="text-danger">*</span></label>
                                <input type="file" id="csv" name="csv" class="form-control @error('csv') is-invalid @enderror" accept=".csv,.xlsx,.xls" required>
                                @error('csv')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted mt-2">
                                    <strong>Required columns:</strong> product, cost, quantity, expiry, category, selling_price (optional)
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <strong>⚠️ Important Notes:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Product names cannot contain SQL reserved words (DOUBLE, FLOAT, SELECT, etc.)</li>
                                    <li>All prices must be positive numbers</li>
                                    <li>Product names must be between 2-255 characters</li>
                                    <li>Quantities cannot be negative</li>
                                    <li>Selling price will be calculated from cost if not provided</li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Import Products
                            </button>
                            <a href="{{ route('app.products.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
