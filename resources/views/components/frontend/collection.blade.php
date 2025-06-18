@props(['row'])
@if ($row->collections->count() == 1)

    @php
        $collection = $row->collections->first();
        $end = \Carbon\Carbon::parse($collection->end_date);
        $now = \Carbon\Carbon::now();
        $days = $end->diffInDays($now);
        $secs = $end->diffInSeconds($now);
        $mins = $end->diffInMinutes($now);
        $hrs = $end->diffInHours($now);
        $collection_products = $collection->collection_products;
    @endphp
    <section class="product-list  pt-5 bg-light">
        <div class="container">
        <!--<h6 class="mt-1 mb-0 float-right"><a href="#">View All Items</a></h6>-->
        <h4 class="mt-0 mb-3 text-dark font-weight-normal section_heading">{{ $row->section_title}}</h4>
        <div class="row">

                 @foreach ($collection_products as $cp)
                  <div class="col-md-3">
                    <x-frontend.product :product="$cp" />
                   </div>
                @endforeach


            </div>
        </div>
</section>
  
@else
    @php
        $n = $row->collections->count();
    @endphp
    <section class="product-list  pt-5 bg-light">
        <div class="container">
            @if($row->section_title)
               <h4 class="mt-0 mb-3 text-dark font-weight-bold section_heading">{{ $row->section_title}}</h4>
        @endif
            <div class="row">
            @foreach ($row->collections as $cl)
            <div class="col">
                    <div class="offers-block">
                        <a href="/collection_product_list/{{$cl->id}}">
                            <img class="img-fluid" src="{{ asset('storage/collections/' . $cl->image) }}" style="max-height:214px" alt></a>
                    </div>
                </div>
                
            @endforeach

            </div>
           

        </div>
</section>
@endif
