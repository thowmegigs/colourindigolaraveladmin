<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Gurdeep singh osahan">
    <meta name="author" content="Gurdeep singh osahan">
    <title>MJS Fashion  </title>

    <link rel="icon" type="image/png" href="{{ asset('fav-icon.png') }}">

    <link href="{{ asset('front_assets/vendor/bootstrap/css/bootstrap.min.css') }}"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('front_assets/vendor/slider/slider.css') }}">
 <link rel="stylesheet" href="{{ asset('commonjs/ion.rangeSlider.min.css') }}" />
    <link href="{{ asset('front_assets/vendor/select2/css/select2-bootstrap.css') }}" />
    <link href="{{ asset('front_assets/vendor/select2/css/select2.min.css') }}"
        rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('commonjs/vanilla-notify.css') }}">
    <link href="{{ asset('front_assets/vendor/fontawesome/css/all.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('front_assets/vendor/icofont/icofont.min.css') }}" rel="stylesheet">

    <link href="{{ asset('front_assets/css/style.css') }}?v=3" rel="stylesheet" />
     <link href="{{ asset('front_assets/css/swiper-bundle.min.css') }}" rel="stylesheet">

    <link rel="stylesheet"
        href="{{ asset('front_assets/vendor/owl-carousel/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('front_assets/vendor/owl-carousel/owl.theme.css') }}">
       <script src="https://store.flexipattern.biz/commonjs/vanilla-notify.js"></script>
    
    <style>
        
    .topl{
        margin-right: 10px;
    }
    .loadingoverlay_text{
        font-size:14px!importantimportant;
    }
    .loadingoverlay_element svg{
        width:20px!importantimportant; height:20px!importantimportant;
    }
     @media only screen and (max-width: 600px) {
            .section_heading{
                font-size:15px;font-weight:bold;
            }
     }
        @media only screen and (min-width: 600px) {
        
  .prod_image {
    height: 300px;
  }
}
.form_error{
    color:red;
}

    .footer-btn {
      border-radius: 50px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
   

    .cart-btn {
          position: absolute;
            top: -48%;background:#f53434;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 50%;
            width: 46px;
            height: 46px;color:white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .pol{
       box-shadow: 0px -3px 20px 2px #888;
         -webkit-box-shadow: 0px -3px 20px 2px #888;
          -moz-box-shadow: 0px -3px 20px 2px #888;
        
    }
    .navbar-custom {
           position: fixed;
    bottom: 0;
    width: 100%;
    height: 51px;
    border-top-left-radius: 19px;
     border-top-right-radius: 19px;
    background-color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    box-shadow: 2px -7px 5px rgba(0, 0, 0, 0.1);
}

        .navbar-custom .curve {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 40px;
            background-color: #fff;
            border-top-left-radius: 40px;
            border-top-right-radius: 40px;
            z-index: 1;
              box-shadow: 2px -7px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar-custom .fab {
            margin-top:16px;
            position: absolute;
            top: -35px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 60px;
            background-color: #d81b60; /* Pink button */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            border: none;
            z-index: 2;
        }

        .navbar-custom .fab:hover {
            background-color: #c2185b;
        }

        .navbar-custom .fab .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ff3d00; /* Red badge */
            color: white;
            font-size: 12px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

       
         .navbar-custom .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #3f4848;
            font-size: 14px;
            text-decoration: none;
            flex: 1;
        }

        .navbar-custom .nav-item i {
            font-size: 20px;
            margin-bottom: 3px;
        }

        .navbar-custom .nav-item:hover {
            color: #ff3e56; /* Light green hover color */
        }
        .navbar-custom .nav-item.active,
        .navbar-custom .nav-item:hover,.navbar-custom .nav-item.active i {
            color: #ff3e56; /* Light green hover color */
        }
        </style>
</head>

<body>
  
  
    <div class="btn-primary pt-2 pb-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <a href="shop.html" class="mb-0 text-white">
                       
                    </a>
                </div>
            </div>
        </div>
    </div>
      <!-- Footer Navigation -->
  
    <div class="modal fade login-modal-main" id="login">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="login-modal">
                        <div class="row">
                           
                            <div class="col-md-12">
                                <button type="button" class="close close-top-right position-absolute"
                                    data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="icofont-close-line"></i></span>
                                    <span class="sr-only">Close</span>
                                </button>
                                   <form class="position-relative" x-data="{
    current_tab: 'Login',
    form_data: {
        name: '',
        phone: '',password:'',password_confirmation:'',
       
    },
    error: {
        name: '',
        phone: '',
        otp: '',password:'',password_confirmation:'',
    },
    is_form_valid: true,
    is_otp_submit: false,
    show_otp_form: false,
    loading: false,
    isValidPhone(value) {
        let expr = /^(0|91)?[6-9][0-9]{9}$/;
        return expr.test(value)

    },
    validateForm() {
        if (this.current_tab == 'Register') {
            if (this.form_data.name.length < 3) {
                this.error.name = 'Name is required'
                this.is_form_valid = false;
                return this.is_form_valid;
            } else {
                this.error.name = ''
                this.is_form_valid = true;
                 return this.is_form_valid;
            }
             if (this.form_data.password!==this.form_data.password_confirmation) {
            this.error.password_confirmation = 'Password did n\'t match'
            this.is_form_valid = false;
             return this.is_form_valid;
                }
                else {
                    this.error.password_confirmation = ''
                    this.is_form_valid = true;
                     return this.is_form_valid;
                }
        }
        if (this.form_data.phone.length < 1) {
            this.error.phone = 'Phone NUmber  is rquired'
            this.is_form_valid = false;
             return this.is_form_valid;
        } else if (!this.isValidPhone(this.form_data.phone)) {
            this.error.phone = 'Phone number is invalid'
            this.is_form_valid = false;
             return this.is_form_valid;
        } else {
            this.error.phone = ''
            this.is_form_valid = true;
             return this.is_form_valid;
        }
        if (this.form_data.password.length < 5) {
            this.error.password = 'Password must be minimum 6 characters'
            this.is_form_valid = false;
             return this.is_form_valid;
        }
         else {
            this.error.password = ''
            this.is_form_valid = true;
             return this.is_form_valid;
        }
           
        if (this.current_tab == 'OTP') {
            let first = document.querySelector('#first').value
            let second = document.querySelector('#second').value
            let third = document.querySelector('#third').value
            let fourth = document.querySelector('#fourth').value
            let fifth = document.querySelector('#fifth').value
            let sixth = document.querySelector('#sixth').value
            if (first.length < 1 || second.length < 1 || third.length < 1 || fourth.length < 1 || fifth.length < 1 || sixth.length < 1) {
                error.otp = '6 digit OTP is required'
                this.is_form_valid = false;
                 return this.is_form_valid;
            } else {
                error.otp = ''
                this.is_form_valid = true;
                 return this.is_form_valid;
            }
        }
       return this.is_form_valid;
    },
    login() {
     this.validateForm();
        if (this.is_form_valid) {
            this.loading=true;
            fetch('/customer_login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',

                    },
                    body: JSON.stringify(this.form_data)
                })
                .then(async(response)=> {
                    this.loading=false;
                    const res = await response.json();
                  
                  if (response.ok) {
                        this.loading=false;
                        
                        if (res['success']) {
                           this.form_data={ phone: '',password:''};
                           this.current_tab='Login'
                           vNotify.success({ text: res['message'],title: 'Success' });
                           setTimeout(()=>{location.reload()},3000)
                        } else
                            vNotify.error({ text: res['message'],title: 'Error' });
                    }
                    else if (response.status === 422) {
                        
                        const errors = res.errors; // Laravel validation errors
                        Object.values(errors).forEach(messages => {
                        console.log(messages[0])
                            messages.forEach(message => vNotify.error({text: message,title: 'Error'})); // Display each error
                        });
                    }
                    else
                            vNotify.error({ text: res['message'],title: 'Error' });
                   
                })
        }
    },
   
    register() {
    console.log(this.validateForm());
   
        if (this.is_form_valid) {
            this.loading=true;
            fetch('/customer_register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',

                    },
                    body: JSON.stringify(this.form_data)
                })
                .then(async(response)=> {
                    this.loading=false;
                    const res = await response.json();
                  
                  if (response.ok) {
                        this.loading=false;
                        
                        if (res['success']) {
                           this.form_data={ name: '',phone: '',password:'',password_confirmation:''};
                           this.current_tab='Login'
                           vNotify.success({ text: res['message'],title: 'Success' });
                        } else
                            vNotify.error({ text: res['message'],title: 'Error' });
                    }
                    else if (response.status === 422) {
                        
                        const errors = res.errors; // Laravel validation errors
                        Object.values(errors).forEach(messages => {
                        console.log(messages[0])
                            messages.forEach(message => vNotify.error({text: message,title: 'Error'})); // Display each error
                        });
                    }
                    else
                            vNotify.error({ text: res['message'],title: 'Error' });
                   
                })
        }
    },
    verify_otp() {
        if (this.is_form_valid) {
            this.loading=true;
            let first = document.querySelector('#first').value
            let second = document.querySelector('#second').value
            let third = document.querySelector('#third').value
            let fourth = document.querySelector('#fourth').value
            let fifth = document.querySelector('#fifth').value
            let sixth = document.querySelector('#sixth').value
            fetch('/verify_otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',

                    },
                    body: JSON.stringify({otp:first+second+third+fourth+fifth+sixth})
                })
                .then((response) => response.json()).then(res => {
                    this.loading=false;
                    if (res['success']) {
                        setTimeout(function(){
                            location.reload();
                        },3000)
                        this.$store.cart.is_logged_in='Yes';
                        vNotify.success({ text: 'Login Successfully', title: 'Success' });
                    } else
                        vNotify.error({ text: res['message'], title: 'Error' });
                })
        }
    }

}">
                                    <ul class="mt-4 mr-4 nav nav-tabs-login float-right position-absolute"
                                        role="tablist">
                                        <li>
                                            <a class="nav-link-login" :class="current_tab=='Login'?'active':''" @click="current_tab='Login'" data-toggle="tab" href="#login-form"
                                                role="tab"><i class="icofont-ui-lock"></i> LOGIN</a>
                                        </li>
                                        <li>
                                            <a class="nav-link-login" :class="current_tab=='Register'?'active':''" data-toggle="tab" @click="current_tab='Register'" href="#register" role="tab"><i
                                                    class="icofont icofont-pencil"></i> REGISTER</a>
                                        </li>
                                    </ul>
                                    <div class="login-modal-right p-4">

                                        <div class="tab-content">
                                            <div class="tab-pane" :class="current_tab=='Login'?'active':''"  id="login-form" role="tabpanel">
                                                <h5 class="heading-design-h5 text-dark">LOGIN</h5>
                                                <fieldset class="form-group mt-4">
                                                    <label>Enter Email/Mobile number</label>
                                                    <input type="text" name="phone" id="phone" x-model="form_data.phone" class="form-control"
                                                        placeholder="+91 123 456 7890" required>
                                                        <span x-show="error.phone.length>0" x-text="error.phone" class="form_error"></span>
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <label>Enter Password</label>
                                                    <input type="password"  name="password" x-model="form_data.password" id="password" class="form-control" placeholder="********">
                                                      <span x-show="error.password.length>0" x-text="error.password" class="form_error"></span>
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <button type="button" @click="login()"  class="btn btn-lg btn-primary btn-block"
                                                    x-text="loading?'Please wait..':'Enter to your account'">
                                                    </button>
                                                </fieldset>
                                               
                                            </div>
                                            <div class="tab-pane" :class="current_tab=='Register'?'active':''"  id="register" role="tabpanel">
                                                <h5 class="heading-design-h5 text-dark">REGISTER</h5>
                                                <fieldset class="form-group mt-4">
                                                    <label>Enter name</label>
                                                    <input type="text" class="form-control" name="name" id="name" x-model="form_data.name"
                                                        placeholder="Enter name">
                                                        <span x-show="error.name.length>0" x-text="error.name" class="form_error"></span>
                                                </fieldset>
                                                <fieldset class="form-group mt-4">
                                                    <label>Enter Mobile number</label>
                                                    <input type="text" name="phone" id="password_1" x-model="form_data.phone" class="form-control"
                                                        placeholder="+91 123 456 7890">
                                                        <span x-show="error.phone.length>0" x-text="error.phone" class="form_error"></span>
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <label>Enter Password</label>
                                                    <input type="password" class="form-control" name="password"  x-model="form_data.password" placeholder="********">
                                                    <span x-show="error.password.length>0" x-text="error.password" class="form_error"></span>
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <label>Enter Confirm Password </label>
                                                    <input type="password" name="password_confirmation"  x-model="form_data.password_confirmation"  class="form-control" placeholder="********">
                                                    <span x-show="error.password_confirmation.length>0" x-text="error.password_confirmation" class="form_error"></span>
                                                </fieldset>
                                                <fieldset class="form-group">
                                                    <button type="button"
                                                        class="btn btn-lg btn-primary btn-block" @click="register()" 
                                                        x-text="loading?'Please wait..':'Create Your Account'">
                                                    </button>
                                                </fieldset>
                                               
                                               
                                            </div>
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
    @include('layouts.frontend.header')
    @yield('content')
       <nav class="navbar-custom d-sm-none" x-data>
        <div class="curve"></div>
       
        <button class="fab " data-toggle="offcanvas">
            <i class="fa fa-shopping-cart"></i>
            <div class="cart-badge"><span  x-text="$store.cart.items.length"></span></div> <!-- Cart Count Badge -->
        </button>
         <a href="/" class="nav-item">
            <i class="fas fa-home"></i>
            
        </a>
        <a href="javascript:void(0)" id="menu-toggle2" class="nav-item" >
            <i class="fas fa-bars"></i>
            
        </a>
       
       
    </nav>
    @include('layouts.frontend.footer')

</body>


</html>
