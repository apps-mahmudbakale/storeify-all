@extends('layouts.app')

@section('content')
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Import Staff</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.staff.index') }}">Staff</a></li>
                            <li class="breadcrumb-item active">Import</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                <div class="card card-primary mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Upload Excel File</h3>
                    </div>

                    <form action="{{ route('app.staff.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="card-body">

                            <div class="form-group">
                                <label>Choose Excel File</label>
                                <input type="file" name="file" class="form-control" required>
                                <small class="text-muted">Allowed: .xlsx, .xls, .csv</small>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                Import Staff
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </section>

    </div>
@endsection
