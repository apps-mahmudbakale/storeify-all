@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Update Customer Credit</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.customer-credits.index') }}">Customer Credits</a></li>
                            <li class="breadcrumb-item active">Update</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Update Payment Status</h3>
                    </div>

                    <form action="{{ route('app.customer-credits.update', $customerCredit) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">Customer Name</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                            value="{{ old('customer_name', $customerCredit->customer_name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_phone">Phone Number</label>
                                        <input type="text" class="form-control" id="customer_phone" name="customer_phone" 
                                            value="{{ old('customer_phone', $customerCredit->customer_phone) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="invoice">Invoice Number</label>
                                        <input type="text" class="form-control" id="invoice" name="invoice" 
                                            value="{{ old('invoice', $customerCredit->invoice) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Total Credit Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! app(App\Settings\StoreSettings::class)->currency !!}</span>
                                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" 
                                                value="{{ old('amount', $customerCredit->amount) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="alert alert-info">
                                <strong>Payment Status Update</strong>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Original Credit</span>
                                            <span class="info-box-number">
                                                {!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($customerCredit->amount, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount_cleared">Amount Cleared/Paid <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! app(App\Settings\StoreSettings::class)->currency !!}</span>
                                            <input type="number" class="form-control" id="amount_cleared" name="amount_cleared" 
                                                step="0.01" value="{{ old('amount_cleared', $customerCredit->amount_cleared) }}" required>
                                        </div>
                                        <small class="text-muted">How much of the credit has been cleared?</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reason">Reason</label>
                                        <select class="form-control" id="reason" name="reason" required>
                                            <option value="">-- Select Reason --</option>
                                            <option value="Change not available" {{ old('reason', $customerCredit->reason) == 'Change not available' ? 'selected' : '' }}>Change not available</option>
                                            <option value="To be deducted from next purchase" {{ old('reason', $customerCredit->reason) == 'To be deducted from next purchase' ? 'selected' : '' }}>To be deducted from next purchase</option>
                                            <option value="Customer paid in advance" {{ old('reason', $customerCredit->reason) == 'Customer paid in advance' ? 'selected' : '' }}>Customer paid in advance</option>
                                            <option value="Other" {{ old('reason', $customerCredit->reason) == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_date">Due Date (Optional)</label>
                                        <input type="date" class="form-control" id="due_date" name="due_date" 
                                            value="{{ old('due_date', $customerCredit->due_date ? $customerCredit->due_date->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes...">{{ old('notes', $customerCredit->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Credit Record
                            </button>
                            <a href="{{ route('app.customer-credits.show', $customerCredit) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.getElementById('amount_cleared').addEventListener('input', function() {
            const total = {{ $customerCredit->amount }};
            const cleared = parseFloat(this.value) || 0;
            const balance = total - cleared;
            
            if (cleared > total) {
                alert('Amount cleared cannot be more than the total credit amount!');
                this.value = total;
            }
        });
    </script>
@endsection
