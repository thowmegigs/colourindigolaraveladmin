@props(['user_address'])
<div class="mfp-hide" style="max-width:80%;padding:20px;" id="address_edit_modal" tabindex="-1"
    aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-body" x-data="{
                loading:false,
                address: {
                    billing_fname: '{!! $user_address?->billing_fname !!}',
                    billing_lname: '{!! $user_address?->billing_lname !!}',
                    billing_address1: '{!! $user_address?->billing_address1 !!}',
                    billing_address2: '{!! $user_address?->billing_address2 !!}',
                    billing_city: '{!! $user_address?->billing_city !!}',
                    
                    billing_email: '{!! $user_address?->billing_email !!}',
                    billing_pincode: '{!! $user_address?->billing_pincode !!}',
                    billing_phone: '{!! \Auth::check() ? auth()->user()->phone : $user_address?->billing_phone !!}',
                    shipping_fname: '{!! $user_address?->shipping_fname !!}',
                    shipping_lname: '{!! $user_address?->shipping_lname !!}',
                    shipping_address1: '{!! $user_address?->shipping_address1 !!}',
                    shipping_address2: '{!! $user_address?->shipping_address2 !!}',
                    shipping_city: '{!! $user_address?->shipping_city !!}',
                   
                    shipping_email: '{!! $user_address?->shipping_email !!}',
                    shipping_pincode: '{!! $user_address?->shipping_pincode !!}',
                    shipping_phone: '{!! \Auth::check() ? auth()->user()->phone : $user_address?->shipping_phone !!}',
            
            
                },
                save(){
                    this.loading=true;
                    fetch('/update_address', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(this.address)
                    }).then(response => response.json()).then(res => {
                        this.loading = false;
                        if (res['success']) {
                           
                            vNotify.success({ text: ' Address Updated Successfully', title: 'Suceess' });
            
                        } else {
                            vNotify.error({ text: res['message'], title: 'Error' });
                        }
            
                    })
                }
            }">
                <form class="form account-details-form" action="#" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class=" ls-25 font-weight-bold">Billing Address</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">First name </label>
                                        <input type="text" x-model="address.billing_fname" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname">Last name </label>
                                        <input type="text" x-model="address.billing_fname" placeholder="Doe"
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Address1 </label>
                                        <input type="text" x-model="address.billing_address1" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Address2 </label>
                                        <input type="text" x-model="address.billing_address2" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">City </label>
                                        <input type="text" x-model="address.billing_city" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Address2 </label>
                                        <input type="text" x-model="address.billing_address2" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Pincode </label>
                                        <input type="number" x-model="address.billing_pincode" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Phone </label>
                                        <input type="number" x-model="address.billing_phone" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="firstname">Email </label>
                                        <input type="email" x-model="address.billing_email" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <h5 class=" ls-25 font-weight-bold">Shipping Address</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">First name </label>
                                        <input type="text" x-model="address.shipping_fname" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname">Last name </label>
                                        <input type="text" x-model="address.shipping_fname" placeholder="Doe"
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Address1 </label>
                                        <input type="text" x-model="address.shipping_address1" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Address2 </label>
                                        <input type="text" x-model="address.shipping_address2" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">City </label>
                                        <input type="text" x-model="address.shipping_city" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Address2 </label>
                                        <input type="text" x-model="address.shipping_address2" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Pincode </label>
                                        <input type="number" x-model="address.shipping_pincode" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Phone </label>
                                        <input type="number" x-model="address.shipping_phone" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="firstname">Email </label>
                                        <input type="email" x-model="address.shipping_email" placeholder=""
                                            class="form-control form-control-md">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>



                    <button type="button" @click.prevent="save()"  class="btn btn-dark btn-rounded btn-sm mb-4" x-text="loading?'Please wait...':'Save Changes'"></button>
                </form>

            </div>
        </div>
    </div>
</div>
