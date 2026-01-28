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
                <h3 class="card-title">Items</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @if(session('error'))
                  <div class="alert alert-danger">{{ session('error') }}</div>
              @endif
              @if(session('success'))
                  <div class="alert alert-success">{{ session('success') }}</div>
              @endif

              <div class="row mb-4">
                  <div class="col-md-6">
                      <h5>Invoice: <strong>{{ $invoice }}</strong></h5>
                  </div>
                  <div class="col-md-6 text-right">
                      @if($isDone)
                          <span class="badge badge-success px-4 py-2">RETURN COMPLETED</span>
                      @elseif($returnRequests->isNotEmpty())
                          <span class="badge badge-warning px-4 py-2">RETURN PENDING APPROVAL</span>
                      @endif
                  </div>
              </div>

              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Item Name</th>
                    <th class="text-right">Sale Price</th>
                    <th class="text-center">Remaining Qty</th>
                    <th class="text-center">Returned Qty</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @php $hasPending = false; @endphp
                  @foreach ($items as $item)
                    @php 
                      $request = $returnRequests->get($item->product_id);
                      if ($request && !$request->status) $hasPending = true;
                    @endphp
                    <tr>
                      <td>{{$item->product}}</td>
                      <td class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{number_format($item->selling_price, 2)}}</td>
                      <td class="text-center">{{$item->quantity}}</td>
                      <td class="text-center">
                          {{ $request ? $request->return_qty : 0 }}
                      </td>
                      <td class="text-center">
                          @if($request)
                              @if($request->status)
                                  <span class="badge badge-success">Approved</span>
                              @else
                                  <span class="badge badge-warning">Pending</span>
                              @endif
                          @else
                              <span class="text-muted">-</span>
                          @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>

              @if($hasPending && auth()->user()->hasRole('admin'))
                <div class="mt-4">
                    <form action="{{route('app.returns.approve')}}" method="POST" onsubmit="return confirm('Are you sure you want to approve this return and update inventory?');">
                      @csrf
                      <input type="hidden" name="invoice" value="{{ $invoice }}">
                      <button type="submit" class="btn btn-success btn-lg btn-block">
                          <i class="fas fa-check-circle mr-1"></i> Approve & Process Return
                      </button>
                    </form>
                </div>
              @endif

              <div class="mt-3">
                  <a href="{{ route('app.returns.index') }}" class="btn btn-secondary">
                      <i class="fas fa-arrow-left mr-1"></i> Back to List
                  </a>
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
