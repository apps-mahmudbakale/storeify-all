<div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Products</h3>
                    <a href="{{ route('app.products.export') }}" class="btn btn-warning  float-right"><i
                        class="fa fa-file-export"></i> Export Products</a>
                        @can('create-products')
                        <a href="{{ route('app.products.import') }}" class="btn btn-primary float-right"><i
                        class="fa fa-file-import"></i> Import Products</a>
                    @endcan
                    @can('create-products')
                    <a href="{{ route('app.products.create') }}" class="btn btn-success float-right"><i
                            class="fa fa-plus-circle"></i></a>
                    @endcan

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="users_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_length" id="users_length"><label>Show <select wire:model="perPage"
                                            aria-controls="users"
                                            class="custom-select custom-select-sm form-control form-control-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select> entries</label></div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div id="users_filter" class="dataTables_filter"><label>Search:<input type="search"
                                            class="form-control form-control-sm" wire:model.debounce.300ms='search' placeholder=""
                                            aria-controls="users"></label></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="users" class="table table-bordered table-striped dataTable no-footer"
                                    role="grid" aria-describedby="users_info">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Buying Price</th>
                                            <th>Selling Price</th>
                                            <th>Quantity</th>
                                            <th>Expiry</th>
                                            {{-- <th>Store</th> --}}
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach($products as $product)
                                            <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$product->name}}</td>
                                            <td>{!! app(App\Settings\StoreSettings::class)->currency !!} {{number_format($product->buying_price)}}</td>
                                            <td>{!! app(App\Settings\StoreSettings::class)->currency !!}  {{number_format($product->selling_price)}}</td>
                                            <td>{{$product->qty}}</td>
                                            <td>{{\Carbon\Carbon::parse($product->expiry_date)->diffForHumans()}}</td>
                                            {{-- <td>{{$product->store->name}}</td> --}}
                                            <td>
                                                <div class="btn-group">
                                                    @can('update-products')
                                                    <a href="{{route('app.products.edit', $product->id)}}" class="btn btn-info btn-sm">
                                                        <i class="fa fa-edit"></i></a>
                                                    @endcan
                                                    @can('delete-products')
                                                        <button class="btn btn-danger btn-sm" id="del{{ $product->id }}"
                                                            data-value="{{ $product->id }}"><i class="fa fa-trash"></i></button>
                                                        @endcan
                                                        <script>
                                                            document.querySelector('#del{{ $product->id }}').addEventListener('click', function(e) {
                                                                // alert(this.getAttribute('data-value'));
                                                                Swal.fire({
                                                                    title: 'Are you sure?',
                                                                    text: "You won't be able to revert this!",
                                                                    icon: 'warning',
                                                                    showCancelButton: true,
                                                                    confirmButtonColor: '#3085d6',
                                                                    cancelButtonColor: '#d33',
                                                                    confirmButtonText: 'Yes, delete it!'
                                                                }).then((result) => {
                                                                    if (result.isConfirmed) {
                                                                        document.getElementById('del#'+this.getAttribute('data-value')).submit();
                                                                        // Swal.fire(
                                                                        //     'Deleted!',
                                                                        //     'Your file has been deleted.',
                                                                        //     'success'
                                                                        // )
                                                                    }
                                                                })
                                                            })
                                                        </script>
                                                        <form id="del#{{ $product->id }}"
                                                            action="{{ route('app.products.destroy', $product->id) }}" method="POST"
                                                             style="display: inline-block;">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        </form>
                                                </div>
                                            </td>
                                        </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info" id="users_info" role="status" aria-live="polite">Showing <b>{{ $products->firstItem() }}</b> to
                                    <b>{{ $products->lastItem() }}</b> out of <b>{{ $products->total() }}</b> entries</div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="users_paginate">
                                    {{ $products->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div><!-- /.container-fluid -->
    </section>
</div>
