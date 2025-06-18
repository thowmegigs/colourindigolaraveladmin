@extends('layouts.frontend.app')
@section('content')
    <section class="py-5 products-listing bg-light">
    <div class="container" x-data="{
                    html: '',
                    loading: false,
                    controller: null,
                    category: [],
                    brand: [],
                    is_in_view: false,
                    page: 1,
                    reached_end:false,
                
                
                    filter_data: {
                        sort_by: 'ASC',
                        child_categories: [],
                        brands: [],
                
                        min_price: document.querySelector('#min_price_input').value,
                        max_price: document.querySelector('#max_price_input').value
                    },
                    init() {
                        let p = this;
                
                        $(window).scroll(function() {
                
                            const elementToCheck = document.getElementById('end');
                            if (isInView(elementToCheck)) {
                                console.log('in view')
                                console.log(p.is_in_view)
                               
                                if (!p.is_in_view && !p.reached_end) {
                                    p.is_in_view=true;
                                    p.page++;
                                    p.loadMore()
                                }
                            }
                        });
                        jQuery('#min_price_input').change(function() {
                
                            p.filter_data.min_price = $(this).val()
                        })
                        jQuery('#max_price_input').change(function() {
                            p.filter_data.max_price = $(this).val()
                        })
                        this.fetchContent();
                        this.$watch('filter_data', (v) => {
                            this.filter()
                        })
                        {{-- this.$watch('page', (v) => {
                            this.fetchContent()
                        }) --}}
                
                
                    },
                    fetchContent(reset=false) {
                
                        this.loading = true;
                         !reset?showLoader('end','Loading..'):showLoader(null,'Loading...');
                        
                        fetch('/ajax_collection_product_list?page='+this.page, {
                
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({...this.filter_data,page:this.page})
                        }).then(response => response.json()).then(res => {
                            this.loading = false;
                            !reset?hideLoader('end'):hideLoader();
                            this.is_in_view = false;
                            if (res['success']) {
                            $('#product_count').text(res['product_count']);
                                        if(res['current_count']>0)
                                    { 
                                        if(!reset)
                                        this.html+=res['view'];
                                        else
                                        this.html=res['view'];
                                    
                                        setTimeout(function() {
                                            jQuery('.btn-quickview1').magnificPopup({
                                                midClick: true,
                                                mainClass: 'mfp-fade'
                                            });
                        
                                        }, 3000)
                                    }
                                    else{
                                        console.log('nothig foun')
                                        this.html='<center><p>No Product found</p></center>'
                                    }
                
                            } else {
                                vNotify.error({ text: res['message'], title: 'Error' });
                            }
                
                        })
                    },
                    loadMore() {
                
                        this.loading = true;
                        showLoader('end')
                        fetch('/ajax_collection_product_list?page='+this.page, {
                
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({...this.filter_data,page:this.page})
                        }).then(response => response.json()).then(res => {
                            this.loading = false;
                            hideLoader('end')
                            this.is_in_view = false;
                            if (res['success']) {
                                if(res['current_count']>0)
                               { 
                                if(!reset)
                                this.html+=res['view'];
                                else
                                this.html=res['view'];
                               
                                setTimeout(function() {
                                    jQuery('.btn-quickview1').magnificPopup({
                                        midClick: true,
                                        mainClass: 'mfp-fade'
                                    });
                
                                }, 3000)
                                }
                                else{
                                    this.reached_end=true;
                                    
                                }
                
                            } else {
                                vNotify.error({ text: res['message'], title: 'Error' });
                            }
                
                        })
                    },
                    
                
                    filter() {
                        this.page=1
                        this.fetchContent(true);
                
                
                
                
                    }
                }">
        <div class="row">
            <div class="col-md-3">
                <div class="filters mobile-filters shadow-sm rounded bg-white mb-4 d-none d-block d-md-none">
                    <div class="border-bottom">
                        <a class="h6 font-weight-bold text-dark d-block m-0 p-3" data-toggle="collapse"
                            href="#mobile-filters" role="button" aria-expanded="false"
                            aria-controls="mobile-filters">Filter By <i
                                class="icofont-arrow-down float-right mt-1"></i></a>
                                  
                    </div>
                    <div id="mobile-filters" class="filters-body collapse multi-collapse">
                        <div id="accordion">
                            <div class="filters-card border-bottom p-3">
                                <div class="filters-card-header" id="headingOffer">
                                    <h6 class="mb-0">
                                        <a href="#" class="btn-link" data-toggle="collapse" data-target="#collapseSort"
                                            aria-expanded="true" aria-controls="collapseSort">
                                            Sort  Products <i class="icofont-arrow-down float-right"></i>
                                        </a>
                                    </h6>
                                </div>
                                <div id="collapseSort" class="collapse" aria-labelledby="headingOffer"
                                    data-parent="#accordion">
                                    <div class="filters-card-body card-shop-filters">

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" x-model="filter_data.sort_by" value="ASC"
                                                name="sort_by" class="custom-control-input" id="osahan112">
                                            <label class="custom-control-label" for="osahan112">Price (Low to High)
                                            </label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" x-model="filter_data.sort_by" value="DESC"
                                                name="sort_by" class="custom-control-input" id="osahan113">
                                            <label class="custom-control-label" for="osahan113">Price (High to Low)
                                            </label>
                                        </div>

                                        <!--<div class="custom-control custom-checkbox">-->
                                        <!--    <input type="checkbox" x-model="filter_data.sort_by" value="name"-->
                                        <!--        name="sort_by" class="custom-control-input" id="osahan115">-->
                                        <!--    <label class="custom-control-label" for="osahan115">Name (A to Z)-->
                                        <!--    </label>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                            @if(count($child_categories) > 0)
                                <div class="filters-card border-bottom p-3">
                                    <div class="filters-card-header" id="headingTwo">
                                        <h6 class="mb-0">
                                            <a href="#" class="btn-link" data-toggle="collapse"
                                                data-target="#collapsetwo" aria-expanded="true"
                                                aria-controls="collapsetwo">
                                                 Categories
                                                <i class="icofont-arrow-down float-right"></i>
                                            </a>
                                        </h6>
                                    </div>
                                    <div id="collapsetwo" class="collapse" aria-labelledby="headingTwo"
                                        data-parent="#accordion">
                                        <div class="filters-card-body card-shop-filters">
                                            <!-- <form class="filters-search mb-3">
                                       <div class="form-group">
                                          <i class="icofont-search"></i>
                                          <input type="text" class="form-control" placeholder="Start typing to search...">
                                       </div>
                                    </form> -->
                                            @foreach($child_categories as $cat)
                                                <div class="custom-control custom-checkbox">
                                                    <input x-model="filter_data.child_categories"  id="cat-{{$cat['id']}}" value="{{ $cat['id'] }}"
                                                        type="checkbox" class="custom-control-input">
                                                    <label class="custom-control-label"
                                                        for="cat-{{$cat['id']}}">{{ $cat['name'] }}</label>
                                                </div>
                                            @endforeach



                                            <!-- <div class="mt-2"><a href="#" class="link">See all</a></div> -->
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if(count($brands_list) > 0 && $brands_list[0]['id']!=null)
                                <div class="filters-card border-bottom p-3">
                                    <div class="filters-card-header" id="headingOne">
                                        <h6 class="mb-0">
                                            <a href="#" class="btn-link" data-toggle="collapse"
                                                data-target="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                Brand <i class="icofont-arrow-down float-right"></i>
                                            </a>
                                        </h6>
                                    </div>
                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                        data-parent="#accordion">
                                        <div class="filters-card-body card-shop-filters">
                                            @foreach($brands_list as $cat)
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" value="{{ $cat['id'] }}" id="brnd-{{$cat['id']}}" x-model="filter_data.brands"  class="custom-control-input">
                                                    <label class="custom-control-label"
                                                        for="brnd-{{$cat['id']}}">{{ $cat['name'] }}
                                                    </label>
                                                </div>
                                            @endforeach



                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="filters-card border-bottom p-3">
                                <div class="filters-card-header" id="headingOffer">
                                    <h6 class="mb-0">
                                        <a href="#" class="btn-link" data-toggle="collapse" data-target="#collapseOffer"
                                            aria-expanded="true" aria-controls="collapseOffer">
                                            Price <i class="icofont-arrow-down float-right"></i>
                                        </a>
                                    </h6>
                                </div>
                                <div id="collapseOffer" class="collapse" aria-labelledby="headingOffer"
                                    data-parent="#accordion">
                                    <div>
                                        @php
                                            $middle_val= ($minPrice+$maxPrice)/2;
                                        @endphp
                                        <input name="min_price" type="hidden" value="{{ $minPrice }}"
                                            id="min_price_input" />
                                        <input name="max_price" type="hidden" value="{{ $maxPrice }}"
                                            id="max_price_input" />
                                        <input type="text" data-from="{{ $minPrice }}" data-to="{{ $maxPrice }}"
                                            data-min="{{ $minPrice }}" data-max="{{ $maxPrice }}"
                                            class="js-range-slider" name="my_range" value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="filters shadow-sm rounded bg-white mb-3 d-none d-sm-none d-md-block">
                    <div class="filters-header border-bottom pl-4 pr-4 pt-3 pb-3">
                        <h5 class="m-0 text-dark">Filter By</h5>
                        
                    </div>
                    <div class="filters-body">
                        <div id="accordion">
                            @if(count($child_categories) > 0)
                                <div class="filters-card border-bottom p-4">
                                    <div class="filters-card-header" id="headingTwo">
                                        <h6 class="mb-0">
                                            <a href="#" class="btn-link" data-toggle="collapse"
                                                data-target="#collapsetwo" aria-expanded="true"
                                                aria-controls="collapsetwo">
                                                Categories
                                                <i class="icofont-arrow-down float-right"></i>
                                            </a>
                                        </h6>
                                    </div>
                                    <div id="collapsetwo" class="collapse show" aria-labelledby="headingTwo"
                                        data-parent="#accordion">
                                        <div class="filters-card-body card-shop-filters">
                                            @foreach($child_categories as $cat)
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" value="{{ $cat['id'] }}" id="catm-{{$cat['id']}}" x-model="filter_data.child_categories" class="custom-control-input"
                                                        >
                                                    <label class="custom-control-label"
                                                        for="catm-{{$cat['id']}}">{{ $cat['name'] }} </label>
                                                </div>
                                            @endforeach




                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if(count($brands_list) > 0 && $brands_list[0]['id']!=null)
                           
                                <div class="filters-card border-bottom p-4">
                                    <div class="filters-card-header" id="headingOne">
                                        <h6 class="mb-0">
                                            <a href="#" class="btn-link" data-toggle="collapse"
                                                data-target="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                Brand <i class="icofont-arrow-down float-right"></i>
                                            </a>
                                        </h6>
                                    </div>
                                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                        data-parent="#accordion">
                                        <div class="filters-card-body card-shop-filters">
                                            @foreach($brands_list as $cat)
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox"  id="brndm-{{$cat['id']}}" class="custom-control-input" value="{{ $cat['id'] }}"  x-model="filter_data.brands" >
                                                    <label class="custom-control-label"
                                                        for="brndm-{{$cat['id']}}">{{ $cat['name'] }}

                                                    </label>
                                                </div>

                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="filters-card border-bottom p-4">
                                <div class="filters-card-header mb-3" id="headingOffer">
                                    <h6 class="mb-0">
                                        <a href="#" class="btn-link" data-toggle="collapse" data-target="#collapseOffer"
                                            aria-expanded="true" aria-controls="collapseOffer">
                                            Price 
                                        </a>
                                    </h6>
                                </div>
                                <div id="collapseOffer" class="collapse show" aria-labelledby="headingOffer"
                                    data-parent="#accordion">
                                     <div>
                                        @php
                                            $middle_val= ($minPrice+$maxPrice)/2;
                                        @endphp
                                        <input name="min_price" type="hidden" value="{{ $minPrice }}"
                                            id="min_price_input" />
                                        <input name="max_price" type="hidden" value="{{ $maxPrice }}"
                                            id="max_price_input" />
                                        <input type="text" data-from="{{ $minPrice }}" data-to="{{ $maxPrice }}"
                                            data-min="{{ $minPrice }}" data-max="{{ $maxPrice }}"
                                            class="js-range-slider" name="my_range" value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <img src="img/offer-2.png" class="w-100 bg-white rounded overflow-hidden position-relative shadow-sm d-none d-sm-none d-md-block" alt="..."> -->
            </div>
            <div class="col-md-9">
                <div class="shop-head mb-3">
                    <div class="btn-group float-right mt-2 d-none d-sm-none d-md-block">
                        <button type="button" class="btn btn-dark btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="icofont icofont-filter"></span> Sort  Products &nbsp;&nbsp;
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">

                            <a class="dropdown-item" @click="filter_data.sort_by='ASC'" href="#">Price (Low to High)</a>
                            <a class="dropdown-item" href="#" @click="filter_data.sort_by='DESC'">Price (High to
                                Low)</a>

                            <!--<a class="dropdown-item" href="#" @click="filter_data.sort_by='name'">Name (A to Z)</a>-->
                        </div>
                    </div>
                    <h5 class="mb-1 text-dark">{{ $collection->name }}</h5>
                   
                </div>
               
                <div class="row" x-html="html" id="here">


                </div>
                <div id="end" style="height:100px;"></div>
            </div>
        </div>
    </div>
    </div>
</section>

@endsection
