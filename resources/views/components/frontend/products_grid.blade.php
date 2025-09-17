@props(['row'])
    @if(count($row->products) > 0)
        <section class="product-list pt-5 bg-light">
            <div class="container">
                <!--<h6 class="mt-1 mb-0 float-right"><a href="#">View All Items</a></h6>-->
                <h4 class="mt-0 mb-3 text-dark font-weight-bold section_heading">{{ $row->section_title }}</h4>
                <div class="row">

                    @if($row->products->count() > 0)
                        @foreach($row->products as $p)
                            <div class="col-6 col-md-4 col-lg-3">
                                <x-frontend.product :product="$p" />
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

    @endif
