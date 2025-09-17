@extends('layouts.vendor.app')
@section('content')
    <div class="container-xxl flex-grow-1">

        @if($orders->isNotEmpty())
        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header font-bold py-2 d-flex justify-content-between align-items-center">
                <div>
                   <h5 class="mb-0">Sales Report</h5>
                    <small class="text-muted">Shows the product items sold and their stocks</small>
                </div>
                <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
                    href="#filterCollapse" style="cursor:pointer">
                    <h6 class="mb-0"><i class="bi bi-funnel-fill me-2"></i> Filters</h6>
                    <i class="bi bi-chevron-down toggle-icon ms-1"></i>
                </div>
            </div>

            <div class="card mb-3">
                <div class="collapse show" id="filterCollapse">
                    <div class="card-body">
                        <form method="GET" action="/sales" class="row g-3 align-items-end">
                            <div class="col-md-3 position-relative">
                                <label for="start_date" class="form-label"><i class="bi bi-calendar-event me-1"></i> Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>

                            <div class="col-md-3 position-relative">
                                <label for="end_date" class="form-label"><i class="bi bi-calendar-event-fill me-1"></i> End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>

                            <div class="col-md-3 position-relative">
                                <label for="order_uuid" class="form-label"><i class="bi bi-card-checklist me-1"></i> Order ID</label>
                                <input type="text" id="order_id" name="order_uuid" class="form-control" placeholder="Enter Order ID" value="{{ request('order_uuid') }}">
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
                                <a href="/sales" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise me-1"></i> Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Rotate chevron icon on collapse -->
            <script>
                document.querySelectorAll('.card-header[data-bs-toggle="collapse"]').forEach(header => {
                    const icon = header.querySelector('.toggle-icon');
                    const target = document.querySelector(header.getAttribute('href'));
                    target.addEventListener('shown.bs.collapse', () => {
                        icon.classList.add('bi-chevron-up');
                        icon.classList.remove('bi-chevron-down');
                    });
                    target.addEventListener('hidden.bs.collapse', () => {
                        icon.classList.add('bi-chevron-down');
                        icon.classList.remove('bi-chevron-up');
                    });
                });
            </script>
        </div>
        @endif

        <!-- Orders Section -->
        @forelse ($orders as $order)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger text-white d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <span><strong>Order #:</strong> {{ $order->uuid }}</span>
                        <span class="ms-2"><strong>Date:</strong> {{ formateDate($order->created_at) }}</span>
                    </div>
                    <span class="badge bg-success">{{ $order->delivery_status ?? 'Pending' }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm align-middle mb-0">
                        <thead class="text-center table-light">
                            <tr>
                                <th style="width:80px">Image</th>
                                <th>Item</th>
                                <th>Variant</th>
                                <th>Qty Sold</th>
                                <th>Current Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order?->order?->items ?? [] as $item)
                                @php
                                    $img = $item->product?->image
                                        ? asset('storage/products/' . $item->product_id . '/' . $item->product?->image)
                                        : asset('assets/images/placeholder.png');
                                    $current_stock = $item->variant_id
                                        ? $item->variant->quantity
                                        : $item->product->quantity;
                                @endphp
                                <tr class="text-center align-middle">
                                    <td>
                                        <img src="{{ $img }}" class="img-fluid rounded" style="max-height:60px;" alt="{{ $item->product?->name }}">
                                    </td>
                                    <td class="text-start">
                                        <strong>{{ \Str::limit($item->product?->name, 80) }}</strong><br>
                                        <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                    </td>
                                    <td>{{ $item->variant?->name ?? '-' }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>
                                        @if ($current_stock < 5)
                                            <span class="badge bg-danger">{{ $current_stock }}</span>
                                        @else
                                            <span>{{ $current_stock }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white d-flex flex-column align-items-center justify-content-center py-5">
                <i class="bi bi-emoji-frown fs-1 text-muted mb-3"></i>
                <h5 class="text-muted">No Sell available</h5>
                <p class="text-muted">You will see your sales report here once orders are placed.</p>
            </div>
        @endforelse
    </div>
@endsection
