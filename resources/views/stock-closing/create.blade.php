@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Record Stock Closing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.stock-closing.index', ['month' => $month, 'year' => $year]) }}">Stock Closing</a></li>
                            <li class="breadcrumb-item active">Record</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title">{{ $product->name }}</h3>
                            </div>
                            <div class="card-body">
                                <!-- Period Info -->
                                <div class="alert alert-info">
                                    <strong>Period:</strong> {{ $periodStart->format('d M Y') }} - {{ $periodEnd->format('d M Y') }}
                                </div>

                                <!-- Calculation Summary -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="info-box bg-lightblue">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Opening Qty</span>
                                                <span class="info-box-number">{{ $openingQty }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-orange">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Dispensed</span>
                                                <span class="info-box-number">{{ $qtyDispensed }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <strong>Expected Closing Qty:</strong> {{ $openingQty }} - {{ $qtyDispensed }} = <strong>{{ $expectedClosing }}</strong>
                                </div>

                                <!-- Form -->
                                <form method="POST" action="{{ route('app.stock-closing.store', $product) }}">
                                    @csrf
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="year" value="{{ $year }}">

                                    <div class="form-group">
                                        <label for="closing_qty">Actual Closing Quantity <span class="text-danger">*</span></label>
                                        <input 
                                            type="number" 
                                            id="closing_qty" 
                                            name="closing_qty" 
                                            class="form-control @error('closing_qty') is-invalid @enderror"
                                            value="{{ $existingClosing?->closing_qty ?? $expectedClosing }}"
                                            min="0"
                                            required
                                        >
                                        @error('closing_qty')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div id="varianceAlert" class="alert alert-warning" style="display: none;">
                                        <strong>Variance: <span id="varianceValue">0</span></strong>
                                        <small class="d-block mt-2" id="varianceNote"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea 
                                            id="notes" 
                                            name="notes" 
                                            class="form-control @error('notes') is-invalid @enderror"
                                            rows="4"
                                            placeholder="e.g., Missing items due to damage, expiry, etc."
                                        >{{ $existingClosing?->notes }}</textarea>
                                        @error('notes')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Save Stock Closing
                                        </button>
                                        <a href="{{ route('app.stock-closing.index', ['month' => $month, 'year' => $year]) }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar: Sales History -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h3 class="card-title">Dispense History ({{ $qtyDispensed }} items)</h3>
                            </div>
                            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                                @forelse ($salesHistory as $sale)
                                    <div class="timeline-item">
                                        <i class="fas fa-receipt bg-blue"></i>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">
                                                <strong>Qty: {{ $sale->qty_changed }}</strong>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> {{ $sale->user->name ?? 'System' }}
                                            </small>
                                            <br>
                                            @if ($sale->buyer_name)
                                                <small class="text-muted">
                                                    <i class="fas fa-users"></i> {{ $sale->buyer_name }}
                                                </small>
                                                <br>
                                            @endif
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> {{ $sale->created_at->format('d M Y H:i') }}
                                            </small>
                                            <br>
                                            @if ($sale->invoice)
                                                <small class="badge badge-primary">{{ $sale->invoice }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-info">
                                        No dispenses recorded for this period
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.getElementById('closing_qty').addEventListener('change', function() {
            const expectedClosing = {{ $expectedClosing }};
            const closingQty = parseInt(this.value) || 0;
            const variance = closingQty - expectedClosing;
            
            const alert = document.getElementById('varianceAlert');
            const varianceValue = document.getElementById('varianceValue');
            const varianceNote = document.getElementById('varianceNote');
            
            varianceValue.textContent = (variance >= 0 ? '+' : '') + variance;
            
            if (variance === 0) {
                alert.style.display = 'none';
            } else if (variance > 0) {
                alert.classList.remove('alert-warning', 'alert-danger');
                alert.classList.add('alert-success');
                alert.style.display = 'block';
                varianceNote.textContent = '✓ Extra items found (surplus)';
            } else {
                alert.classList.remove('alert-success', 'alert-warning');
                alert.classList.add('alert-danger');
                alert.style.display = 'block';
                varianceNote.textContent = '⚠ Missing items (shortage)';
            }
        });
        
        // Trigger on page load
        document.getElementById('closing_qty').dispatchEvent(new Event('change'));
    </script>
@endsection
