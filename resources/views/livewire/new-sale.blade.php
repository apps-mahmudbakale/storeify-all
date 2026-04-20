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
                            placeholder="Search or scan barcode..." style="width: 100%; border-radius: 3px;"
                            autocomplete="off">
        <div id="search-loading" style="display:none; padding: 10px; text-align:center; border: 1px #CDCDCD solid; background:white;">
                            <span style="display:inline-block; width:18px; height:18px; border:3px solid #ccc; border-top-color:#333; border-radius:50%; animation:spin 0.7s linear infinite; vertical-align:middle; margin-right:6px;"></span>
                            Searching...
                        </div>
                        <style>
                            @keyframes spin { to { transform: rotate(360deg); } }
                        </style>
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
                                    <th>Category</th>
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
                                        <input type='hidden' value='{{ $cart->product_id }}' id='prid{{ $cart->id }}'>
                                        <td id="item{{ $cart->id }}">{{ $cart->name }}</td>
                                        <td>{{ $cart->product_category ?? 'N/A' }}</td>
                                        <td>&#8358; <input type='number' id='price{{$cart->id}}' value='{{ $cart->price }}' style='width:110px; display:inherit;' class='form-control'></td>
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
                                                var discount = $('#discount_amount').val() || 0;
                                                var discount_type = $('#discount_type').val();
                                                var url = 'save/{{ $cart->invoice }}?discount=' + discount + '&discount_type=' + discount_type;
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
                                                var discount = $('#discount_amount').val() || 0;
                                                var discount_type = $('#discount_type').val();
                                                var url = 'print/{{ $cart->invoice }}?discount=' + discount + '&discount_type=' + discount_type;
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
                                                        window.open(url, '_blank');
                                                        window.location = 'cancel/{{ $cart->invoice }}';
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
                                <tr id="discount-row">
                                    <td><strong style="font-size: 14px; color: #555;">Discount: </strong></td>
                                    <td colspan="2">
                                        <div class="input-group" style="max-width: 280px;">
                                            <input type="number" id="discount_amount" min="0" step="0.01" value="0"
                                                class="form-control" placeholder="0.00" style="max-width:120px;">
                                            <div class="input-group-append">
                                                <select id="discount_type" class="form-control">
                                                    <option value="fixed">Fixed ({!! app(App\Settings\StoreSettings::class)->currency !!})</option>
                                                    <option value="percent">Percent (%)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong style="font-size: 16px; color: #c0392b;">Grand Total: </strong></td>
                                    <td colspan="2"><strong style="font-size: 16px; color: #c0392b;">{!! app(App\Settings\StoreSettings::class)->currency !!} <span id="grand_total">{{ number_format($getSum->total, 2) }}</span></strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>

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
            let searchTimer = null;

            // Prevent Enter key from submitting any form (barcode scanner sends Enter)
            $('#search_keyword').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    return false;
                }
            });

            $('#search_keyword').on('keyup', function(e) {
                // Ignore modifier keys and Enter
                if (['Enter', 'Shift', 'Control', 'Alt', 'Meta', 'Tab'].includes(e.key)) {
                    return false;
                }

                const value = $(this).val().trim();
                const result = document.getElementById('result');
                const loading = document.getElementById('search-loading');

                // Clear previous timer (debounce)
                clearTimeout(searchTimer);

                if (value === '') {
                    result.style.display = 'none';
                    loading.style.display = 'none';
                    result.innerHTML = '';
                    return false;
                }

                // Show loading immediately
                loading.style.display = 'block';
                result.style.display = 'none';
                result.innerHTML = '';

                // Debounce the actual fetch by 500ms
                searchTimer = setTimeout(() => {
                    const formData = new FormData();
                    formData.append('search_keyword', value);
                    formData.append('_token', "{{ csrf_token() }}");

                    fetch("{{ route('app.sales.search') }}", {
                        method: 'POST',
                        body: formData,
                        cache: 'no-cache',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    })
                    .then(response => response.text())
                    .then(html => {
                        loading.style.display = 'none';
                        result.innerHTML = html;
                        result.style.display = html.trim() !== '' ? 'block' : 'none';
                    })
                    .catch(error => {
                        loading.style.display = 'none';
                        console.error('Search error:', error);
                    });
                }, 500); // 500ms debounce — loader shows immediately, fetch waits

                return false;
            });

            // Hide results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#search_keyword, #result').length) {
                    document.getElementById('result').style.display = 'none';
                }
            });

            // Discount grand total calculation
            function recalcGrandTotal() {
                var subtotal = parseFloat($('#total').text().replace(/,/g, '')) || 0;
                var discount = parseFloat($('#discount_amount').val()) || 0;
                var type = $('#discount_type').val();
                var discountAmt = type === 'percent' ? (subtotal * discount / 100) : discount;
                discountAmt = Math.min(discountAmt, subtotal);
                var grand = subtotal - discountAmt;
                $('#grand_total').text(grand.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }
            $('#discount_amount, #discount_type').on('input change', recalcGrandTotal);
        })
    </script>
</div>
