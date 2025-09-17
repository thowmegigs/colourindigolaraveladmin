@extends('layouts.admin.app')
@section('content')
    <div class="container-fluid">

        <div class="row gy-4">
            <!-- User Sidebar -->
            <div class="col-md-4">
                <!-- User Card -->
                <div class="card mb-4">
                    <div class="card-body">


                        <h5 class="pb-2 mb-4">Order Details</h5>
                        <div class="info-container">
                            <x-displayViewData :module="$module" :row1="$row" :modelRelations="$model_relations" :viewColumns="$view_columns"
                                :imageFieldNames="$image_field_names" :storageFolder="$storage_folder" 
                                :repeatingGroupInputs="$repeating_group_inputs"/>

                            {{-- <div class="d-flex justify-content-center pt-3">


                                <a href="editUrl" class="rounded-0 btn btn-primary me-3"><i class="fa fa-edit"></i> Edit</a>

                            </div> --}}
                        </div>
                    </div>
                </div>
                <!-- /User Card -->
                <!-- Plan Card -->

                <!-- /Plan Card -->
            </div>
            <!--/ User Sidebar -->


            <!-- User Content -->
            <div class="col-md-8">

                <!--/ User Pills -->

                <!-- Change Password -->
                <div class="card">
                    <h5 class="card-header">Items List</h5>
                    <div class="table-responsive">
                        <table class="table border-top">
                            <thead>
                                <tr>
                                    <th class="text-truncate">Image </th>
                                    <th class="text-truncate">Product Name</th>
                                    <th class="text-truncate">Variant Name</th>
                                    <th class="text-truncate">Vendor Name</th>
                                    <th class="text-truncate">Qty</th>
                                    <th class="text-truncate">Price</th>
                                    <th class="text-truncate">Sale Price</th>
                                    <th class="text-truncate">Total Discount</th>


                                </tr>
                            </thead>
                            <tbody>

                                @if (count($row->items) > 0)
                                    @foreach ($row->items as $item)
                                       @php 
                                                                        
                                                                        
                                        $imageurl=$item->variant_id
                                        ?asset('storage/products/'.$item->product_id.'/variants/'.$item->variant?->image)
                                        :asset('storage/products/'.$item->product_id.'/'.$item->product->image);
                                       @endphp
                                        <tr>
                                            <td class="text-truncate" style="word-wrap: break-word;max-width:600px;">
                                                <img src="{{$imageurl}}" style="width:50px;height:50px"/>
                                            </td>
                                            <td class="text-truncate" style="word-wrap: break-word;max-width:600px;">
                                                {{ $item->name }}</td>
                                            <td class="text-truncate" style="word-wrap: break-word;max-width:600px;">
                                                {{ $item->variant?->name }}</td>
                                            <td class="text-truncate" style="word-wrap: break-word;max-width:600px;">
                                                {{ $item->vendor?->name }}</td>
                                            <td class="text-truncate">{{ $item->qty }} </td>
                                            <td class="text-truncate">{{ $item->price }}</td>
                                            <td class="text-truncate">{{ $item->sale_price }}</td>
                                            <td class="text-truncate">{{ $item->total_discount }} </td>
                                      </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" style="text-align:center">No Data Available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($return_items->count()>0)
                <div class="card">
                    <h5 class="card-header">Return Item List</h5>
                    <div class="table-responsive">
                        <table class="table border-top">
                            <thead>
                                <tr>
                                    <th class="text-truncate"># </th>
                                    <th class="text-truncate">Image </th>
                                    <th class="text-truncate">Product Name</th>
                                    <th class="text-truncate">Variant Name</th>
                                    <th class="text-truncate">Vendor Name</th>
                                    <th class="text-truncate">Return type</th>
                                    <th class="text-truncate">Refundable Amount</th>
                                    <th class="text-truncate">Date</th>
                                   


                                </tr>
                            </thead>
                            <tbody>

                                    @foreach ($return_items as $item)
                                       @php 
                                               $order_item=$item->order_item;                         
                                                                        
                                        $imageurl=$order_item->variant_id
                                        ?asset('storage/products/'.$order_item->product_id.'/variants/'.$order_item->variant?->image)
                                        :asset('storage/products/'.$order_item->product_id.'/'.$order_item->product->image);
                                       @endphp
                                        <tr>
                                            <td class="text-truncate" style="word-wrap: break-word;max-width:600px;">
                                                <a href="/return_items/{{$item->id}}" class="btn btn-outline btn-primary btn-sm" 
                                               >View </a>
                                            </td>
                                            <td class="text-truncate" style="word-wrap: break-word;max-width:600px;">
                                                <img src="{{$imageurl}}" style="width:50px;height:50px"/>
                                            </td>
                                            <td class="text-truncate" style="word-wrap: break-word;max-width:600px;">
                                                {{ $order_item->product->name }}</td>
                                            <td class="text-truncate" style="word-wrap: break-word;max-width:600px;">
                                                {{ $order_item->variant?->name }}</td>
                                            <td class="text-truncate" style="word-wrap: break-word;max-width:600px;">
                                                {{ $order_item->vendor?->name }}</td>
                                            <td class="text-truncate">{{ $item->type }} </td>
                                            <td class="text-truncate">{{ $item->refund_amount }}</td>
                                             <td class="text-truncate">{{ formateDate($item->created_at) }} </td>
                                      </tr>
                                    @endforeach
                              
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @if($row->applied_coupons)
                  <div class="card">
                    <h5 class="card-header">Applied Coupons </h5>
                    <div class="table-responsive">
                        <table class="table border-top">
                            <thead>
                                <tr>
                                    <th class="text-truncate">Coupon Name </th>
                                    <th class="text-truncate">Coupon Type </th>
                                    <th class="text-truncate">Coupon Code</th>
                                    <th class="text-truncate">Discount Method</th>
                                    


                                </tr>
                            </thead>
                            <tbody>
                               
                                @if ($row->applied_coupons)
                                    @foreach ($row->applied_coupons as $item)
                                    
                                        <tr>
                                           
                                            <td class="text-truncate" style="word-wrap: break-word;max-width:600px;">
                                                {{ $item->coupon->name}}</td>
                                           
                                            <td class="text-truncate">{{ $item->coupon_type }}</td>
                                            <td class="text-truncate">{{ $item->coupon_method}}</td>
                                            <td class="text-truncate">{{ $item->coupon->coupon_code }}</td>
                                        
                                      </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" style="text-align:center">No Data Available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
              
                <!--/ Change Password -->


                <!--/ Two-steps verification -->

                <!-- Recent Devices -->

                <!--/ Recent Devices -->
            </div>
            <!--/ User Content -->
        </div>



    </div>
@endsection
