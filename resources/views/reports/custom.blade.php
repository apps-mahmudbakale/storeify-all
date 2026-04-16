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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #fff;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            color: #495057;
            padding-left: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 6px;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6c757d;
        }
        .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 10px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #007bff;
        }
    </style>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    To
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    User
                    <select name="user" class="form-control select2" id="user-select">
                        <option value="">-- All Users --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    Product
                    <select name="product" class="form-control select2" id="product-select">
                        <option value="">-- All Products --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('product') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <br>
                    <button type="submit" class="btn btn-success btn-block">Filter</button>
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
                        <td>{{$sale->quantity}}</td>
                        <td>{!! app(App\Settings\StoreSettings::class)->currency !!}  {{number_format($sale->price)}}</td>
                        <td>{!! app(App\Settings\StoreSettings::class)->currency !!}  {{number_format($sale->amount)}}</td>
                        <td>{{$sale->user}}</td>
                        <td>{{\Carbon\Carbon::parse($sale->created_at)->toFormattedDayDateString()}}</td>
                        </tr>
                       @endforeach
                    </tbody>
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
</body>

<script>
    $(document).ready(function() {
        $('#user-select').select2({
            placeholder: '-- All Users --',
            allowClear: true,
            width: '100%'
        });
        $('#product-select').select2({
            placeholder: '-- All Products --',
            allowClear: true,
            width: '100%'
        });
    });
</script>

</html>
