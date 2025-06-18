@props(['row'])
@php
    $ar_colors = ['bg-9', 'bg-10', 'bg-11', 'bg-12', 'bg-13', 'bg-14', 'bg-15'];
@endphp
<section class="product-list  pt-5 bg-light">
        <div class="container">
        <!--<h6 class="mt-1 mb-0 float-right"><a href="#">View All Items</a></h6>-->
        <h4 class="mt-0 mb-3 text-dark font-weight-bold section_heading">{{ $row->section_title}}</h4>
        <div class="swiper swiper_cat">
  <!-- Additional required wrapper -->
  <div class="swiper-wrapper" style="height:156px;">
    @if ($row->categories->count() > 0)
                    @foreach ($row->categories as $g)
                      @if($g->image)
                    <div class="swiper-slide">
                       
                        <figure class="category-media text-center" >
                            <a href="/products/{{$g->name }}">
                                <img alt="" 
                                class="shimmer-background1" src="/category_image/{{ $g->image }}?width=200&height=200"
                                   
                                    style="height: 100px;
    border-radius: 50%;"/>
                            </a>
                        </figure>
                        <div class="category-content text-center">
                            <h6 class="category-name text-center">
                                <a href="/products/{{ $g->name }}">{{ $g->name }}</a>
                            </h6>
                        </div>
                    </div>
                    @endif
                    @endforeach
                    @endif
  </div>
 
  <div class="swiper-pagination"></div>

 
</div>
        <div class="container">
           
                <div class="row">
                   


                </div>
                </div>
           
         </div>
    </div>
</section>

