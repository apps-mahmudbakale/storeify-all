<div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Requests</h3>
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
                                    <tr role="row">
                                        <th>S/N</th>
                                        <th>Request Reference</th>
                                        <th>Station</th>
                                        {{-- <th>Requested By</th> --}}
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                        @foreach($requests as $request)
                                        <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$request->request_ref}}</td>
                                        <td>{{$request->name}}</td>
                                        {{-- <td>{{$request->user}}</td> --}}
                                        <td>{{\Carbon\Carbon::parse($request->date)->diffForHumans()}}</td>
                                        <td><label class="badge {{$request->status == 'pending' ? 'badge-secondary' : 'badge-success'}}">{{$request->status}}</label></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{route('app.requests.show', $request->request_ref)}}" class="btn btn-success btn-sm">
                                                    <i class="fa fa-eye"></i></a>
                                                    <button class="btn btn-danger btn-sm" id="del{{ $request->request_ref }}"
                                                        data-value="{{ $request->request_ref }}"><i class="fa fa-trash"></i></button>
                                                    <script>
                                                        document.querySelector('#del{{ $request->request_ref }}').addEventListener('click', function(e) {
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
                                                    <form id="del#{{ $request->request_ref }}"
                                                        action="{{ route('app.requests.destroy', $request->request_ref) }}" method="POST"
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
                        {{-- <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info" id="users_info" role="status" aria-live="polite">Showing <b>{{ $stations->firstItem() }}</b> to
                                    <b>{{ $reqiuests->lastItem() }}</b> out of <b>{{ $reqiuests->total() }}</b> entries</div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="users_paginate">
                                    {{ $reqiuests->links() }}
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div><!-- /.container-fluid -->
    </section>
</div>
