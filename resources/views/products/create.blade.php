@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Vehicles</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.products.index') }}">Vehicles</a></li>
                            <li class="breadcrumb-item active">Create Vehicle</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- New User form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Create Vehicle</h3>
                    </div>
                    <!-- /.card-header -->
                    <form action="{{route('app.products.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- form start -->
                        <div class="card-body">
                            <div class="form-group">
                                <label>Make</label>
                                <input type="text" name="make" class="form-control" placeholder="Make">
                            </div>
                             <div class="form-group">
                                <label>Body Type</label>
                                <input type="text" name="bodyType" class="form-control" placeholder="Body Type">
                            </div>
                            <div class="form-group">
                                <label>Min Price</label>
                                <input type="number" name="minPrice" class="form-control" placeholder="Min Price">
                            </div>
                            <div class="form-group">
                                <label>Max Price</label>
                                <input type="number" name="maxPrice" class="form-control" placeholder="Max Price">
                            </div>
                            <div class="form-group">
                                <label>Transmission</label>
                                <select name="transmission" class="form-control">
                                    <option value="Manual">Manual</option>
                                    <option value="Automatic">Automatic</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fuel Type</label>
                                <select name="fuelType" class="form-control">
                                    <option value="Petrol">Petrol</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="Electric">Electric</option>
                                    <option value="Hybrid">Hybrid</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Owner</label>
                                <select name="user_id" class="form-control">
                                    <option value="">Select Owner</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->name }} ({{ $owner->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Features</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="AC">
                                    <label class="form-check-label">AC</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="GPS">
                                    <label class="form-check-label">GPS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Sunroof">
                                    <label class="form-check-label">Sunroof</label>
                                    <div class="form-group">
                                <label>Images</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image" />
                                        <label class="custom-file-label">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Bluetooth">
                                    <label class="form-check-label">Bluetooth</label>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
