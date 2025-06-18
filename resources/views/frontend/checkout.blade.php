@extends('layouts.frontend.app')
@section('content')
<style>
    [class="required"]{
        color:red;
    }
</style>
    <section class="checkout-body py-5 bg-light">
        <div class="container" x-data="{
             init(){
             console.log('se',localStorage.getItem('cart_session_id'))
             },
           
                form_data: {
                    billing_fname: '{!! $user_address?->billing_fname !!}',
                    billing_lname: '{!! $user_address?->billing_lname !!}',
                    billing_address1: '{!! $user_address?->billing_address1 !!}',
                    billing_address2: '{!! $user_address?->billing_address2 !!}',
                    billing_city: '{!! $user_address?->billing_city !!}',
                    billing_state: '{!! $user_address?->billing_state !!}',
                    billing_email: '{!! $user_address?->billing_email !!}',
                    billing_pincode: '{!! $user_address?->billing_pincode !!}',
                    billing_phone: '{!! \Auth::check() ? auth()->user()->phone : $user_address?->billing_phone !!}',
                    slot_time: '',
                    slot_date: '',
                    delivery_instructions: '',
                    payment_method: 'Online',
                    cart_session_id: localStorage.getItem('cart_session_id'),
                    is_wallet_used: 'No',
                    wallet_amount_used: {{ $wallet }}
        
                },
                error: {
                    billing_fname: '',
        
                    billing_address1: '',
                    billing_city: '', billing_state: '',
                    billing_email: '',
                    billing_phone: '',
                    billing_pincode: '',
                    slot_date: '',
                },
                is_form_valid: true,
        
                loading: false,
        
                validateForm() {
               
                    if (this.form_data.billing_fname.length < 3) {
                        this.error.billing_fname = 'First Name is required'
                        this.is_form_valid = false;
                    } else {
                        this.error.billing_fname = ''
                        this.is_form_valid = true;
                    }
        
        
                    if (this.form_data.billing_address1.length < 3) {
                        this.error.billing_address1 = 'Address is required'
                        this.is_form_valid = false;
                    } else {
                        this.error.billing_address1 = ''
                        this.is_form_valid = true;
                    }
                    if (this.form_data.billing_state.length < 3) {
                        this.error.billing_city = 'State is required'
                        this.is_form_valid = false;
                    }
                    if (this.form_data.billing_city.length < 3) {
                        this.error.billing_city = 'City is required'
                        this.is_form_valid = false;
                    } else {
                        this.error.billing_city = ''
                        this.is_form_valid = true;
                    }
        
                    if (this.form_data.billing_pincode.length != 6) {
                        this.error.billing_pincode = 'Valid Pincode  is required'
                        this.is_form_valid = false;
                    } else {
                        this.error.billing_pincode = ''
                        this.is_form_valid = true;
                    }
                    if (this.form_data.billing_email.length < 1) {
                        this.error.billing_email = 'Valid mail  is required'
                        this.is_form_valid = false;
                    } else {
                        this.error.billing_email = ''
                        this.is_form_valid = true;
                    }
        
        
        
        
                },
                placeOrder(method) {
               let isloged=this.$store.cart.is_logged_in
          
                    if (isloged=='No') {
                        $('#login').modal('show');
                    } else {
                        this.validateForm();
                        console.log(this.error)
        
                        this.form_data.payment_method = method
                        if (this.is_form_valid) {
        
                            this.loading = true;
                            fetch('/create_order', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        
                                    },
                                    body: JSON.stringify(this.form_data)
                                })
                                .then((response) => response.json()).then(res => {
                                    console.log('resposne pay');
                                    console.log(res)
                                    this.loading = false;
                                    if (res['success']) {
                                           localStorage.removeItem('cart_session_id');
                                        if (this.form_data.payment_method == 'Online')
                                            this.$store.cart.pay(res['amount'], res['razorpay_orderid'])
                                        else
                                            location.href = '/order_success'
        
        
                                    } else
                                        vNotify.error({ text: res['message'], title: 'Error' });
                                })
                        }
                        else
                         {
                         vNotify.error({ text: 'Delivery address form has errors', title: 'Error' });
                         }
                    }
                },
        
        
        }">
            <div class="row">
                <div class="col-md-8 order-2 order-md-1">
                    <div class="checkout-body-left">
                        <div class="accordion checkout-step" id="accordionExample">
                                <div class="bg-white rounded shadow-sm mb-3 overflow-hidden">
                                <div class="card-header bg-white" id="headingTwo">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                            data-target="#logino" aria-expanded="false" aria-controls="collapseTwo">
                                            <i class="icofont-simple-down float-right mt-1"></i>
                                            1. Login/Create Account
                                        </button>
                                    </h2>
                                </div>
                                <div id="logino" class="collapse @if(!\Auth::check()) show @endif" aria-labelledby="headingTwo"
                                    data-parent="#accordionExample">
                                    <div class="card-body text-center">
                                        @if(!\Auth::check())
                                        <a href="javascript:void(0)" data-target="#login" data-toggle="modal"
                                                                class="mx-auto btn btn-primary  btn-md">Login
                                                                <i class="icofont-long-arrow-right"></i></a>
                                                                @else
                                                                 <a href="javascript:void(0)"
                                                                class="mx-auto btn btn-success  btn-md">Authenticated</a>
                                                                @endif

                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded shadow-sm mb-3 overflow-hidden">
                                <div class="card-header bg-white" id="headingTwo">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                            data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            <i class="icofont-simple-down float-right mt-1"></i>
                                            2. Add Delivery Address
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTwo" class="collapse @if(\Auth::check()) show @endif" aria-labelledby="headingTwo"
                                    data-parent="#accordionExample">
                                    <div class="card-body">
                                        <form>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>First Name <span class="required">*</span></label>
                                                    <input class="form-control border-form-control" value=""
                                                        x-model="form_data.billing_fname" name="fname" required placeholder
                                                        type="text">
                                                    <span x-show="error.billing_fname.length>0" x-text="error.billing_fname"
                                                        class="form_error"></span>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Last Name </label>
                                                    <input class="form-control border-form-control" value=""
                                                        x-model="form_data.billing_lname" name="lname"  placeholder
                                                        type="text">
                                                   
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Email <span class="required">*</span></label>
                                                    <input class="form-control border-form-control" value=""
                                                        x-model="form_data.billing_email" name="email"  placeholder
                                                        type="email">
                                                    <span x-show="error.billing_email.length>0" x-text="error.billing_email"
                                                        class="form_error"></span>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Address1 <span class="required">*</span></label>
                                                    <input class="form-control border-form-control" value=""
                                                        x-model="form_data.billing_address1" name="billing_address1" required
                                                        placeholder type="text">
                                                    <span x-show="error.billing_address1.length>0"
                                                        x-text="error.billing_address1" class="form_error"></span>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Address2 </label>
                                                    <input class="form-control border-form-control" value=""
                                                        x-model="form_data.billing_address2" name="billing_address2"
                                                        placeholder type="text">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>State <span class="required">*</span></label>
                                                    <select class="select2 form-control border-form-control"
                                                        
                                                        x-model="form_data.billing_state" id="states">
                                                        <option value="">Select State</option>
                                                        @foreach ($states as $st)
                                                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                                                        @endforeach

                                                    </select>
                                                    <span x-show="error.billing_state.length>0" x-text="error.billing_state"
                                                        class="form_error"></span>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>City <span class="required">*</span></label>
                                                    <select class="select2 form-control border-form-control" required
                                                        x-model="form_data.billing_city" id="cities">
                                                        @if($user_city)
                                                        <option value="{{$user_city->id}}" selected>{{$user_city->name}}</option>
                                                        @else
                                                        <option value="">Select City</option>
                                                        @endif

                                                    </select>
                                                    <span x-show="error.billing_city.length>0" x-text="error.billing_city"
                                                        class="form_error"></span>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="control-label">Pincode Code <span
                                                            class="required">*</span></label>
                                                    <input class="form-control border-form-control" value
                                                        x-model="form_data.billing_pincode" placeholder="123456"
                                                        type="number">
                                                    <span x-show="error.billing_pincode.length>0"
                                                        x-text="error.billing_pincode" class="form_error"></span>
                                                </div>



                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded shadow-sm overflow-hidden">
                                <div class="card-header bg-white" id="headingfour">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                            data-target="#collapsefour" aria-expanded="true" aria-controls="collapsefour">
                                            <i class="icofont-simple-down float-right mt-1"></i> 3. Make Payment
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapsefour" class="collapse @if(!\Auth::check()) show @endif" aria-labelledby="headingOne"
                                    data-parent="#accordionExample">
                                    <div class="card-body osahan-payment">
                                        <div class="row">
                                            <div class="col-sm-4 pr-0">
                                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                                    aria-orientation="vertical">

                                                   
                                                    <a class="nav-link active" id="v-pills-cash-tab" data-toggle="pill"
                                                        href="#v-pills-cash" role="tab" aria-controls="v-pills-cash"
                                                        aria-selected="false"><i class="icofont-money"></i> Cash on
                                                        Delivery</a>
                                                     <a class="nav-link " id="v-pills-settings-tab"
                                                        data-toggle="pill" href="#v-pills-settings" role="tab"
                                                        aria-controls="v-pills-settings" aria-selected="true"><i
                                                            class="icofont-bank-alt"></i> Pay Online</a>
                                                </div>
                                            </div>
                                            <div class="col-sm-8 pl-0">
                                                <div class="tab-content h-100" id="v-pills-tabContent">
                                                       <div class="tab-pane fade show active" id="v-pills-cash" role="tabpanel"
                                                        aria-labelledby="v-pills-cash-tab">
                                                        <h6 class="mb-3 mt-0 mb-3">Cash</h6>
                                                        <p>Please keep exact change handy to help us serve you better</p>
                                                        <hr>
                                                        <form>
                                                            <a href="javascript:void(0)" @click="placeOrder('cod')"
                                                                class="btn btn-primary btn-block btn-lg">PLACE ORDER <span
                                                                    x-text="$store.cart.amount_in_cur($store.cart.sub_total)"></span>
                                                                <i class="icofont-long-arrow-right"></i></a>
                                                    </div>
                                                    </form>
                                                </div>
                                                    <div class="tab-pane fade " id="v-pills-settings"
                                                        role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                                        <h6 class="mb-3 mt-0 mb-3">Online</h6>
                                                        <form>

                                                            <div class="form-row">

                                                                <div class="form-group col-md-12 mb-0">
                                                                    <a href="javascript:void(0)"
                                                                        @click="placeOrder('Online')"
                                                                        class="btn btn-primary btn-block btn-lg">PAY <span
                                                                            x-text="$store.cart.amount_in_cur($store.cart.sub_total)"></span>
                                                                        <i class="icofont-long-arrow-right"></i></a>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                   
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 order-1 order-md-2">
                    <div class="osahan-cart-item">
                        <h5 class="mb-3 mt-0 text-dark">Summary <span class="small text-success">(<span
                                    x-text="$store.cart.items.length"></span> Item)</span></h5>
                        <div class="bg-white rounded shadow-sm mb-3">
                            <template x-if="$store.cart.items.length>0"> <template x-for="item in $store.cart.items"
                                    :key="item.id">
                                    <div class="cart-list-product">

                                        <img class="img-fluid" :src="item.image" />
                                        <!--<span class="badge badge-success">50% OFF</span>-->
                                        <h5><a href="#" :href="$store.cart.product_url(item.name)"
                                                x-text="item.name"></a></h5>
                                          <template x-if="item.atributes_json">
                                            <div style="font-size:11px;">
                                                 <template x-for="[key, value] in Object.entries(item.atributes_json)" :key="key">
                                            <p class="m-0">
                                              <strong  x-text="key"></strong>: <span x-text="value" class="text-success"></span>
                                            </p>
                                          </template>
                                            </div>
                                            </template>
                                        <p class="f-14 mb-0 text-dark float-right"><span
                                                x-text="$store.cart.amount_in_cur(item.sale_price*item.qty)"></span> <del
                                                class="small text-secondary"
                                                x-text="$store.cart.amount_in_cur(item.price*item.qty)">$ 0.0 </del></p>
                                        <p class="f-12 text-secondary float-left quantity-text">Quantity:<span
                                                x-text="item.qty"></span></p>
                                    </div>
                                </template> </template>
                        </div>
                        <div class="mb-3 bg-white rounded shadow-sm p-3 clearfix">
                            <p class="mb-1">Item Total <span class="float-right text-dark"
                                    x-text="$store.cart.amount_in_cur($store.cart.total)"></span></p>
                            <!--<p class="mb-1">GST Charges 10% <span class="float-right text-dark">$62.8</span></p>-->
                            <!--<p class="mb-1">Delivery Fee <span class="text-info" data-toggle="tooltip" data-placement="top" title="Total discount breakup">-->
                            <!--   <i class="icofont-info-circle"></i>-->
                            <!--   </span> <span class="float-right text-dark">$10</span>-->
                            <!--</p>-->
                            <p class="mb-1 text-info">Total Discount
                                <span class="float-right text-info"
                                    x-text="'-'+$store.cart.amount_in_cur($store.cart.total_discount)"></span>
                            </p>
                            <hr />
                            <h6 class="font-weight-bold text-danger mb-0">TO PAY <span class="float-right"
                                    x-text="$store.cart.amount_in_cur($store.cart.sub_total)">$1329</span></h6>
                        </div>
                    </div>
                    <div  style="text-align:center;max-width: 400px;
    height: 29px;
    margin: auto;
    background: #bcf6bc;
    line-height: 30px;
    color: green !important;">
                        You have saved <strong x-text="$store.cart.amount_in_cur($store.cart.total_discount)"></strong> on
                        the bill
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- End of Main -->
@endsection
