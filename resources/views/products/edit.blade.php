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
                            <li class="breadcrumb-item active">Update Product</li>
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
                        <h3 class="card-title">Update Product</h3>
                    </div>
                    <!-- /.card-header -->
                    <form action="{{route('app.products.update', $product->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- form start -->
                        <div class="card-body">
                            <div class="form-group">
                                <label>Make</label>
                                <input type="text" name="make" value="{{old('make', isset($product) ? $product->make : '')}}" class="form-control" placeholder="Make">
                            </div>
                            <div class="form-group">
                                <label>Body Type</label>
                                <input type="text" name="bodyType" value="{{old('bodyType', isset($product) ? $product->bodyType : '')}}" class="form-control" placeholder="Body Type">
                            </div>
                            <div class="form-group">
                                <label>Min Price</label>
                                <input type="number" name="minPrice" value="{{old('minPrice', isset($product) ? $product->minPrice : '')}}" class="form-control" placeholder="Min Price">
                            </div>
                            <div class="form-group">
                                <label>Max Price</label>
                                <input type="number" name="maxPrice" value="{{old('maxPrice', isset($product) ? $product->maxPrice : '')}}" class="form-control" placeholder="Max Price">
                            </div>
                            <div class="form-group">
                                <label>Transmission</label>
                                <select name="transmission" class="form-control">
                                    <option value="Manual" {{old('transmission', isset($product) ? $product->transmission : '') === 'Manual' ? 'selected' : ''}}>Manual</option>
                                    <option value="Automatic" {{old('transmission', isset($product) ? $product->transmission : '') === 'Automatic' ? 'selected' : ''}}>Automatic</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fuel Type</label>
                                <select name="fuelType" class="form-control">
                                    <option value="Petrol" {{old('fuelType', isset($product) ? $product->fuelType : '') === 'Petrol' ? 'selected' : ''}}>Petrol</option>
                                    <option value="Diesel" {{old('fuelType', isset($product) ? $product->fuelType : '') === 'Diesel' ? 'selected' : ''}}>Diesel</option>
                                    <option value="Electric" {{old('fuelType', isset($product) ? $product->fuelType : '') === 'Electric' ? 'selected' : ''}}>Electric</option>
                                    <option value="Hybrid" {{old('fuelType', isset($product) ? $product->fuelType : '') === 'Hybrid' ? 'selected' : ''}}>Hybrid</option>
                                </select>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Owner</label>
                                <select name="user_id" class="form-control">
                                    <option value="">Select Owner</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" {{ old('user_id', $product->user_id) == $owner->id ? 'selected' : '' }}>
                                            {{ $owner->name }} ({{ $owner->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Features</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="AC" {{ in_array('AC', old('features', $product->features ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">AC</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="GPS" {{ in_array('GPS', old('features', $product->features ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">GPS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Sunroof" {{ in_array('Sunroof', old('features', $product->features ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">Sunroof</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Bluetooth" {{ in_array('Bluetooth', old('features', $product->features ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">Bluetooth</label>
                                </div>
                                <div class="form-group">
                                <label>Images</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image">
                                        <label class="custom-file-label">Choose file</label>
                                    </div>
                                </div>
                                        @if($product->image)
                                            <div class="mt-2">
                                                <img src="data:image/jpeg;base64,{{ $product->image }}" class="img-fluid img-thumbnail" style="height: 200px; object-fit: cover;" />
                                            </div>
                                        @endif
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
