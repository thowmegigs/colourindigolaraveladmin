@props(['product'])
    @php
        $p = $product;
        $cart = session('shoppingCart', []);
        $unique_cart_item_id = $p->id;
        $unique_cart_item_id = trim($unique_cart_item_id);
        // dd($unique_cart_item_id);
        $qty = !empty($cart) ? cartProductCount($cart, $p->id) : 0;
        $percent = intval((($p->price - $p->sale_price) / $p->price) * 100);
    @endphp
    <div class="card list-item bg-white rounded overflow-hidden position-relative shadow-sm">
        
        <a href="/product/{{ $p->slug }}">
            <!--<span class="badge badge-danger">NEW</span>-->
            <img src="/product_image/{{ $p->id }}/{{ $p->image }}?width=300&height=300" 
                class="card-img-top prod_image img-fluid" alt="..."></a>
        <div class="card-body">
            <h6 class="card-title mb-1">{{ $p->name }}</h6>
            
            <p class="mb-0 text-dark">{{ getCurrency() }}{{ $p->sale_price }} <span class="text-black-50">
                    <del>{{ getCurrency() }}{{ $p->price }} </del></span><br>
               </p>
               <div style="display:flex;justify-content:space-between">
                    <span class="bg-danger  rounded-sm pl-1 ml-1 pr-1 text-white small"> {{ $percent }}% OFF</span>
                    @if($p->quantity==0)
                     <span class="badge badge-danger" style="position:relative!important;top:unset"> Out Of Stock</span>
                     @endif
               </div>
        </div>
    </div>
