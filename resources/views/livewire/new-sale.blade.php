<div>
    <style>
        #result {
            width: 100%;
            display: none;
            margin-top: -1px;
            border-top: 0px;
            overflow: hidden;
            border: 1px #CDCDCD solid;
            background-color: white;
        }
    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">New Sale</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">New Sale </li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <input type="text" class="form-control search_keyword" id="search_keyword" autofocus
                            placeholder="Search....." style="width: 100%; border-radius: 3px;">
                        <div id="result" class=""></div>
                    </div>
                    <!-- /.card-header -->
                    <div id="status"><br></div>
                    <div class="card-body">
                        <table class="table  table-striped">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Item Name</th>
                                    <th>Selling Price</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($carts as $cart)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <input type='hidden' value='{{ $cart->product_id }}'
                                            id='prid{{ $cart->id }}'>
                                        <td id="item{{ $cart->id }}">{{ $cart->name }}</td>
                                        <td>&#8358; <input type='number' id='price{{$cart->id}}' value='{{ $cart->selling_price }}' style='width:110px; display:inherit;' class='form-control'></td>
                                        <td><input type='number' id="qty{{ $cart->id }}" style='width:69px;'
                                                class='form-control' value='{{ $cart->quantity }}'></td>
                                        <td>&#8358; <span
                                                id="m{{ $cart->id }}">{{ number_format($cart->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <div class='btn-group'>
                                                <button id="plus{{ $cart->id }}" class='btn btn-info btn-sm'><i
                                                        class='fa fa-plus-circle'></i></button>
                                                <button id="minus{{ $cart->id }}"
                                                    class='btn btn-warning text-white btn-sm delete'><i
                                                        class='fa fa-minus-circle'></i></button>
                                                <a href="remove/{{$cart->product_id}}"
                                                    class='btn btn-danger btn-sm delete'><i
                                                        class='fa fa-times-circle'></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <script>
                                        $(() => {
                                            var item = $('#item{{ $cart->id }}').text()
                                            $('#plus{{ $cart->id }}').click(() => {
                                                var qty = $('#qty{{ $cart->id }}').val();
                                                var prid = $('#prid{{ $cart->id }}').val();
                                                var price = $('#price{{ $cart->id }}').val();
                                                var a = ++qty;
                                                var invoice = '{{ $cart->invoice }}';
                                                var user = '{{auth()->user()->id}}';
                                                console.log(user);
                                                $('#qty{{ $cart->id }}').val(a);

                                                $.ajax({
                                                    type: "POST",
                                                    url: "/api/getPrice",
                                                    data: {
                                                        qty: qty,
                                                        invoice: invoice,
                                                        prid: prid,
                                                        user: user,
                                                        price: price

                                                    },
                                                    cache: false,
                                                    success: function(html) {
                                                        console.log(html)
                                                        var json = html;
                                                        if (json) {
                                                            $('#m{{ $cart->id }}').text(json.amount);
                                                            $('#total').html(json.total);
                                                            $('#text').html(json.text);

                                                            if (json.msg == 'success') {

                                                            } else if (json.msg == 'excess') {
                                                                Swal.fire({
                                                                    position: 'center',
                                                                    icon: 'error',
                                                                    title: 'Quantity ' + item +
                                                                        ' is greater than Available',
                                                                    showConfirmButton: true,
                                                                    timer: 3500
                                                                })
                                                            }
                                                        }
                                                    }
                                                });
                                            });

                                            $('#minus{{ $cart->id }}').click(() => {
                                                var qty = $('#qty{{ $cart->id }}').val();
                                                var prid = $('#prid{{ $cart->id }}').val();
                                                var price = $('#price{{$cart->id}}').val();
                                                var a = --qty;
                                                var invoice = '{{ $cart->invoice }}';
                                                var user = '{{auth()->user()->id}}';
                                                $('#qty{{ $cart->id }}').val(a);

                                                $.ajax({
                                                    type: "POST",
                                                    url: "/api/getPrice",
                                                    data: {
                                                        qty: qty,
                                                        invoice: invoice,
                                                        prid: prid,
                                                        user:user,
                                                        price:price
                                                    },
                                                    cache: false,
                                                    success: function(html) {
                                                        console.log(html)
                                                        var json = html;
                                                        if (json) {
                                                            $('#m{{ $cart->id }}').text(json.amount);
                                                            $('#total').html(json.total);
                                                            $('#text').html(json.text);
                                                        }
                                                    }
                                                });
                                            });
                                            $('#price{{$cart->id}}').change(()=>{
                                                var price = $('#price{{$cart->id}}').val();
                                                var qty = $('#qty{{ $cart->id }}').val();
                                                var prid = $('#prid{{ $cart->id }}').val();
                                                var invoice = '{{ $cart->invoice }}';
                                                var user = '{{auth()->user()->id}}';
                                                // alert(price);
                                                $.ajax({
                                                    type: "POST",
                                                    url: "/api/getPrice",
                                                    data: {
                                                        qty: qty,
                                                        invoice: invoice,
                                                        prid: prid,
                                                        user:user,
                                                        price:price
                                                    },
                                                    cache: false,
                                                    success: function(html) {
                                                        console.log(html)
                                                        var json = html;
                                                        if (json) {
                                                            $('#m{{ $cart->id }}').text(json.amount);
                                                            $('#total').html(json.total);
                                                            $('#text').html(json.text);
                                                        }
                                                    }
                                                });
                                            })
                                            $('#qty{{ $cart->id }}').change(() => {
                                                var qty = $('#qty{{ $cart->id }}').val();
                                                var prid = $('#prid{{ $cart->id }}').val();
                                                var invoice = '{{ $cart->invoice }}';
                                                var user = '{{auth()->user()->id}}';
                                                var price = $('#price{{$cart->id}}').val();
                                                $.ajax({
                                                    type: "POST",
                                                    url: "/api/getPrice",
                                                    data: {
                                                        qty: qty,
                                                        invoice: invoice,
                                                        prid: prid,
                                                        user:user,
                                                        price: price
                                                    },
                                                    cache: false,
                                                    success: function(html) {
                                                        var json = html;
                                                        if (json) {
                                                            $('#m{{ $cart->id }}').text(json.amount);
                                                            $('#total').html(json.total);
                                                            $('#text').html(json.text);

                                                            console.log(json.msg);

                                                            if (json.msg == 'success') {

                                                            } else if (json.msg == 'excess') {
                                                                Swal.fire({
                                                                    position: 'center',
                                                                    icon: 'error',
                                                                    title: 'Quantity ' + item +
                                                                        ' is greater than Available',
                                                                    showConfirmButton: true,
                                                                    timer: 3500
                                                                })
                                                            }

                                                        }
                                                    }
                                                });
                                            })

                                            $('#save').click(() => {
                                                var buyer_name = $('#buyer_name').val();
                                                var buyer_dept = $('#buyer_dept').val();
                                                var url = 'save/{{ $cart->invoice }}?buyer_name=' + encodeURIComponent(buyer_name) + '&buyer_dept=' + encodeURIComponent(buyer_dept);
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "You won't be able to revert this!",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes, save it!'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location = url;
                                                    }
                                                })
                                            });

                                            $('#cancel').click(() => {
                                                var url = 'cancel/{{ $cart->invoice }}';
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "You won't be able to revert this!",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes, cancel it!'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location = url;
                                                    }
                                                })

                                            });

                                            $('#save_print').click(() => {
                                                var buyer_name = $('#buyer_name').val();
                                                var buyer_dept = $('#buyer_dept').val();
                                                var url = 'print/{{ $cart->invoice }}?buyer_name=' + encodeURIComponent(buyer_name) + '&buyer_dept=' + encodeURIComponent(buyer_dept);
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "You won't be able to revert this!",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes, save and print it!'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location = url;
                                                    }
                                                })

                                            });

                                            $('#invoice').click(() => {
                                                var buyer_name = $('#buyer_name').val();
                                                var buyer_dept = $('#buyer_dept').val();
                                                var url = '/app/invoice/{{ $cart->invoice }}?buyer_name=' + encodeURIComponent(buyer_name) + '&buyer_dept=' + encodeURIComponent(buyer_dept);
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "You won't be able to revert this!",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes, save and print it!'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location = url;
                                                    }
                                                })

                                            });
                                        });
                                    </script>
                                @endforeach
                                <tr>
                                    <td><strong style="font-size: 16px; color: #222222;">Total: </strong></td>
                                    <td><strong style="font-size: 16px; color: #222222;">{!! app(App\Settings\StoreSettings::class)->currency !!} <span
                                                id="total">{{ number_format($getSum->total, 2) }}</span></strong>
                                    </td>
                                    <td><strong style="font-size: 16px; color: #222222;"><span id="text">
                                                <?php $words = new NumberFormatter('En', NumberFormatter::SPELLOUT); ?>
                                                {{ strtoupper($words->format($getSum->total)) . ' NAIRA ONLY' }}
                                            </span>
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>

                        <!-- Buyer Information Section -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="buyer_name"><strong>Staff Name:</strong></label>
                                <select id="buyer_name" class="form-control">
                                    <option value="">-- Select staff --</option>
                                    @foreach($staff as $s)
                                        <option value="{{ $s->name }}" {{ $s->name === auth()->user()->name ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="buyer_dept"><strong>Department:</strong></label>
                                <select id="buyer_dept" class="form-control">
                                    <option value="">-- Select department --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <br>

                        <div class="btn-group pull-right">
                            <button class="btn btn-danger" id="cancel"><i class="fa fa-times"></i> Cancel</button>
                            <button id='save' class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                            <button id="save_print" class="btn btn-info"><i class="fa fa-print"></i> Save And
                                Print</button>
{{--                            <button id='invoice' class="btn btn-success"><i class="fa fa-save"></i> Generate Invoice</button>--}}
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <script>
        $(() => {
            $('#search_keyword').keyup(() => {
                var search_keyword_value = $('#search_keyword').val();
                // alert(search_keyword_value);
                var dataString = 'search_keyword=' + search_keyword_value;
                if (search_keyword_value !== '') {
                    const formData = new FormData();
                    formData.append('search_keyword', search_keyword_value);
                    formData.append('_token', "{{ csrf_token() }}");

                    fetch("{{ route('app.sales.search') }}", {
                            method: 'POST',
                            body: formData,
                            cache: 'no-cache',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest', // Add this header if needed
                            },
                        })
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('result').innerHTML = html;
                            document.getElementById('result').style.display = 'block';
                            // console.log(html);
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                        });
                }
                return false;
            })
        })
    </script>
</div>
