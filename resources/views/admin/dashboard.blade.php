@extends('layouts.admin.app')
@section('content')

      <div class="container-fluid">

          <div class="row">
              <div class="col">

                  <div class="h-100">
                      <div class="row mb-3 pb-1">
                          <div class="col-12">
                          
                            @if(!auth()->id())
                              @php 
    $me=auth()->guard('vendor')->user();
   $is_uploaded_docs=!empty($me->gst) && !empty($me->gst_image) && !empty($me->pan)
    && !empty($me->pan_image) && !empty($me->gst) && !empty($me->trademark_image);
     @endphp
            <div class="alert alert-warning text-center mb-4" role="alert" style="font-size: 1rem; padding: 1rem;">
                <strong>Action Required:</strong> Your vendor documents are pending verification. Please upload them 
                for verification , to avoid account deactivation or rejection.
                <a href="/profile" class="btn btn-sm btn-outline-danger ms-3">Upload Now</a>
            </div>
@endif
                          </div>
                          <!--end col-->
                      </div>
                      <!--end row-->

                      <div class="row">
                        @foreach($widgets as $w)
                          <div class="col-xl-3 col-md-6">
                              <!-- card -->
                              <div class="card card-animate">
                                  <div class="card-body">
                                      <div class="d-flex align-items-center">
                                          <div class="flex-grow-1 overflow-hidden">
                                              <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> {{ucwords($w['title'])}}</p>
                                          </div>
                                          {{-- <div class="flex-shrink-0">
                                              <h5 class="text-success fs-14 mb-0">
                                                  <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +16.24 %
                                              </h5>
                                          </div> --}}
                                      </div>
                                      <div class="d-flex align-items-end justify-content-between mt-4">
                                          <div>
                                              <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                {{$w['append']}} {{$w['value']}} </h4>
                                                @if(!empty($w['link']))
                                             <a href="{{$w['link']}}" class="text-decoration-underline">View All</a>
                                             @endif
                                          </div>
                                          <div class="avatar-sm flex-shrink-0">
                                              <span class="avatar-title bg-{{$w['bg_color']}} rounded fs-3">
                                                  <i class="bx bx-dollar-circle"></i>
                                              </span>
                                          </div>
                                      </div>
                                  </div><!-- end card body -->
                              </div><!-- end card -->
                          </div><!-- end col -->
                       @endforeach
                        
                      </div> <!-- end row-->
@if(!auth()->id())
                      <div class="row">
                        <div class="col-xl-6">
                            <div class="card card-height-100">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Latest Paid Order</h4>
                                 
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                          <thead>
                                              <tr>
                                                  <th>Order #</th>
                                                  <th>Amount</th>
                                                  <th>Order Date </th>
                                                  <th>Paid Status</th>
                                              </tr>
                                          </thead>
                                            <tbody>
                                              @if(!empty($latest_paid_orders))
                                               @foreach($latest_paid_orders as $o)
                                                 @php
                                                 $o=(array)$o;
                                                  @endphp
                                                <tr>
                                                   
                                                    <td>
                                                        <span class="text-bold">Order#<br>{{$o['uuid']}}</span>
                                                    </td>
                                                    <td>
                                                        <p class="mb-0">{{getCurrency()}}{{$o['vendor_total']}}</p>
                                                      
                                                    </td>
                                                    <td>
                                                        <span class="text-bold">{{formateDate($o['created_at'])}}</span><br>
                                                        
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-14 mb-0">
                                                          <x-status :status="$o['delivery_status']" /></h5>
                                                          
                                                    </td>
                                                </tr><!-- end -->
                                                @endforeach
                                                @else
                                                <tr ><td colspan="4">No Data</td></tr>
                                              @endif
                                            </tbody>
                                        </table><!-- end table -->
                                    </div>

                                 

                                </div> <!-- .card-body-->
                            </div> <!-- .card-->
                        </div>

                          <div class="col-xl-6">
                              <div class="card card-height-100">
                                  <div class="card-header align-items-center d-flex">
                                      <h4 class="card-title mb-0 flex-grow-1">New Orders</h4>
                                   
                                  </div><!-- end card header -->

                                  <div class="card-body">
                                      <div class="table-responsive table-card">
                                          <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Amount</th>
                                                    <th>Order Date </th>
                                                    <th>Delivery Status</th>
                                                </tr>
                                            </thead>
                                              <tbody>
                                                @if(!empty($latest_pending_orders))
                                                 @foreach($latest_pending_orders as $o)
                                                   @php
                                                   $o=(array)$o;
                                                    @endphp
                                                  <tr>
                                                     
                                                      <td>
                                                          <span class="text-bold">Order#<br>{{$o['uuid']}}</span>
                                                      </td>
                                                      <td>
                                                          <p class="mb-0">{{getCurrency()}}{{$o['vendor_total']}}</p>
                                                        
                                                      </td>
                                                      <td>
                                                          <span class="text-bold">{{formateDate($o['created_at'])}}</span><br>
                                                          
                                                      </td>
                                                      <td>
                                                          <h5 class="fs-14 mb-0">
                                                           {{$o['delivery_status']}}</h5>
                                                            
                                                      </td>
                                                  </tr><!-- end -->
                                                  @endforeach
                                                  @else
                                                  <tr ><td colspan="4">No Data</td></tr>
                                                @endif
                                              </tbody>
                                          </table><!-- end table -->
                                      </div>

                                   

                                  </div> <!-- .card-body-->
                              </div> <!-- .card-->
                          </div> <!-- .col-->
                      </div> <!-- end row-->
@endif
                    
                  </div> <!-- end .h-100-->

              </div> <!-- end col -->

              <div class="col-auto layout-rightside-col">
                  <div class="overlay"></div>
                  <div class="layout-rightside">
                      <div class="card h-100 rounded-0">
                          <div class="card-body p-0">
                              <div class="p-3">
                                  <h6 class="text-muted mb-0 text-uppercase fw-semibold">Recent Activity</h6>
                              </div>
                              <div data-simplebar style="max-height: 410px;" class="p-3 pt-0">
                                  <div class="acitivity-timeline acitivity-main">
                                      <div class="acitivity-item d-flex">
                                          <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                              <div class="avatar-title bg-soft-success text-success rounded-circle shadow">
                                                  <i class="ri-shopping-cart-2-line"></i>
                                              </div>
                                          </div>
                                          <div class="flex-grow-1 ms-3">
                                              <h6 class="mb-1 lh-base">Purchase by James Price</h6>
                                              <p class="text-muted mb-1">Product noise evolve smartwatch </p>
                                              <small class="mb-0 text-muted">02:14 PM Today</small>
                                          </div>
                                      </div>
                                      <div class="acitivity-item py-3 d-flex">
                                          <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                              <div class="avatar-title bg-soft-danger text-danger rounded-circle shadow">
                                                  <i class="ri-stack-fill"></i>
                                              </div>
                                          </div>
                                          <div class="flex-grow-1 ms-3">
                                              <h6 class="mb-1 lh-base">Added new <span class="fw-semibold">style collection</span></h6>
                                              <p class="text-muted mb-1">By Nesta Technologies</p>
                                              <div class="d-inline-flex gap-2 border border-dashed p-2 mb-2">
                                                  <a href="apps-ecommerce-product-details.html" class="bg-light rounded p-1">
                                                      <img src="assets/images/products/img-8.png" alt="" class="img-fluid d-block" />
                                                  </a>
                                                  <a href="apps-ecommerce-product-details.html" class="bg-light rounded p-1">
                                                      <img src="assets/images/products/img-2.png" alt="" class="img-fluid d-block" />
                                                  </a>
                                                  <a href="apps-ecommerce-product-details.html" class="bg-light rounded p-1">
                                                      <img src="assets/images/products/img-10.png" alt="" class="img-fluid d-block" />
                                                  </a>
                                              </div>
                                              <p class="mb-0 text-muted"><small>9:47 PM Yesterday</small></p>
                                          </div>
                                      </div>
                                      <div class="acitivity-item py-3 d-flex">
                                          <div class="flex-shrink-0">
                                              <img src="assets/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                          </div>
                                          <div class="flex-grow-1 ms-3">
                                              <h6 class="mb-1 lh-base">Natasha Carey have liked the products</h6>
                                              <p class="text-muted mb-1">Allow users to like products in your WooCommerce store.</p>
                                              <small class="mb-0 text-muted">25 Dec, 2021</small>
                                          </div>
                                      </div>
                                      <div class="acitivity-item py-3 d-flex">
                                          <div class="flex-shrink-0">
                                              <div class="avatar-xs acitivity-avatar">
                                                  <div class="avatar-title rounded-circle bg-secondary shadow">
                                                      <i class="mdi mdi-sale fs-14"></i>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="flex-grow-1 ms-3">
                                              <h6 class="mb-1 lh-base">Today offers by <a href="apps-ecommerce-seller-details.html" class="link-secondary">Digitech Galaxy</a></h6>
                                              <p class="text-muted mb-2">Offer is valid on orders of Rs.500 Or above for selected products only.</p>
                                              <small class="mb-0 text-muted">12 Dec, 2021</small>
                                          </div>
                                      </div>
                                      <div class="acitivity-item py-3 d-flex">
                                          <div class="flex-shrink-0">
                                              <div class="avatar-xs acitivity-avatar">
                                                  <div class="avatar-title rounded-circle bg-soft-danger text-danger shadow">
                                                      <i class="ri-bookmark-fill"></i>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="flex-grow-1 ms-3">
                                              <h6 class="mb-1 lh-base">Favoried Product</h6>
                                              <p class="text-muted mb-2">Esther James have favorited product.</p>
                                              <small class="mb-0 text-muted">25 Nov, 2021</small>
                                          </div>
                                      </div>
                                      <div class="acitivity-item py-3 d-flex">
                                          <div class="flex-shrink-0">
                                              <div class="avatar-xs acitivity-avatar">
                                                  <div class="avatar-title rounded-circle bg-secondary shadow">
                                                      <i class="mdi mdi-sale fs-14"></i>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="flex-grow-1 ms-3">
                                              <h6 class="mb-1 lh-base">Flash sale starting <span class="text-primary">Tomorrow.</span></h6>
                                              <p class="text-muted mb-0">Flash sale by <a href="javascript:void(0);" class="link-secondary fw-medium">Zoetic Fashion</a></p>
                                              <small class="mb-0 text-muted">22 Oct, 2021</small>
                                          </div>
                                      </div>
                                      <div class="acitivity-item py-3 d-flex">
                                          <div class="flex-shrink-0">
                                              <div class="avatar-xs acitivity-avatar">
                                                  <div class="avatar-title rounded-circle bg-soft-info text-info shadow">
                                                      <i class="ri-line-chart-line"></i>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="flex-grow-1 ms-3">
                                              <h6 class="mb-1 lh-base">Monthly sales report</h6>
                                              <p class="text-muted mb-2"><span class="text-danger">2 days left</span> notification to submit the monthly sales report. <a href="javascript:void(0);" class="link-warning text-decoration-underline">Reports Builder</a></p>
                                              <small class="mb-0 text-muted">15 Oct</small>
                                          </div>
                                      </div>
                                      <div class="acitivity-item d-flex">
                                          <div class="flex-shrink-0">
                                              <img src="assets/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                          </div>
                                          <div class="flex-grow-1 ms-3">
                                              <h6 class="mb-1 lh-base">Frank Hook Commented</h6>
                                              <p class="text-muted mb-2 fst-italic">" A product that has reviews is more likable to be sold than a product. "</p>
                                              <small class="mb-0 text-muted">26 Aug, 2021</small>
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="p-3 mt-2">
                                  <h6 class="text-muted mb-3 text-uppercase fw-semibold">Top 10 Categories
                                  </h6>

                                  <ol class="ps-3 text-muted">
                                      <li class="py-1">
                                          <a href="#" class="text-muted">Mobile & Accessories <span class="float-end">(10,294)</span></a>
                                      </li>
                                      <li class="py-1">
                                          <a href="#" class="text-muted">Desktop <span class="float-end">(6,256)</span></a>
                                      </li>
                                      <li class="py-1">
                                          <a href="#" class="text-muted">Electronics <span class="float-end">(3,479)</span></a>
                                      </li>
                                      <li class="py-1">
                                          <a href="#" class="text-muted">Home & Furniture <span class="float-end">(2,275)</span></a>
                                      </li>
                                      <li class="py-1">
                                          <a href="#" class="text-muted">Grocery <span class="float-end">(1,950)</span></a>
                                      </li>
                                      <li class="py-1">
                                          <a href="#" class="text-muted">Fashion <span class="float-end">(1,582)</span></a>
                                      </li>
                                      <li class="py-1">
                                          <a href="#" class="text-muted">Appliances <span class="float-end">(1,037)</span></a>
                                      </li>
                                      <li class="py-1">
                                          <a href="#" class="text-muted">Beauty, Toys & More <span class="float-end">(924)</span></a>
                                      </li>
                                      <li class="py-1">
                                          <a href="#" class="text-muted">Food & Drinks <span class="float-end">(701)</span></a>
                                      </li>
                                      <li class="py-1">
                                          <a href="#" class="text-muted">Toys & Games <span class="float-end">(239)</span></a>
                                      </li>
                                  </ol>
                                  <div class="mt-3 text-center">
                                      <a href="javascript:void(0);" class="text-muted text-decoration-underline">View all Categories</a>
                                  </div>
                              </div>
                              <div class="p-3">
                                  <h6 class="text-muted mb-3 text-uppercase fw-semibold">Products Reviews</h6>
                                  <!-- Swiper -->
                                  <div class="swiper vertical-swiper" style="height: 250px;">
                                      <div class="swiper-wrapper">
                                          <div class="swiper-slide">
                                              <div class="card border border-dashed shadow-none">
                                                  <div class="card-body">
                                                      <div class="d-flex">
                                                          <div class="flex-shrink-0 avatar-sm">
                                                              <div class="avatar-title bg-light rounded shadow">
                                                                  <img src="assets/images/companies/img-1.png" alt="" height="30">
                                                              </div>
                                                          </div>
                                                          <div class="flex-grow-1 ms-3">
                                                              <div>
                                                                  <p class="text-muted mb-1 fst-italic text-truncate-two-lines"> " Great product and looks great, lots of features. "</p>
                                                                  <div class="fs-11 align-middle text-warning">
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                  </div>
                                                              </div>
                                                              <div class="text-end mb-0 text-muted">
                                                                  - by <cite title="Source Title">Force Medicines</cite>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="swiper-slide">
                                              <div class="card border border-dashed shadow-none">
                                                  <div class="card-body">
                                                      <div class="d-flex">
                                                          <div class="flex-shrink-0">
                                                              <img src="assets/images/users/avatar-3.jpg" alt="" class="avatar-sm rounded shadow">
                                                          </div>
                                                          <div class="flex-grow-1 ms-3">
                                                              <div>
                                                                  <p class="text-muted mb-1 fst-italic text-truncate-two-lines"> " Amazing template, very easy to understand and manipulate. "</p>
                                                                  <div class="fs-11 align-middle text-warning">
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-half-fill"></i>
                                                                  </div>
                                                              </div>
                                                              <div class="text-end mb-0 text-muted">
                                                                  - by <cite title="Source Title">Henry Baird</cite>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="swiper-slide">
                                              <div class="card border border-dashed shadow-none">
                                                  <div class="card-body">
                                                      <div class="d-flex">
                                                          <div class="flex-shrink-0 avatar-sm">
                                                              <div class="avatar-title bg-light rounded shadow">
                                                                  <img src="assets/images/companies/img-8.png" alt="" height="30">
                                                              </div>
                                                          </div>
                                                          <div class="flex-grow-1 ms-3">
                                                              <div>
                                                                  <p class="text-muted mb-1 fst-italic text-truncate-two-lines"> "Very beautiful product and Very helpful customer service."</p>
                                                                  <div class="fs-11 align-middle text-warning">
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-line"></i>
                                                                      <i class="ri-star-line"></i>
                                                                  </div>
                                                              </div>
                                                              <div class="text-end mb-0 text-muted">
                                                                  - by <cite title="Source Title">Zoetic Fashion</cite>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="swiper-slide">
                                              <div class="card border border-dashed shadow-none">
                                                  <div class="card-body">
                                                      <div class="d-flex">
                                                          <div class="flex-shrink-0">
                                                              <img src="assets/images/users/avatar-2.jpg" alt="" class="avatar-sm rounded shadow">
                                                          </div>
                                                          <div class="flex-grow-1 ms-3">
                                                              <div>
                                                                  <p class="text-muted mb-1 fst-italic text-truncate-two-lines">" The product is very beautiful. I like it. "</p>
                                                                  <div class="fs-11 align-middle text-warning">
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-fill"></i>
                                                                      <i class="ri-star-half-fill"></i>
                                                                      <i class="ri-star-line"></i>
                                                                  </div>
                                                              </div>
                                                              <div class="text-end mb-0 text-muted">
                                                                  - by <cite title="Source Title">Nancy Martino</cite>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="p-3">
                                  <h6 class="text-muted mb-3 text-uppercase fw-semibold">Customer Reviews</h6>
                                  <div class="bg-light px-3 py-2 rounded-2 mb-2">
                                      <div class="d-flex align-items-center">
                                          <div class="flex-grow-1">
                                              <div class="fs-16 align-middle text-warning">
                                                  <i class="ri-star-fill"></i>
                                                  <i class="ri-star-fill"></i>
                                                  <i class="ri-star-fill"></i>
                                                  <i class="ri-star-fill"></i>
                                                  <i class="ri-star-half-fill"></i>
                                              </div>
                                          </div>
                                          <div class="flex-shrink-0">
                                              <h6 class="mb-0">4.5 out of 5</h6>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="text-center">
                                      <div class="text-muted">Total <span class="fw-medium">5.50k</span> reviews</div>
                                  </div>

                                  <div class="mt-3">
                                      <div class="row align-items-center g-2">
                                          <div class="col-auto">
                                              <div class="p-1">
                                                  <h6 class="mb-0">5 star</h6>
                                              </div>
                                          </div>
                                          <div class="col">
                                              <div class="p-1">
                                                  <div class="progress bg-soft-success animated-progress progress-sm">
                                                      <div class="progress-bar bg-success" role="progressbar" style="width: 50.16%" aria-valuenow="50.16" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-auto">
                                              <div class="p-1">
                                                  <h6 class="mb-0 text-muted">2758</h6>
                                              </div>
                                          </div>
                                      </div>
                                      <!-- end row -->

                                      <div class="row align-items-center g-2">
                                          <div class="col-auto">
                                              <div class="p-1">
                                                  <h6 class="mb-0">4 star</h6>
                                              </div>
                                          </div>
                                          <div class="col">
                                              <div class="p-1">
                                                  <div class="progress bg-soft-success animated-progress progress-sm">
                                                      <div class="progress-bar bg-success" role="progressbar" style="width: 29.32%" aria-valuenow="29.32" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-auto">
                                              <div class="p-1">
                                                  <h6 class="mb-0 text-muted">1063</h6>
                                              </div>
                                          </div>
                                      </div>
                                      <!-- end row -->

                                      <div class="row align-items-center g-2">
                                          <div class="col-auto">
                                              <div class="p-1">
                                                  <h6 class="mb-0">3 star</h6>
                                              </div>
                                          </div>
                                          <div class="col">
                                              <div class="p-1">
                                                  <div class="progress bg-soft-warning animated-progress progress-sm">
                                                      <div class="progress-bar bg-warning" role="progressbar" style="width: 18.12%" aria-valuenow="18.12" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-auto">
                                              <div class="p-1">
                                                  <h6 class="mb-0 text-muted">997</h6>
                                              </div>
                                          </div>
                                      </div>
                                      <!-- end row -->

                                      <div class="row align-items-center g-2">
                                          <div class="col-auto">
                                              <div class="p-1">
                                                  <h6 class="mb-0">2 star</h6>
                                              </div>
                                          </div>
                                          <div class="col">
                                              <div class="p-1">
                                                  <div class="progress bg-soft-success animated-progress progress-sm">
                                                      <div class="progress-bar bg-success" role="progressbar" style="width: 4.98%" aria-valuenow="4.98" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                              </div>
                                          </div>

                                          <div class="col-auto">
                                              <div class="p-1">
                                                  <h6 class="mb-0 text-muted">227</h6>
                                              </div>
                                          </div>
                                      </div>
                                      <!-- end row -->

                                      <div class="row align-items-center g-2">
                                          <div class="col-auto">
                                              <div class="p-1">
                                                  <h6 class="mb-0">1 star</h6>
                                              </div>
                                          </div>
                                          <div class="col">
                                              <div class="p-1">
                                                  <div class="progress bg-soft-danger animated-progress progress-sm">
                                                      <div class="progress-bar bg-danger" role="progressbar" style="width: 7.42%" aria-valuenow="7.42" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-auto">
                                              <div class="p-1">
                                                  <h6 class="mb-0 text-muted">408</h6>
                                              </div>
                                          </div>
                                      </div><!-- end row -->
                                  </div>
                              </div>

                              <div class="card sidebar-alert bg-light border-0 text-center mx-4 mb-0 mt-3">
                                  <div class="card-body">
                                      <img src="assets/images/giftbox.png" alt="">
                                      <div class="mt-4">
                                          <h5>Invite New Seller</h5>
                                          <p class="text-muted lh-base">Refer a new seller to us and earn $100 per refer.</p>
                                          <button type="button" class="btn btn-primary btn-label rounded-pill"><i class="ri-mail-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Invite Now</button>
                                      </div>
                                  </div>
                              </div>

                          </div>
                      </div> <!-- end card-->
                  </div> <!-- end .rightbar-->

              </div> <!-- end col -->
          </div>

      </div>
      <!-- container-fluid -->
 @endsection