@props(['row'])
<section class="product-list pbc-5 pb-4 pt-5 bg-light">
        <div class="container">
        <!--<h6 class="mt-1 mb-0 float-right"><a href="#">View All Items</a></h6>-->
        <h4 class="mt-0 mb-3 text-dark font-weight-normel">{{ $row->section_title}}</h4>
       
           
                <div class="owl-carousel owl-carousel-category owl-theme">
                    @if ($row->products->count() > 0)
                    @foreach ($row->products as $p)
                    <div class="item mx-2">
                        <x-frontend.product :product="$p" />
                    </div>
                    @endforeach
                    @endif


                </div>
           
         </div>
    </div>
</section>
