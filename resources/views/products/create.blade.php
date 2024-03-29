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
                            <li class="breadcrumb-item active">Create Product</li>
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
                        <h3 class="card-title">Create Product</h3>
                    </div>
                    <!-- /.card-header -->
                    <form action="{{route('app.products.store')}}" method="POST">
                        @csrf
                        <!-- form start -->
                        <div class="card-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Name">
                            </div>
                            <div class="form-group">
                                <label>Buying Price</label>
                                <input type="text" name="buying_price" id="buying" class="form-control" placeholder="Buying Price">
                            </div>
                            <div class="form-group">
                                <label>Selling Price</label>
                                <input type="text" name="selling_price" id="selling"  class="form-control" placeholder="Selling Price">
                            </div>
                            <div class="form-group">
                                <label>Quantity in Stock</label>
                                <input type="number" name="qty" class="form-control" placeholder="Quantity in Stock">
                            </div>
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="date" name="expiry_date" class="form-control" placeholder="Expiry Date">
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
        <script>
            var buying = document.getElementById('buying');
            var selling = document.getElementById('selling');

            buying.addEventListener('keyup', ()=>{
                // alert(buying.value);
                selling.value = buying.value * {{ app(App\Settings\StoreSettings::class)->sell_margin}}
            });

        </script>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
