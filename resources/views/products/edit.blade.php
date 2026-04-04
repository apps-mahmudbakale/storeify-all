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
                    <form action="{{ route('app.products.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- form start -->
                        <div class="card-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name"
                                    value="{{ old('name', isset($product) ? $product->name : '') }}" class="form-control"
                                    placeholder="Name">
                            </div>
                            <div class="form-group">
                                <label>Barcode</label>
                                <input type="text" name="barcode"
                                    value="{{ old('barcode', isset($product) ? $product->barcode : '') }}" class="form-control"
                                    placeholder="Barcode">
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select name="product_category" class="form-control">
                                    <option selected>{{ $product->product_category }}</option>
                                    <option>Bio Med</option>
                                    <option>Medical Consumables</option>
                                    <option>Dialysis Items</option>
                                    <option>Laboratory Items</option>
                                    <option>⁠Maintenance</option>
                                    <option>⁠⁠Miscellaneous</option>
                                    <option>Pharmacy</option>
                                    <option>Radiology</option>
                                    <option value="Sanitary">Sanitary</option>
                                    <option>Stationaries</option>
                                    
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Buying Price</label>
                                <input type="number" name="buying_price" id="buying"
                                    value="{{ old('name', isset($product) ? $product->buying_price : '') }}"
                                    class="form-control" placeholder="Buying Price">
                            </div>
                            {{-- <div class="form-group">
                                <label>Selling Price</label>
                                <input type="text" name="selling_price" id="selling"  value="{{old('name', isset($product) ? $product->selling_price : '')}}" class="form-control" placeholder="Selling Price">
                            </div> --}}
                            <div class="form-group">
                                <label>VAT Percentage (%)</label>
                                <input type="number" name="vat_percentage"
                                    value="{{ old('vat_percentage', isset($product) ? $product->vat_percentage : 0) }}"
                                    class="form-control" placeholder="VAT % (e.g. 7.5)" min="0" max="100" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Quantity in Stock</label>
                                <input type="number" name="qty"
                                    value="{{ old('name', isset($product) ? $product->qty : '') }}" class="form-control"
                                    placeholder="Quantity in Stock">
                            </div>
                            <div class="form-group">
                                <label>Unit</label>
                                <select name="unit" class="form-control">
                                    <option value="pcs"
                                        {{ old('unit', isset($product) ? $product->unit : '') === 'pcs' ? 'selected' : '' }}>
                                        Pieces (pcs)</option>
                                    <option value="packs"
                                        {{ old('unit', isset($product) ? $product->unit : '') === 'packs' ? 'selected' : '' }}>
                                        Packs</option>
                                    <option value="bottles"
                                        {{ old('unit', isset($product) ? $product->unit : '') === 'bottles' ? 'selected' : '' }}>
                                        Bottles</option>
                                    <option value="cartons"
                                        {{ old('unit', isset($product) ? $product->unit : '') === 'cartons' ? 'selected' : '' }}>
                                        Cartons</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Minimum Quantity</label>
                                <input type="number" name="min_qty"
                                    value="{{ old('min_qty', isset($product) ? $product->min_qty : '') }}"
                                    class="form-control" placeholder="Minimum Quantity Alert">
                            </div>
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="date" name="expiry_date"
                                    value="{{ old('name', isset($product) ? $product->expiry_date : '') }}"
                                    class="form-control" placeholder="Expiry Date">
                            </div>

                            {{-- <div class="form-group">
                                <label>Store</label>
                                <select name="store_id" class="form-control">
                                    <option selected value="{{$product->store->id}}">{{$product->store->name}}</option>
                                    @foreach ($stores as $store)
                                            <option value="{{$store->id}}">{{$store->name}}</option>
                                    @endforeach
                                </select>
                            </div> --}}
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
        <script>
            var buying = document.getElementById('buying');
            var selling = document.getElementById('selling');

            buying.addEventListener('keyup', () => {
                // alert(buying.value);
                selling.value = buying.value * {{ app(App\Settings\StoreSettings::class)->sell_margin }}
            });
        </script>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
