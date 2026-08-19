<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ app(App\Settings\StoreSettings::class)->store_name }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    @livewireStyles
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{!empty(app(App\Settings\StoreSettings::class)->favicon) ? asset('storage/settings/'.app(App\Settings\StoreSettings::class)->favicon):asset('favicon.png')}}">

    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->

<body class="hold-transition skin-blue layout-top-nav">


    <!-- Main content -->
    <div class="invoice p-3 mb-3">
        <!-- title row -->
        <div class="row">
            <div class="col-lg-12">
                <h1 align="center">
                    <img src="{{!empty(app(App\Settings\StoreSettings::class)->store_logo) ? asset('storage/store/'.app(App\Settings\StoreSettings::class)->store_logo):asset('logo.png')}}" alt="" width="143px" height="143px" class="img img-rounded">
                        <br>
                    {{ app(App\Settings\StoreSettings::class)->store_name }}
                </h1>
                <!--<address class="text-center">Birnin Kebbi, Kebbi State</address>-->
                <small class="float-right">Date: <?php echo date('d/m/Y'); ?></small>

            </div>
            <!-- /.col -->
        </div>
        <hr>
        <!-- info row -->
        <!-- /.row -->
        <div class="col-sm-12">
            <form action="{{route('app.custom.report')}}" method="POST" class="row">
                @csrf
                <div class="col-md-2">
                    From
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    To
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-2">
                    Product
                    <select name="product" class="form-control select2">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    Category
                    <select name="category" class="form-control select2">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    Sold By
                    <select name="user" class="form-control select2">
                        <option value="">Select Staff</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <br>
                   <button type="submit" class="btn btn-success">Filter</button>
                   <a href="{{ route('app.custom.report.view') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
        <!-- Table row -->
        <div class="row">
            @if(isset($sales) && !empty($sales))
            <div class="col-lg-12 table-responsive">
                <br>
                <hr>
                <br>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Invoice</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Sold Rate</th>
                            <th>Amount</th>
                            <th>Sold By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach($sales as $sale)
                        <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$sale->invoice}}</td>
                        <td>{{$sale->product}}</td>
                        <td>{{$sale->category}}</td>
                        <td>{{$sale->quantity}}</td>
                        <td>{!! app(App\Settings\StoreSettings::class)->currency !!}  {{number_format($sale->price)}}</td>
                        <td>{!! app(App\Settings\StoreSettings::class)->currency !!}  {{number_format($sale->amount)}}</td>
                        <td>{{$sale->user}}</td>
                        <td>{{\Carbon\Carbon::parse($sale->created_at)->toFormattedDayDateString()}}</td>
                        </tr>
                       @endforeach
                    </tbody>
                    <tfoot style="font-weight: bold; background-color: #f8f9fa;">
                        <tr>
                            <td colspan="4" class="text-right">Total:</td>
                            <td>{{ number_format($qty_sum ?? 0) }}</td>
                            <td></td>
                            <td>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($sum ?? 0, 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
            <!-- accepted payments column -->
            <div class="col-lg-6">

            </div>
            <!-- /.col -->
            <div class="col-lg-6">
                <p class="lead">Sales Made </p>

                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th>Total:</th>
                            <td>{!! app(App\Settings\StoreSettings::class)->currency !!}  {{number_format($sum,2)}}</td>
                        </tr>
                        <tr>
                            <th>Amount In Words:</th>
                            <td>{{ucfirst($words)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- this row will not appear when printing -->
        <div class="row no-print">
            <div class="col-lg-12">
                <a href="{{ route('app.dashboard') }}" class="btn btn-primary pull-left"><i class="fa fa-home"></i> Go
                    Home</a>
                <div class="btn-group">

                    <button type="button" class="btn btn-info">Download Report</button>
                    <button type="button" class="btn btn-info dropdown-toggle dropdown-icon"
                        data-toggle="dropdown">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a class="dropdown-item" href="{{route('app.general-report.export-excel')}}">Excel Format</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{route('app.general-report.export-pdf')}}">PDF Format</a>
                    </div>

                </div>
                <button onclick="window.print();" class="btn btn-success pull-right"><i class="fa fa-save"></i>
                    Print</button>
            </div>
        </div>
        @endif
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select an option",
                allowClear: true
            });
        });
    </script>
</body>

</html>
