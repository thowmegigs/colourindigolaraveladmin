@props(['order'])

<div class="modal fade" role="dialog"  id="order_items_{{ $order->id }}" tabindex="-1"
    aria-labelledby="quickViewModalLabel" aria-hidden="true">
 
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"> Item List</h6>
                
            </div>
            <div class="modal-body">
                <div class="table-responsive border-0">

                    <table class="table mb-0 text-nowrap table-centered table-bordered">
                         <thead>
                                    <tr>
                                       <th>#</th>
                                       <th>Image</th>
                                       <th>Name</th>
                                       <th>Qty</th>
                                       <th>Cost</th>
                                      
                                    </tr>
                                 </thead>

                        <tbody>
                            @foreach ($order->items as $t)
                             <tr>
                                       <td>{{$loop->index+1}}</th>
                                       <td>  <img src="{{ $t->image }}" style="width:80px;height:80px;object-fit:contain"
                                                        alt="Ecommerce" class="icon-shape icon-xl" /> </th>
                                       <td>{{ $t->name }} <span><small
                                                                class="text-muted">{{ str_replace('_', ' ', $t['variant_name']) }}</small></span></th>
                                       <td>{{ $t->qty }}</th>
                                       <td>{{ getCurrency() }}{{ $t['net_cart_amount'] }}</th>
                                      
                                    </tr>
                               
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
