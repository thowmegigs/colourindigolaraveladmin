@extends('layouts.frontend.app')
@section('content')
    @php
        $user = auth()->user();
    @endphp
  
      <section class="py-5 account-page bg-light">
         
            <div class="modal fade" id="edit-profile-modal" tabindex="-1" role="dialog" aria-labelledby="edit-profile" aria-hidden="true">
         <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="edit-profile">Edit profile</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <form>
                     <div class="form-row">
                        <div class="form-group col-md-12">
                           <label>Phone number
                           </label>
                           <input type="text" value="+91 85680-79956" class="form-control" placeholder="Enter Phone number">
                        </div>
                        <div class="form-group col-md-12">
                           <label>Email id
                           </label>
                           <input type="text" value="iamosahan@gmail.com" class="form-control" placeholder="Enter Email id
                              ">
                        </div>
                        <div class="form-group col-md-12 mb-0">
                           <label>Password
                           </label>
                           <input type="password" value="**********" class="form-control" placeholder="Enter password
                              ">
                        </div>
                     </div>
                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn d-flex w-50 text-center justify-content-center btn-outline-primary" data-dismiss="modal">CANCEL
                  </button><button type="button" class="btn d-flex w-50 text-center justify-content-center btn-primary">UPDATE</button>
               </div>
            </div>
         </div>
      </div>
    
         <div class="container">
            <div class="row">
               <div class="col-sm-3">
                  <div class="osahan-account-page-left overflow-hidden shadow-sm rounded bg-white h-100">
                     <div class="p-4">
                        <div class="osahan-user text-center">
                           <div class="osahan-user-media">
                              <div class="osahan-user-media-body">
                                 <h6 class="mb-2 font-weight-bold">{{ucwords($user->name)}}</h6>
                                 <p class="mb-1">{{$user->phone}}</p>
                                 
                              </div>
                           </div>
                        </div>
                     </div>
                     <ul class="nav nav-tabs flex-column border-0" id="myTab" role="tablist">
                        <li class="nav-item">
                           <a class="nav-link" id="my-profile-tab" data-toggle="tab" href="#my-profile" role="tab" aria-controls="my-profile" aria-selected="true"><i class="icofont-ui-user"></i> My Profile</a>
                        </li>
                      
                        <!--<li class="nav-item">-->
                        <!--   <a class="nav-link" id="wish-list-tab" data-toggle="tab" href="#wish-list" role="tab" aria-controls="wish-list" aria-selected="false"><i class="icofont-heart"></i> Wish List</a>-->
                        <!--</li>-->
                        <li class="nav-item">
                           <a class="nav-link active" id="order-list-tab" data-toggle="tab" href="#order-list" role="tab" aria-controls="order-list" aria-selected="false"><i class="icofont-list"></i> Order List</a>
                        </li>
                       
                        <li class="nav-item">
                           <a class="nav-link" href="/logout"><i class="icofont-logout"></i> Logout</a>
                        </li>
                     </ul>
                  </div>
               </div>
               <div class="col-sm-9">
                  <div class="osahan-account-page-right rounded shadow-sm bg-white p-4 h-100">
                     <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade" id="my-profile" role="tabpanel" aria-labelledby="my-profile-tab">
                           <h4 class="text-dark mt-0 mb-4">My Profile</h4>
                           
                           <form method="post" action="{{route('user.update_profile')}}">
                               @csrf
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label class="control-label"> Name <span class="required">*</span></label>
                                       <input class="form-control border-form-control" value="{{$me->name}}" name="name" placeholder="Enter name" type="text">
                                    </div>
                                 </div>
                               
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label class="control-label">Phone <span class="required">*</span></label>
                                       <input class="form-control border-form-control" value="{{$me->phone}}" name="phone" placeholder="Enter phone number " type="number">
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label class="control-label">Email Address <span class="required">*</span></label>
                                       <input class="form-control border-form-control "  placeholder="Enter email address" name="email" value="{{$me->email}}" type="email">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                               
                                 
                              </div>
                              <div class="row">
                                
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label class="control-label">State <span class="required">*</span></label>
                                       <select class="select2 form-control border-form-control" name="billing_state"  id="states">
                                          <option value>Select State</option>
                                          @foreach($states as  $st)
                                          <option value="{{$st->id}}" @if($st->id==$address->bill_state->id) selected @endif>{{$st->name}}</option>
                                          @endforeach
                                         
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label class="control-label">City <span class="required">*</span></label>
                                       <select class="select2 form-control border-form-control" name="billing_city" id="cities">
                                           <option value="{{$address->bill_city->id}}" selected >{{$address->bill_city->name}}</option>
                                         
                                       </select>
                                    </div>
                                 </div>
                                  <div class="col-sm-4">
                                    <div class="form-group">
                                       <label class="control-label">Pin Code <span class="required">*</span></label>
                                       <input class="form-control border-form-control" name="billing_pincode"  placeholder="123456" value="{{$address->billing_pincode}}" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-12">
                                    <div class="form-group">
                                       <label class="control-label">Address1 <span class="required">*</span></label>
                                       <textarea class="form-control border-form-control" name="billing_address1">{{$address->billing_address1}}</textarea>
                                    </div>
                                 </div>
                                  <div class="col-sm-12">
                                    <div class="form-group">
                                       <label class="control-label">Address2</label>
                                       <textarea class="form-control border-form-control" name="billing_address2">{{$address->billing_address2}}</textarea>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-12 text-right">
                                    
                                    <button  class="btn btn-primary"> Save Changes </button>
                                 </div>
                              </div>
                           </form>
                        </div>
                        <div class="tab-pane fade" id="my-address" role="tabpanel" aria-labelledby="my-address-tab">
                           <h4 class="text-dark mt-0 mb-4">My Address</h4>
                           <div class="row">
                              <div class="col-md-6">
                                 <div class="bg-white card addresses-item mb-3 shadow-sm">
                                    <div class="gold-members p-4">
                                       <div class="media">
                                          <div class="mr-4"><i class="icofont-ui-home icofont-3x"></i></div>
                                          <div class="media-body">
                                             <span class="badge badge-danger">Default - Home</span>
                                             <h6 class="mb-3 mt-1 text-dark">Gurdeep Singh</h6>
                                             <p>Delhi Bypass Rd GK mall Near, Jawaddi Taksal, Ludhiana, Punjab 141002, India</p>
                                             <p class="text-secondary">Phone: <span class="text-dark">8872306061</span></p>
                                             <hr>
                                             <p class="mb-0 text-black"><a class="text-success mr-3" data-toggle="modal" data-target="#add-address-modal" href="#"><i class="icofont-ui-edit"></i> EDIT</a> <a class="text-danger" data-toggle="modal" data-target="#delete-address-modal" href="#"><i class="icofont-ui-delete"></i> DELETE</a></p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-6">
                                 <div class="bg-white card addresses-item mb-3 shadow-sm">
                                    <div class="gold-members p-4">
                                       <div class="media">
                                          <div class="mr-4"><i class="icofont-briefcase icofont-3x"></i></div>
                                          <div class="media-body">
                                             <span class="badge badge-secondary">Office</span>
                                             <h6 class="mb-3 mt-1 text-dark">Askbootstrap</h6>
                                             <p>MT, Model Town Rd, Pritm Nagar, Model Town, Ludhiana, Punjab 141002, India</p>
                                             <p class="text-secondary">Phone: <span class="text-dark">8872306061</span></p>
                                             <hr>
                                             <p class="mb-0 text-black"><a class="text-success mr-3" data-toggle="modal" data-target="#add-address-modal" href="#"><i class="icofont-ui-edit"></i> EDIT</a> <a class="text-danger" data-toggle="modal" data-target="#delete-address-modal" href="#"><i class="icofont-ui-delete"></i> DELETE</a></p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-6">
                                 <div class="bg-white card addresses-item mb-3 shadow-sm">
                                    <div class="gold-members p-4">
                                       <div class="media">
                                          <div class="mr-4"><i class="icofont-location-pin icofont-3x"></i></div>
                                          <div class="media-body">
                                             <span class="badge badge-secondary">Other</span>
                                             <h6 class="mb-3 mt-1 text-dark">Askbootstrap</h6>
                                             <p>HHG, Model Town Rd, Pritm Nagar, Model Town, Ludhiana, Punjab 141002, India</p>
                                             <p class="text-secondary">Phone: <span class="text-dark">8872306061</span></p>
                                             <hr>
                                             <p class="mb-0 text-black"><a class="text-success mr-3" data-toggle="modal" data-target="#add-address-modal" href="#"><i class="icofont-ui-edit"></i> EDIT</a> <a class="text-danger" data-toggle="modal" data-target="#delete-address-modal" href="#"><i class="icofont-ui-delete"></i> DELETE</a></p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-6 pb-4">
                                 <a data-toggle="modal" data-target="#add-address-modal" href="#">
                                    <div class="bg-light border rounded  mb-3  shadow-sm text-center h-100 d-flex align-items-center">
                                       <h6 class="text-center m-0 w-100"><i class="icofont-plus-circle icofont-3x mb-5"></i><br><br>Add New Address</h6>
                                    </div>
                                 </a>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="wish-list" role="tabpanel" aria-labelledby="wish-list-tab">
                           <h4 class="text-dark mt-0 mb-4">Wish List</h4>
                           <div class="row">
                              <div class="col-6 col-md-4">
                                 <div class="card list-item bg-white rounded overflow-hidden position-relative shadow-sm">
                                    <span class="like-icon"><a href="#"> <i class="icofont icofont-close-line"></i></a></span>
                                    <a href="#">
                                    <span class="badge badge-danger">NEW</span>
                                    <img src="img/item/1.jpg" class="card-img-top" alt="..."></a>
                                    <div class="card-body">
                                       <h6 class="card-title mb-1">Floret Printed Ivory Skater Dress</h6>
                                       <div class="stars-rating"><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star"></i> <span>613</span></div>
                                       <p class="mb-0 text-dark">$135.00 <span class="text-black-50"><del>$500.00 </del></span></p>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-6 col-md-4">
                                 <div class="card list-item bg-white rounded overflow-hidden position-relative shadow-sm">
                                    <span class="like-icon"><a href="#"> <i class="icofont icofont-close-line"></i></a></span>
                                    <a href="#">
                                    <span class="badge badge-success">50% OFF</span>
                                    <img src="img/item/2.jpg" class="card-img-top" alt="..."></a>
                                    <div class="card-body">
                                       <h6 class="card-title mb-1">Floret Printed Ivory Skater Dress</h6>
                                       <div class="stars-rating"><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star"></i> <span>613</span></div>
                                       <p class="mb-0 text-dark">$ 135.00 <span class="bg-danger  rounded-sm pl-1 ml-1 pr-1 text-white small"> 50% OFF</span></p>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-6 col-md-4">
                                 <div class="card list-item bg-white rounded overflow-hidden position-relative shadow-sm">
                                    <span class="like-icon"><a href="#"> <i class="icofont icofont-close-line"></i></a></span>
                                    <a href="#">
                                    <span class="badge badge-danger">NEW</span>
                                    <img src="img/item/3.jpg" class="card-img-top" alt="..."></a>
                                    <div class="card-body">
                                       <h6 class="card-title mb-1">Floret Printed Ivory Skater Dress</h6>
                                       <div class="stars-rating"><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star"></i> <span>613</span></div>
                                       <p class="mb-0 text-dark">$ 135.00 <span class="bg-info rounded-sm pl-1 ml-1 pr-1 text-white small"> 50% OFF</span></p>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-6 col-md-4">
                                 <div class="card list-item bg-white rounded overflow-hidden position-relative shadow-sm">
                                    <span class="like-icon"><a href="#"> <i class="icofont icofont-close-line"></i></a></span>
                                    <a href="#">
                                    <span class="badge badge-success">50% OFF</span>
                                    <img src="img/item/4.jpg" class="card-img-top" alt="..."></a>
                                    <div class="card-body">
                                       <h6 class="card-title mb-1">Floret Printed Ivory Skater Dress</h6>
                                       <div class="stars-rating"><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star"></i> <span>613</span></div>
                                       <p class="mb-0 text-dark">$135.00 <span class="text-black-50"><del>$500.00 </del></span></p>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-6 col-md-4">
                                 <div class="card list-item bg-white rounded overflow-hidden position-relative shadow-sm">
                                    <span class="like-icon"><a href="#"> <i class="icofont icofont-close-line"></i></a></span>
                                    <a href="#">
                                    <span class="badge badge-danger">NEW</span>
                                    <img src="img/item/5.jpg" class="card-img-top" alt="..."></a>
                                    <div class="card-body">
                                       <h6 class="card-title mb-1">Floret Printed Ivory Skater Dress</h6>
                                       <div class="stars-rating"><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star"></i> <span>613</span></div>
                                       <p class="mb-0 text-dark">$135.00 <span class="text-black-50"><del>$500.00 </del></span></p>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-6 col-md-4">
                                 <div class="card list-item bg-white rounded overflow-hidden position-relative shadow-sm">
                                    <span class="like-icon"><a href="#"> <i class="icofont icofont-close-line"></i></a></span>
                                    <a href="#">
                                    <span class="badge badge-success">50% OFF</span>
                                    <img src="img/item/6.jpg" class="card-img-top" alt="..."></a>
                                    <div class="card-body">
                                       <h6 class="card-title mb-1">Floret Printed Ivory Skater Dress</h6>
                                       <div class="stars-rating"><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star active"></i><i class="icofont icofont-star"></i> <span>613</span></div>
                                       <p class="mb-0 text-dark">$135.00 <span class="text-black-50"><del>$500.00 </del></span></p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <nav aria-label="Page navigation example">
                              <ul class="pagination justify-content-center">
                                 <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                 </li>
                                 <li class="page-item"><a class="page-link" href="#">1</a></li>
                                 <li class="page-item"><a class="page-link" href="#">2</a></li>
                                 <li class="page-item"><a class="page-link" href="#">3</a></li>
                                 <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                 </li>
                              </ul>
                           </nav>
                        </div>
                        <div class="tab-pane fade show active" id="order-list" role="tabpanel" aria-labelledby="order-list-tab">
                           <h4 class="text-dark mt-0 mb-4">Order List</h4>
                           <x-alert />
                             @foreach ($orders as $t)
                                    <x-frontend.modals.order_items_modal :order="$t" />
                                @endforeach
                           <div class="order-list-tabel-main table-responsive">
                              <table class="datatabel table table-striped table-bordered order-list-tabel" width="100%" cellspacing="0">
                                 <thead>
                                    <tr>
                                       <th>Order #</th>
                                       <th>Date Purchased</th>
                                       <th>Status</th>
                                       <th>Total</th>
                                       <th>Payment Method</th>
                                       <th>Action</th>
                                    </tr>
                                 </thead>
                                  @if ($orders->count() > 0)
                                 <tbody>
                                      @foreach ($orders as $t)
                                    <tr>
                                       <td>{{ ($loop->index)+1 }}</td>
                                       <td>{{ formateDate($t->created_at) }}</td>
                                       <td> @if ($t->delivery_status != 'Delivered')
                                                            <span class="badge badge-warning"
                                                                >{{ $t->delivery_status == 'Order Placed' ? 'Pending' : $t->delivery_status }}</span>
                                                        @else
                                                            <span class="badge badge-success"
                                                                >{{ $t->delivery_status }}</span>
                                                        @endif</td>
                                       <td>{{ getCurrency() }}{{ $t->total_amount_after_discount }}</td>
                                       <td >{{ $t->payment_method }}</td>
                                       <td><a data-toggle="modal" data-target="#order_items_{{ $t->id }}" href="javascript:void(0)" class="btn btn-info btn-sm"><i class="icofont-eye-alt"></i></a></td>
                                    </tr>
                                    
                                  @endforeach
                                   
                                 </tbody>
                                 @endif
                              </table>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="order-status" role="tabpanel" aria-labelledby="order-status-tab">
                           <h4 class="text-dark mt-0 mb-4">Your Order Status</h4>
                           <div class="status-main">
                              <div class="row mb-4">
                                 <div class="col-lg-12">
                                    <div class="statustop">
                                       <p class="mb-2"><strong>Status:</strong> OnHold</p>
                                       <p class="mb-2"><strong>Order Date:</strong> Saturday, April 09,2019</p>
                                       <p class="mb-2"><strong>Order Number:</strong> #6469 </p>
                                    </div>
                                 </div>
                              </div>
                              <div class="row mb-3">
                                 <div class="col-lg-6 col-md-6">
                                    <div class="card">
                                       <div class="card-header">
                                          Billing Address
                                       </div>
                                       <div class="card-body">
                                          <p class="card-text mb-2 text-dark"><strong>TITLE</strong></p>
                                          <p class="card-text mb-2"><strong>Gurdeep Singh Osahan</strong></p>
                                          <p class="card-text mb-0"> 4894 Burke Street<br>
                                             North Billerica, MA 01862
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-lg-6 col-md-6">
                                    <div class="card">
                                       <div class="card-header">
                                          Shipping Address
                                       </div>
                                       <div class="card-body">
                                          <p class="card-text mb-2 text-dark"><strong>TITLE</strong></p>
                                          <p class="card-text mb-2"><strong>Gurdeep Singh Osahan</strong></p>
                                          <p class="card-text mb-0"> 4894 Burke Street<br>
                                             North Billerica, MA 01862
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="row mb-3">
                                 <div class="col-lg-6 col-md-6">
                                    <div class="card">
                                       <div class="card-header">
                                          Payment Method
                                       </div>
                                       <div class="card-body">
                                          <p class="card-text text-dark mb-2">Payment via Master Card <strong><span class="badge badge-success">Paid</span></strong></p>
                                          <p class="card-text mb-2"><strong>Name Of card </strong>: Gurdeep Osahan</p>
                                          <p class="card-text mb-0"><strong>Card Number </strong>: 00335 251 124</p>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-lg-6 col-md-6">
                                    <div class="card">
                                       <div class="card-header">
                                          Shipping Method
                                       </div>
                                       <div class="card-body">
                                          <p class="card-text text-dark mb-2"> via Post Air Mail #4502</p>
                                          <p class="card-text mb-2"><strong>Gurdeep Singh Osahan</strong></p>
                                          <p class="card-text mb-0"> 4894 Burke Street North Billerica</p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-12 col-md-12">
                                    <div class="card">
                                       <div class="card-header">
                                          Order Items
                                       </div>
                                       <div class="card-block padding-none">
                                          <div class="cart-table">
                                             <div class="table-responsive">
                                                <table class="table cart_summary">
                                                   <thead>
                                                      <tr>
                                                         <th>Product</th>
                                                         <th>Description</th>
                                                         <th>Delivery Options</th>
                                                         <th>Quantity</th>
                                                         <th>Subtotal</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td class="cart_product"><a href="#"><img class="img-fluid" src="img/item/1.jpg" alt></a></td>
                                                         <td class="cart_description">
                                                            <h6 class="product-name"><a href="#">Floret Printed Ivory Skater Dress </a></h6>
                                                            <p class="f-12 text-secondary mb-1 pt-1 pb-1">5/4 Review</p>
                                                         </td>
                                                         <td>
                                                            <p class="text-secondary mb-0"><i class="icofont-check-circled"></i> 17 Aug to 19 Aug <span class="text-dark">+$. 49</span></p>
                                                         </td>
                                                         <td class="qty">
                                                            <select class="custom-select custom-select-sm" disabled>
                                                               <option selected>1</option>
                                                               <option value="1">2</option>
                                                               <option value="2">3</option>
                                                               <option value="3">4</option>
                                                            </select>
                                                         </td>
                                                         <td class="price">
                                                            <p class="f-14 mb-0 text-dark float-right">$250.00 <del class="small text-secondary">$ 500.00 </del></p>
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <td class="cart_product"><a href="#"><img class="img-fluid" src="img/item/2.jpg" alt></a></td>
                                                         <td class="cart_description">
                                                            <h6 class="product-name"><a href="#">Floret Printed Ivory Skater Dress </a></h6>
                                                            <p class="f-12 text-secondary mb-1 pt-1 pb-1">5/4 Review</p>
                                                         </td>
                                                         <td>
                                                            <p class="text-secondary mb-0"><i class="icofont-check-circled"></i> 17 Aug to 19 Aug <span class="text-dark">+$. 49</span></p>
                                                         </td>
                                                         <td class="qty">
                                                            <select class="custom-select custom-select-sm" disabled>
                                                               <option selected>1</option>
                                                               <option value="1">2</option>
                                                               <option value="2">3</option>
                                                               <option value="3">4</option>
                                                            </select>
                                                         </td>
                                                         <td class="price">
                                                            <p class="f-14 mb-0 text-dark float-right">$250.00 <del class="small text-secondary">$ 500.00 </del></p>
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <td class="cart_product"><a href="#"><img class="img-fluid" src="img/item/3.jpg" alt></a></td>
                                                         <td class="cart_description">
                                                            <h6 class="product-name"><a href="#">Floret Printed Ivory Skater Dress </a></h6>
                                                            <p class="f-12 text-secondary mb-1 pt-1 pb-1">5/4 Review</p>
                                                         </td>
                                                         <td>
                                                            <p class="text-secondary mb-0"><i class="icofont-check-circled"></i> 17 Aug to 19 Aug <span class="text-dark">+$. 49</span></p>
                                                         </td>
                                                         <td class="qty">
                                                            <select class="custom-select custom-select-sm" disabled>
                                                               <option selected>1</option>
                                                               <option value="1">2</option>
                                                               <option value="2">3</option>
                                                               <option value="3">4</option>
                                                            </select>
                                                         </td>
                                                         <td class="price">
                                                            <p class="f-14 mb-0 text-dark float-right">$250.00 <del class="small text-secondary">$ 500.00 </del></p>
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <td class="cart_product"><a href="#"><img class="img-fluid" src="img/item/4.jpg" alt></a></td>
                                                         <td class="cart_description">
                                                            <h6 class="product-name"><a href="#">Floret Printed Ivory Skater Dress </a></h6>
                                                            <p class="f-12 text-secondary mb-1 pt-1 pb-1">5/4 Review</p>
                                                         </td>
                                                         <td>
                                                            <p class="text-secondary mb-0"><i class="icofont-check-circled"></i> 17 Aug to 19 Aug <span class="text-dark">+$. 49</span></p>
                                                         </td>
                                                         <td class="qty">
                                                            <select class="custom-select custom-select-sm" disabled>
                                                               <option selected>1</option>
                                                               <option value="1">2</option>
                                                               <option value="2">3</option>
                                                               <option value="3">4</option>
                                                            </select>
                                                         </td>
                                                         <td class="price">
                                                            <p class="f-14 mb-0 text-dark float-right">$250.00 <del class="small text-secondary">$ 500.00 </del></p>
                                                         </td>
                                                      </tr>
                                                   </tbody>
                                                   <tfoot>
                                                      <tr>
                                                         <td class="text-right" colspan="3">Total products (tax incl.)</td>
                                                         <td colspan="2">$437.88 </td>
                                                      </tr>
                                                      <tr>
                                                         <td class="text-right" colspan="3"><strong>Total</strong></td>
                                                         <td class="text-danger" colspan="2"><strong>$337.88 </strong></td>
                                                      </tr>
                                                   </tfoot>
                                                </table>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
   
@endsection

