@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Product History: {{ $product->name }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">History</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!-- The time line -->
                        <div class="timeline">
                            @php
                                $currentDate = null;
                                // Merge audits and product histories
                                $allHistories = collect();
                                
                                // Add audits as history items
                                foreach ($audits as $audit) {
                                    $allHistories->push((object)[
                                        'type' => 'audit',
                                        'data' => $audit,
                                        'created_at' => $audit->created_at
                                    ]);
                                }
                                
                                // Add product histories
                                foreach ($productHistories as $history) {
                                    $allHistories->push((object)[
                                        'type' => 'product_history',
                                        'data' => $history,
                                        'created_at' => $history->created_at
                                    ]);
                                }
                                
                                // Sort by created_at descending
                                $allHistories = $allHistories->sortByDesc('created_at');
                            @endphp

                            @forelse ($allHistories as $item)
                                @php
                                    $itemDate = $item->created_at->format('d M. Y');
                                @endphp

                                @if ($currentDate != $itemDate)
                                    <div class="time-label">
                                        <span class="bg-red">{{ $itemDate }}</span>
                                    </div>
                                    @php
                                        $currentDate = $itemDate;
                                    @endphp
                                @endif

                                @if ($item->type === 'audit')
                                    @php
                                        $audit = $item->data;
                                    @endphp
                                    <div>
                                        <i class="fas {{ $audit->event == 'created' ? 'fa-plus bg-green' : ($audit->event == 'updated' ? 'fa-pen bg-blue' : 'fa-trash bg-red') }}"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> {{ $audit->created_at->format('h:i A') }}</span>
                                            <h3 class="timeline-header"><a href="#">{{ $audit->user->name ?? 'System' }}</a> {{ $audit->event }} this product</h3>

                                            <div class="timeline-body">
                                                @if($audit->event == 'created')
                                                    Product was created with initial values.
                                                @else
                                                    <ul class="list-unstyled">
                                                        @foreach ($audit->getModified() as $attribute => $modified)
                                                            @if(in_array($attribute, ['qty', 'buying_price', 'selling_price', 'name', 'min_qty', 'expiry_date', 'product_category', 'unit']))
                                                                <li>
                                                                    <strong>{{ ucfirst(str_replace('_', ' ', $attribute == 'product_category' ? 'category' : $attribute)) }}:</strong>
                                                                    <span class="text-danger">{{ $modified['old'] ?? 'N/A' }}</span>
                                                                    <i class="fas fa-arrow-right mx-1"></i>
                                                                    <span class="text-success">{{ $modified['new'] ?? 'N/A' }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @php
                                        $history = $item->data;
                                        $typeColors = [
                                            'sale' => 'fa-receipt bg-info',
                                            'restock' => 'fa-boxes bg-success',
                                            'adjustment' => 'fa-wrench bg-warning',
                                            'damage' => 'fa-exclamation bg-danger'
                                        ];
                                        $typeLabels = [
                                            'sale' => 'Sale/Dispense',
                                            'restock' => 'Restock',
                                            'adjustment' => 'Adjustment',
                                            'damage' => 'Damage'
                                        ];
                                    @endphp
                                    <div>
                                        <i class="fas {{ $typeColors[$history->type] ?? 'fa-box bg-secondary' }}"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> {{ $history->created_at->format('h:i A') }}</span>
                                            <h3 class="timeline-header">
                                                <a href="#">{{ $history->user->name ?? 'System' }}</a>
                                                {{ $typeLabels[$history->type] ?? ucfirst($history->type) }}
                                            </h3>

                                            <div class="timeline-body">
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <strong>Quantity Before:</strong>
                                                        <span class="badge badge-secondary">{{ $history->qty_before }}</span>
                                                    </li>
                                                    <li>
                                                        <strong>Quantity Dispensed/Sold:</strong>
                                                        <span class="badge badge-danger">{{ $history->qty_changed }}</span>
                                                    </li>
                                                    <li>
                                                        <strong>Quantity After:</strong>
                                                        <span class="badge badge-success">{{ $history->qty_after }}</span>
                                                    </li>
                                                    @if ($history->invoice)
                                                        <li>
                                                            <strong>Invoice:</strong>
                                                            <code>{{ $history->invoice }}</code>
                                                        </li>
                                                    @endif
                                                    @if ($history->buyer_name)
                                                        <li>
                                                            <strong>Buyer:</strong>
                                                            {{ $history->buyer_name }}
                                                            @if ($history->buyer_dept)
                                                                <span class="text-muted">({{ $history->buyer_dept }})</span>
                                                            @endif
                                                        </li>
                                                    @endif
                                                    @if ($history->notes)
                                                        <li>
                                                            <strong>Notes:</strong>
                                                            {{ $history->notes }}
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <div>
                                    <i class="fas fa-info bg-gray"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header">No history available for this product.</h3>
                                    </div>
                                </div>
                            @endforelse

                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
            </div>
            <!-- /.timeline -->

        </section>
    </div>
@endsection
