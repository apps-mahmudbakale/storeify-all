@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Return Sale</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{route('app.dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Sales</li>
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
                <h3 class="card-title">Return Requests</h3>
                <a href="{{route('app.returns.create')}}" class="btn btn-success float-right" title="Initiate New Return">
                    <i class="fa fa-plus-circle mr-1"></i> New Return
                </a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                  <thead>
                    <tr>
                      <th style="width: 50px;">S/N</th>
                      <th>Date</th>
                      <th>Invoice</th>
                      <th class="text-center">Status</th>
                      <th style="width: 100px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($requests as $request)
                      <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{ \Carbon\Carbon::parse($request->date)->format('d M, Y') }}</td>
                        <td><span class="font-weight-bold">{{$request->invoice}}</span></td>
                        <td class="text-center">
                          @if($request->status == true)
                          <span class="badge badge-success px-3">Approved</span>
                          @else
                          <span class="badge badge-warning px-3">Pending</span>
                          @endif
                        </td>
                        <td class="text-center">
                          <div class="btn-group">
                            <a href="{{route('app.returns.show', $request->invoice)}}" 
                               class="btn btn-sm btn-info" 
                               title="View Details">
                              <i class="fa fa-eye"></i>
                            </a>
                            @role('admin')
                              @if($request->status != true)
                              <a href="{{route('app.returns.edit', $request->invoice)}}" 
                                 class="btn btn-sm btn-primary" 
                                 title="Process Return">
                                <i class="fa fa-pencil-alt"></i>
                              </a>
                              @endif
                            @endrole
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="text-center">No return requests found.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
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
@endsection
