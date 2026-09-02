@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Add Customer Credit</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.customer-credits.index') }}">Customer Credits</a></li>
                            <li class="breadcrumb-item active">Add New</li>
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
                        <h3 class="card-title">Record Customer Change/Credit</h3>
                    </div>

                    <form action="{{ route('app.customer-credits.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                            id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                                        @error('customer_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_phone">Phone Number</label>
                                        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                            id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                                        @error('customer_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="invoice">Invoice Number</label>
                                        <input type="text" class="form-control @error('invoice') is-invalid @enderror" 
                                            id="invoice" name="invoice" value="{{ old('invoice') }}">
                                        @error('invoice')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Amount <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! app(App\Settings\StoreSettings::class)->currency !!}</span>
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                id="amount" name="amount" step="0.01" value="{{ old('amount') }}" required>
                                        </div>
                                        @error('amount')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reason">Reason <span class="text-danger">*</span></label>
                                        <select class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" required>
                                            <option value="">-- Select Reason --</option>
                                            <option value="Change not available" {{ old('reason') == 'Change not available' ? 'selected' : '' }}>Change not available</option>
                                            <option value="To be deducted from next purchase" {{ old('reason') == 'To be deducted from next purchase' ? 'selected' : '' }}>To be deducted from next purchase</option>
                                            <option value="Customer paid in advance" {{ old('reason') == 'Customer paid in advance' ? 'selected' : '' }}>Customer paid in advance</option>
                                            <option value="Other" {{ old('reason') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('reason')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_date">Due Date (Optional)</label>
                                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                            id="due_date" name="due_date" value="{{ old('due_date') }}">
                                        @error('due_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                    id="notes" name="notes" rows="3" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Credit Record
                            </button>
                            <a href="{{ route('app.customer-credits.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
