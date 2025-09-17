@extends('layouts.frontend.app')
@section('content')
    <main>
        <!-- section -->
        <section>
            <div class="container">
                <!-- row -->
                <div class="row">
                    <!-- col -->
                   
                    <div class="col-lg-3 col-md-4 col-12 border-end d-none d-md-block">
                        <div class="pt-10 pe-lg-10">
                            <!-- nav -->
                            @include('frontend.account.sidebar')
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                        <div class="py-6 p-md-6 p-lg-10">
                            <!-- heading -->
                            <h2 class="mb-6">My Orders</h2>
                         {{--   @foreach ($orders as $t)
                            <x-frontend.order_items_modal :order="$t" />
                            @endforeach--}}
                            <div class="table-responsive-xxl border-0">
                                <!-- Table -->
                                <table class="table mb-0 text-nowrap table-centered">
                                    <!-- Table Head -->
                                    <thead class="bg-light">
                                        <tr>
                                             <th>Order #</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Status</th>
                                            <th>Total Amount</th>
                                            <th>Payment Method</th>

                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $t)
                                            <tr>

                                                <td class="align-middle border-top-0">
                                                    #{{ $t->uuid }}
                                                </td>
                                                <td class="align-middle border-top-0">{{ formateDate($t->created_at) }}</td>
                                                <td class="align-middle border-top-0">{{ $t->items->count() }}
                                                    &nbsp; &nbsp; <b style="font-size:12px;color:black"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#order_items_{{ $t->id }}">Show Items</b>
                                                </td>
                                                <td class="align-middle border-top-0">
                                                    @if($t->delivery_status!='Delivered')
                                                    <span class="badge bg-warning">{{ $t->delivery_status }}</span>
                                                    @else
                                                    <span class="badge bg-success">{{ $t->delivery_status }}</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle border-top-0">{{ getCurrency() }}{{ $t->net_payable }}
                                                </td>
                                                <td class="text-muted align-middle border-top-0">
                                                    {{ $t->payment_method }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
