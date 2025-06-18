@extends('layouts.admin.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!--Modal end-->
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between flex-wrap">
                    <h5>All items Sold </h5>
                  
                </div>
              


            </div>
            <div class="card-body">
                @forelse ($orders as $order)
        {{-- ---------- Order Header ---------- --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <strong>Order #{{ $order->uuid }}</strong>
                    <span class="text-muted ms-2">{{ $order->created_at}}</span>
                </div>
                <span class="badge bg-success">
                    {{ $order->delivery_status ?? 'Pending' }}
                </span>
            </div>

            {{-- ---------- Items Table ---------- --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th scope="col">Image</th>
                            <th scope="col">Item</th>
                            <th scope="col">Variant</th>
                            <th scope="col">Qty Sold </th>
                            <th scope="col">Current Stock</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->order->items as $item)
                        @php 
                        $img=$item->product?->image?asset('storage/products/'.$item->product_id.'/'.$item->product?->image)
                        :asset('assets/images/placeholder.png');
                        $current_stock=$item->variant_id?$item->variant->quantity:$item->product->quantity;
                        @endphp
                            <tr>
                                {{-- Image --}}
                                <td class="text-center" style="width: 80px;">
                                    <img
                                        src="{{ $img}}"
                                        class="img-fluid rounded"
                                        alt="{{ $item->product?->name }}"
                                        style="max-height: 60px;"
                                    >
                                </td>

                                {{-- Name --}}
                                <td>
                                    <strong>{{ $item->product?->name }}</strong><br>
                                    <small class="text-muted">#{{ $item->product->sku }}</small>
                                </td>

                                {{-- Variant --}}
                                <td class="text-center">
                                    {{ $item->variant?->name ?? '-' }}
                                </td>

                                {{-- Quantity --}}
                                <td class="text-center">
                                    {{ $item->qty }}
                                </td>
                                <td class="text-center">
                                    @if(5>$current_stock)
                                    <span class="alert alert-danger">{{$current_stock}}</span>
                                    @else 
                                     <span >{{$current_stock}}
                                    @endif
                                </td>

                               
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

           
        </div>
    @empty
        <div class="alert alert-info">No orders to display.</div>
    @endforelse
              
            </div>
        </div>
    </div>
@endsection
