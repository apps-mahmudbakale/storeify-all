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

              <form action="{{route('app.returns.store')}}" method="POST" id="returnForm">
                @csrf
                <input type="hidden" value="{{$items[0]->invoice ?? ''}}" name="invoice">
                <table class="table table-bordered table-hover">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 50px;">Select</th>
                      <th>Item Name</th>
                      <th class="text-right">Price</th>
                      <th class="text-center">Sold Qty</th>
                      <th class="text-center" style="width: 150px;">Return Qty</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($items as $index => $item)
                      <tr>
                        <td class="text-center">
                          <input type="checkbox" value="{{$item->product_id}}" name="items[]" id="item_{{$index}}" class="item-checkbox">
                        </td>
                        <td>
                            <label for="item_{{$index}}" class="font-weight-normal mb-0">{{$item->product}}</label>
                        </td>
                        <td class="text-right">{!! app(App\Settings\StoreSettings::class)->currency !!} {{number_format($item->selling_price, 2)}}</td>
                        <td class="text-center">{{$item->quantity}}</td>
                        <td>
                          <input type="number" 
                                 value="0" 
                                 min="0" 
                                 max="{{$item->quantity}}" 
                                 class="form-control form-control-sm return-qty" 
                                 name="rqty[]" 
                                 disabled>
                          <small class="text-muted">Max: {{$item->quantity}}</small>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="text-center">No items found for this invoice.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
                <div class="mt-4">
                  <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fas fa-undo mr-1"></i> Submit Return Process
                  </button>
                  <a href="{{ route('app.returns.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
              </form>
            </div>

            @push('js')
            <script>
                document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const qtyInput = this.closest('tr').querySelector('.return-qty');
                        qtyInput.disabled = !this.checked;
                        if (!this.checked) qtyInput.value = 0;
                        updateSubmitButton();
                    });
                });

                function updateSubmitButton() {
                    const anyChecked = document.querySelectorAll('.item-checkbox:checked').length > 0;
                    document.getElementById('submitBtn').disabled = !anyChecked;
                }
            </script>
            @endpush
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div><!-- /.container-fluid -->
</section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
