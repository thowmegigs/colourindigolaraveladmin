<div class="login-popup mfp-hide" id="auth_modal" x-data="{
    current_tab: 'Login',
    form_data: {
        name: '',
        phone: '',
        otp: ''
    },
    error: {
        name: '',
        phone: '',
        otp: ''
    },
    is_form_valid: true,
    is_otp_submit: false,
    show_otp_form: false,
    loading: false,
    isValidPhone(value) {
        let expr = /^(0|91)?[6-9][0-9]{9}$/;
        return expr.test(mobileNumber)

    },
    validateForm() {
        if (this.current_tab == 'Register') {
            if (this.form_data.name.length < 3) {
                this.error.name = 'Name is required'
                this.is_form_valid = false;
            } else {
                this.error.name = ''
                this.is_form_valid = true;
            }
        }
        if (this.form_data.phone.length < 1) {
            this.error.phone = 'Phone NUmber  is rquired'
            this.is_form_valid = false;
        } else if (!this.isValidPhone(this.form_data.phone)) {
            this.error.phone = 'Phone number is invalid'
            this.is_form_valid = false;
        } else {
            error.phone = ''
            this.is_form_valid = true;
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
            } else {
                error.otp = ''
                this.is_form_valid = true;
            }
        }

    },
    login() {
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
                .then((response) => response.json()).then(res => {
                    this.loading=false;
                    if (res['success']) {
                        this.current_tab = 'OTP';
                    } else
                        vNotify.error({ text: res['message'],title: 'Error' });
                })
        }
    },
   
    register() {
        if (this.is_form_valid) {
            this.loading=true;
            fetch('/customer_register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',

                    },
                    body: JSON.stringify(this.form_data)
                })
                .then((response) => response.json()).then(res => {
                    this.loading=false;
                    if (res['success']) {
                      
                        vNotify.success({ text: res['message'], title: 'Success' });
                    } else
                        vNotify.error({ text: res['message'], title: 'Error' });
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
    <div class="tab tab-nav-boxed tab-nav-center tab-nav-underline" x-data>
        <ul class="nav nav-tabs text-uppercase" role="tablist">
            <li class="nav-item">
                {{-- <span x-text="current_tab=='Login'?'Login1':'Register1'"></span> --}}
                <a href="#sign-in" :class="current_tab=='Login'?'Active':''" @click="current_tab='Login'" class="nav-link active">Sign In</a>
            </li>
            <li class="nav-item">
                <a href="#sign-up" :class="current_tab=='Register'?'Active':''" @click="current_tab='Register'" class="nav-link">Sign Up</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" :class="current_tab=='Login'?'Active':''" id="sign-in">
                <div x-show="current_tab=='Login'" class="form-group">
                    <label>Phone Number *</label>
                    <input type="number" class="form-control" name="phone" id="phone" x-model="form_data.phone"
                        required>
                    <span x-show="error.phone.length>0" x-text="error.phone" class="form_error"></span>
                </div>
                <div x-show="current_tab=='OTP'" class="form-group">
                    <h6>Please enter the one time password to verify your number</h6>
                    <div class="wra">
                        <div id="otp" class="inputs d-flex flex-row justify-content-start mt-2">
                            <input class="m-2 text-center form-control rounded" type="text" id="first"
                                maxlength="1" /> <input class="m-2 text-center form-control rounded" type="text"
                                id="second" maxlength="1" /> <input class="m-2 text-center form-control rounded"
                                type="text" id="third" maxlength="1" /> <input
                                class="m-2 text-center form-control rounded" type="text" id="fourth"
                                maxlength="1" /> <input class="m-2 text-center form-control rounded" type="text"
                                id="fifth" maxlength="1" /> <input class="m-2 text-center form-control rounded"
                                type="text" id="sixth" maxlength="1" />
                        </div>
                    </div>
                </div>
                {{-- <div class="form-group mb-0">
                    <label>Password *</label>
                    <input type="text" class="form-control" name="password" id="password" required>
                </div> --}}
                <template x-if="current_tab=='Login'">
                    <a href="#" @click="login()" class="btn btn-primary" x-text="loading?'Please wait..':'Submit'"></a>
                </template>
                <template x-if="current_tab=='OTP'">
                    <a href="#" @click="verify_otp()" class="btn btn-primary" x-text="loading?'Please wait..':'Verify OTP'"></a>
                </template>
            </div>
            <div class="tab-pane" :class="current_tab=='Register'?'Active':''" id="sign-up">
                <div class="form-group">
                    <label>Your Name *</label>
                    <input type="text" class="form-control" name="email_1" id="email_1" x-model="form_data.name"
                        required>
                    <span x-show="error.name.length>0" x-text="error.name" class="form_error"></span>
                </div>
                <div class="form-group mb-5">
                    <label>Phone Number *</label>
                    <input type="number" class="form-control" name="phone" id="password_1" x-model="form_data.phone"
                        required>
                    <span x-show="error.phone.length>0" x-text="error.phone" class="form_error"></span>
                </div>

                <a href="#" @click="register()" class="btn btn-primary" x-text="loading?'Please wait..':'Submit'"></a>
            </div>
        </div>
        {{-- <p class="text-center">Sign in with social account</p> --}}
        
    </div>
</div>
