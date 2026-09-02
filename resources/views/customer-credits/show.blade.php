@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Credit Details</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.customer-credits.index') }}">Customer Credits</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <strong>{{ $customerCredit->customer_name }}</strong>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">CUSTOMER INFORMATION</h6>
                                        <p>
                                            <strong>Name:</strong> {{ $customerCredit->customer_name }}<br>
                                            <strong>Phone:</strong> {{ $customerCredit->customer_phone ?? 'N/A' }}<br>
                                            <strong>Invoice:</strong> {{ $customerCredit->invoice ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">CREDIT INFORMATION</h6>
                                        <p>
                                            <strong>Reason:</strong> {{ $customerCredit->reason }}<br>
                                            <strong>Status:</strong> 
                                            @if($customerCredit->status === 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($customerCredit->status === 'partially_cleared')
                                                <span class="badge badge-info">Partially Cleared</span>
                                            @else
                                                <span class="badge badge-success">Fully Cleared</span>
                                            @endif
                                            <br>
                                            <strong>Due Date:</strong> {{ $customerCredit->due_date ? $customerCredit->due_date->format('M d, Y') : 'Not set' }}
                                        </p>
                                    </div>
                                </div>

                                <hr>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Credit Amount</span>
                                                <span class="info-box-number">
                                                    {!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($customerCredit->amount, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Amount Cleared</span>
                                                <span class="info-box-number">
                                                    {!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($customerCredit->amount_cleared, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Balance Due</span>
                                                <span class="info-box-number">
                                                    {!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($customerCredit->balance_due, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($customerCredit->notes)
                                    <div class="alert alert-info">
                                        <strong>Notes:</strong> {{ $customerCredit->notes }}
                                    </div>
                                @endif

                                <hr>

                                <div class="text-muted small">
                                    <p>
                                        <strong>Recorded by:</strong> {{ $customerCredit->user->name }} on {{ $customerCredit->created_at->format('M d, Y h:i A') }}<br>
                                        @if($customerCredit->updated_at != $customerCredit->created_at)
                                            <strong>Last updated:</strong> {{ $customerCredit->updated_at->format('M d, Y h:i A') }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="card-footer">
                                @if($customerCredit->status !== 'fully_cleared')
                                    <a href="{{ route('app.customer-credits.edit', $customerCredit) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Update Payment
                                    </a>
                                    @if($customerCredit->balance_due > 0)
                                        <form action="{{ route('app.customer-credits.mark-cleared', $customerCredit) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="return confirm('Mark this credit as fully cleared?')">
                                                <i class="fas fa-check"></i> Mark as Fully Cleared
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                <a href="{{ route('app.customer-credits.index') }}" class="btn btn-secondary">
                                    Back to List
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Quick Actions</h3>
                            </div>
                            <div class="card-body">
                                @if($customerCredit->status !== 'fully_cleared')
                                    <a href="{{ route('app.customer-credits.edit', $customerCredit) }}" class="btn btn-primary btn-block mb-2">
                                        <i class="fas fa-edit"></i> Edit Credit
                                    </a>
                                @endif
                                <form action="{{ route('app.customer-credits.destroy', $customerCredit) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to delete this record?')">
                                        <i class="fas fa-trash"></i> Delete Record
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
