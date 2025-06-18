@extends('layouts.frontend.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('commonjs/ion.rangeSlider.min.css') }}" />
<style>
    .loadingoverlay{
        justify-content:start!important;
    }
</style>
    <main class="main">
        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav">
            <div class="container">
                <ul class="breadcrumb bb-no">
                    <li><a href="/">Home</a></li>
                    <li>{{$section_name}}</li>
                </ul>
            </div>
        </nav>
        <!-- End of Breadcrumb -->

        <!-- Start of Page Content -->
        <div class="page-content">
            <div class="container">

                {{-- @if ($child_categories->count() > 0)
                    <div class="shop-default-category category-ellipse-section mb-6">
                        <div class="swiper-container swiper-theme shadow-swiper"
                            data-swiper-options="{
                    'spaceBetween': 20,
                    'slidesPerView': 2,
                    'breakpoints': {
                        '480': {
                            'slidesPerView': 3
                        },
                        '576': {
                            'slidesPerView': 4
                        },
                        '768': {
                            'slidesPerView': 6
                        },
                        '992': {
                            'slidesPerView': 7
                        },
                        '1200': {
                            'slidesPerView': 8,
                            'spaceBetween': 30
                        }
                    }
                }">
                            <div
                                class="swiper-wrapper row gutter-lg cols-xl-8 cols-lg-7 cols-md-6 cols-sm-4 cols-xs-3 cols-2">
                                @foreach ($child_categories as $g)
                                    <div class="swiper-slide category-wrap">
                                        <div class="category category-ellipse">
                                            <figure class="category-media">
                                                <a href="/category/{{ \Str::slug($g->name) }}">
                                                    <img src="{{ asset('storage/categories/' . $g->image) }}" alt="Categroy"
                                                        width="190" height="190"
                                                        style="width:190px;height:auto;background-color: #5C92C0;" />
                                                </a>
                                            </figure>
                                            <div class="category-content">
                                                <h4 class="category-name">
                                                    <a href="/category/{{ \Str::slug($g->name) }}">{{ $g->name }}</a>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                @endif
                --}}
                <!-- End of Shop Category -->

                <!-- Start of Shop Content -->
                <div class="shop-content row gutter-lg mb-10" x-data="{
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
                        
                        fetch('/ajax_more_products?page='+this.page, {
                
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
                        fetch('/ajax_more_products?page='+this.page, {
                
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
                    <!-- Start of Sidebar, Shop Sidebar -->
                    <aside class="sidebar shop-sidebar sticky-sidebar-wrapper sidebar-fixed">
                        <!-- Start of Sidebar Overlay -->
                        <div class="sidebar-overlay"></div>
                        <a class="sidebar-close" href="#"><i class="close-icon"></i></a>

                        <!-- Start of Sidebar Content -->
                        <div class="sidebar-content scrollable">
                            <!-- Start of Sticky Sidebar -->
                            <div class="sticky-sidebar">
                                <div class="filter-actions">
                                    <label>Filter :</label>

                                </div>

                                <!-- End of Collapsible Widget -->
                                @if (count($child_categories) > 0)
                                    <div class="widget">
                                        <h3 class="widget-title"><label>Categories</label></h3>
                                        <ul class="widget-body filter-items  mt-1" style="max-height:300px;overflow-y:auto">
                                            @foreach ($child_categories as $cat)
                                                <li>
                                                    <div class="d-flex  align-items-center">
                                                        <input value="{{ $cat['id'] }}" type="checkbox"
                                                            style="width:50px;height:16px" class="form-check"
                                                            x-model="filter_data.child_categories" />
                                                        <p class="p-0 mb-0">{{ $cat['name'] }}</p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if (count($brands_list) > 0)
                                    <div class="widget">
                                        <h3 class="widget-title"><label>Brands</label></h3>
                                        <ul class="widget-body filter-items item-check mt-1"
                                            style="max-height:300px;overflow-y:auto">
                                            @foreach ($brands_list as $cat)
                                                <li>
                                                    <div class="d-flex  align-items-center">
                                                        <input value="{{ $cat['id'] }}" type="checkbox"
                                                            style="width:50px;height:16px" class="form-check"
                                                            x-model="filter_data.brands" />
                                                        <p class="p-0 mb-0">{{ $cat['name'] }}</p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                 <div class="widget">
                                    <h3 class="widget-title"><label>Price</label></h3>
              
                                    <div>
                                        @php 
                                        $middle_val= ($minPrice+$maxPrice)/2;
                                        @endphp
                                        <input name="min_price" type="hidden" value="{{ $minPrice }}"
                                            id="min_price_input" />
                                        <input name="max_price" type="hidden" value="{{ $maxPrice }}"
                                            id="max_price_input" />
                                        <input type="text" data-from="{{ $minPrice }}" data-to="{{ $maxPrice }}" data-min="{{ $minPrice }}"
                                            data-max="{{ $maxPrice }}" class="js-range-slider" name="my_range"
                                            value="" />
                                    </div>

                                </div>

                            </div>
                            <!-- End of Sidebar Content -->
                        </div>
                        <!-- End of Sidebar Content -->
                    </aside>
                    <!-- End of Shop Sidebar -->

                    <!-- Start of Shop Main Content -->
                    <div class="main-content">
                        <nav class="toolbox sticky-toolbox sticky-content fix-top">
                             <div class="toolbox-left d-flex" style="align-items:baseline">
                               <h5>Total Product(s)</h5>&nbsp;&nbsp;&nbsp;<span id="product_count"></span>
                               
                            </div>
                            <div class="toolbox-right">
                                
                                <a href="#"
                                    class="btn btn-default btn-outline btn-rounded left-sidebar-toggle 
                                btn-icon-left d-block d-lg-none" style="padding:8px 22px;border: 1px solid #f2f2f2;">
                                    <img src="/filter.png" style="width:20px;height:20px;"/>&nbsp;&nbsp;Filter <span>Filters</span></a>
                                <div class="toolbox-item toolbox-sort select-box text-dark">
                                    <label>Sort By :</label>
                                    <select name="orderby" class="form-control" x-model="filter_data.sort_by">
                                        <option value="ASC" selected>Price: Low to High</option>
                                        <option value="DESC">Price: High to Low</option>
                                        <option value="Date">Release Date</option>
                                        <option value="Rating">Avg. Rating</option>
                                    </select>
                                </div>
                            </div>

                        </nav>

                        <div x-html="html" id="here" >


                        </div>
                        <div id="end" style="height:100px;"></div>


                    </div>
                    <!-- End of Shop Main Content -->
                </div>
                <!-- End of Shop Content -->
            </div>
        </div>
        <!-- End of Page Content -->
    </main>



@endsection
