@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Customer Credits</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Customer Credits</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pending Credits</span>
                                <span class="info-box-number">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($pending, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Fully Cleared</span>
                                <span class="info-box-number">{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($cleared, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('app.customer-credits.create') }}" class="btn btn-primary btn-block" style="padding: 20px;">
                            <i class="fas fa-plus"></i> Add New Credit
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Customer Credits</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Cleared</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Recorded By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($credits as $credit)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $credit->customer_name }}</td>
                                        <td>{{ $credit->customer_phone ?? 'N/A' }}</td>
                                        <td>{{ $credit->invoice ?? 'N/A' }}</td>
                                        <td>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($credit->amount, 2) }}</td>
                                        <td>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($credit->amount_cleared, 2) }}</td>
                                        <td>
                                            <strong>{!! app(App\Settings\StoreSettings::class)->currency !!} {{ number_format($credit->balance_due, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($credit->status === 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($credit->status === 'partially_cleared')
                                                <span class="badge badge-info">Partially Cleared</span>
                                            @else
                                                <span class="badge badge-success">Fully Cleared</span>
                                            @endif
                                        </td>
                                        <td>{{ $credit->user->name }}</td>
                                        <td>{{ $credit->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('app.customer-credits.show', $credit) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($credit->status !== 'fully_cleared')
                                                    <a href="{{ route('app.customer-credits.edit', $credit) }}" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                <form action="{{ route('app.customer-credits.destroy', $credit) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No customer credits found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $credits->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
