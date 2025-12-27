<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Sale Timeline</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('app.sales.index') }}">Sales</a></li>
                        <li class="breadcrumb-item active">Timeline</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- Sale Info Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <h3 class="profile-username text-center">{{ $saleDetails->car_make ?? 'Unknown Car' }}</h3>
                            <p class="text-muted text-center">{{ $saleDetails->car_body ?? 'N/A' }}</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Invoice</b> <a class="float-right">{{ $sale->invoice }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Buyer</b> <a class="float-right">{{ $sale->buyer_name }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Owner</b> <a class="float-right">{{ $saleDetails->owner_name ?? 'Unknown' }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Amount</b> <a class="float-right text-bold">{{ number_format($sale->amount, 2) }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Deposited</b> <a class="float-right text-success text-bold">{{ number_format($sale->deposit, 2) }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Balance Remaining</b> <a class="float-right text-danger text-bold">{{ number_format($sale->balance_remaining, 2) }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Add Payment Form -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Payment</h3>
                        </div>
                        <div class="card-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form wire:submit.prevent="addPayment">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" wire:model="amount">
                                    @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Payment Date</label>
                                    <input type="date" class="form-control @error('payment_date') is-invalid @enderror" wire:model="payment_date">
                                    @error('payment_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" wire:model="notes"></textarea>
                                    @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Record Payment</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Payment History</h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach($payments as $payment)
                                    <div class="time-label">
                                        <span class="bg-info">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M. Y') }}</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-money-bill-wave bg-success"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> {{ $payment->created_at->diffForHumans() }}</span>
                                            <h3 class="timeline-header">Payment of <strong>{{ number_format($payment->amount, 2) }}</strong> received</h3>
                                            @if($payment->notes)
                                                <div class="timeline-body">
                                                    {{ $payment->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                
                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
