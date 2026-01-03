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
                            @endphp

                            @forelse ($audits as $audit)
                                @php
                                    $auditDate = $audit->created_at->format('d M. Y');
                                @endphp

                                @if ($currentDate != $auditDate)
                                    <div class="time-label">
                                        <span class="bg-red">{{ $auditDate }}</span>
                                    </div>
                                    @php
                                        $currentDate = $auditDate;
                                    @endphp
                                @endif

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
