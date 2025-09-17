@extends('layouts.vendor_auth.app')
@section('content')
@php
    $setting = \App\Models\Setting::first();
@endphp
<style>
.heading-underline {
    width: 60px;
    height: 3px;
    background-color: #dc3545; /* Bootstrap red */
    margin: 8px auto 0;
    border-radius: 2px;
}
.fc {
    margin-right: 150px;
    max-width: 350px;
}
@media screen and (max-width:700px) {
    .fc {
        margin: auto;
        max-width: 350px;
    }
}
.focus-red:focus {
    outline: none !important;
    box-shadow: none !important;
    border: 1px solid #8B0000 !important;
}
footer ul li {
    margin-bottom: 6px; 
}
footer a:hover {
    color: #dc3545 !important; /* Bootstrap red */
}
.step-card {
    border-radius: 1rem;
    padding: 43px 10px;
    padding-top: 20px;
    text-align: center;height:347px;
    transition: all 0.3s ease;
}
.step-card:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}
.step-card img {
    max-height: 120px;
    margin-bottom: 1rem;
}
.disclaimer-box {
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f8f9fa;
    padding: 20px;
    font-size: 15px;
    line-height: 1.6;
}
.btn-outline-primary {
    border-radius: 30px;
    padding: 0.5rem 1.5rem;
}
.disclaimer-box {
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f8f9fa;
    padding: 20px;
    font-size: 14px;
}
.disclaimer-box strong {
    font-weight: 700;
}
</style>

<div class="bg-white w-100">
    <div class="d-flex align-items-center justify-content-end vh-100 w-100"
        style="background: url('{{ asset('storage/settings/' . $setting->vendor_background_image) }}')  
           center/cover no-repeat;">

        <!-- Semi-transparent overlay -->
        <div class="position-absolute top-0 start-0 w-100 h-100"></div>

        <!-- Login Form -->
        <div class="position-relative z-1 w-100 fc">
            <div class="bg-white shadow-lg rounded-lg px-4 py-6"
                style="height:600px; border-radius:20px;padding-top:60px; padding-bottom:60px;">
                <div class="text-center mb-4">
                    <img src="https://colourindigo.com/logo.png" alt="Logo" class="mb-3" style="max-height: 50px;">
                    <h4 class="fw-bold">Welcome Back</h4>
                    <p class="text-muted">Login to your vendor account</p>
                </div>

                <form data-module="Login" action="{{ domain_route('login') }}" id="login_form" method="POST">
                    @csrf
                    <!-- <x-alert /> -->
<div id="validation_errors"></div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" class="py-2 form-control focus-red" name="email"
                            placeholder="Enter email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control py-2 focus-red"
                                placeholder="Enter password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('ForgetPasswordGet') }}" class="text-decoration-underline small">Forgot
                            password?</a>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" id="login_btn" class="btn btn-danger btn-lg">Login</button>
                        <a href="/register" class="btn btn-link">Not registered? Signup</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

     <!-- Start Selling Section -->
        <div class="container my-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Start Selling In 4 Simple Steps</h2>
                <div class="heading-underline"></div>
            </div>
            <div class="row g-4">
                <!-- Register -->
                <div class="col-md-3">
                    <div class="step-card bg-light">
                        <img src="{{ asset('register.png') }}" alt="Register" class="img-fluid">
                        <h5>Register</h5>
                        <p class="p-0 pb-2 m-0">Find all the <strong>onboarding requirements</strong> to create your account
                            here.</p>
                        <a href="#" class="btn btn-outline-primary">Watch Video</a>
                    </div>
                </div>
                <!-- Sell -->
                <div class="col-md-3">
                    <div class="step-card bg-warning bg-opacity-10">
                        <img src="{{ asset('sell.png') }}" alt="Sell" class="img-fluid">
                        <h5>Sell</h5>
                        <p>Learn all about fulfilment models, platform integration &amp; <strong>prerequisites</strong> for
                            operational readiness here.</p>
                        <a href="#" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
                <!-- Earn -->
                <div class="col-md-3">
                    <div class="step-card bg-success bg-opacity-10">
                        <img src="{{ asset('earn.png') }}" alt="Earn" class="img-fluid">
                        <h5>Earn</h5>
                        <p>Get <strong>secure &amp; timely payments</strong> on predefined days. Find out about the payment
                            cycle.</p>
                        <a href="#" class="btn btn-outline-primary">Watch Video</a>
                    </div>
                </div>
                <!-- Grow -->
                <div class="col-md-3">
                    <div class="step-card bg-danger bg-opacity-10">
                        <img src="{{ asset('grow.png') }}" alt="Grow" class="img-fluid">
                        <h5>Grow</h5>
                        <p style="margin-bottom:36px;">Get <strong>tailored support</strong> at every step to steer your
                            business.</p>
                        <a href="#" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
        </div>

    <!-- Disclaimer Section -->
    <div class="container my-4">
        <div class="disclaimer-box">
            <p><strong>DISCLAIMER:</strong> Please be cautious of individuals falsely posing as Colourindigo employees, affiliates, agents or representatives. They may attempt to deceive you by seeking personal information or soliciting money under the guise of offering opportunities to be a seller on our platform. <strong>Colourindigo does not charge any onboarding fee or refundable deposit from sellers.</strong> We strongly advise you to exercise vigilance and disregard such offers.</p>

            <p>However, a <strong>one time growth enablement charge</strong> is applicable for new sellers on Colourindigo platform once the sellers start operating on the platform. The charge is <strong>netted off</strong> against the payout Colourindigo has to make to sellers for selling their products on Colourindigo platform. The charge is used to provide <strong>access to product listing ad credits worth 2x the growth enablement charge and partner insights platform</strong> in order to help sellers accelerate their growth. More details can be found in terms of use document.</p>

            <p>Please do not accept or entertain any email communication from email IDs that do not contain "@colourindigo.com". All Colourindigo representatives' email IDs are expected to have "@colourindigo.com" as part of their email addresses.</p>

            <p>Engaging in such fraudulent activities may lead to criminal and civil liabilities. We are committed to cooperating with law enforcement authorities to take appropriate action against these imposters. Please note that Colourindigo, along with its affiliates and subsidiaries, cannot be held liable for any claims, losses, damages, expenses, or inconvenience resulting from the actions of these imposters.</p>
        </div>
    </div>
    <div class="container my-4">
    <div class="disclaimer-box">
        <h6 class="fw-bold my-3">ONLINE SELLING MADE EASY AT Colourindigo</h6>
        <p>
            If you are looking to sell fashion, lifestyle, beauty & grooming and personal care products online, you are at the right place! 
            When it comes to selling online, Colourindigo is the ultimate destination for fashion, beauty and lifestyle, with a wide array of 
            merchandise including clothing, footwear, accessories, jewelry, personal care products and more. 
        </p>
        <p>
            With more than <strong>55 million active users</strong> every month and servicing over <strong>17,000 pin codes across India</strong>, 
            you can grow your business by registering on India’s best fashion, beauty and lifestyle platform.
        </p>
   
        <h6 class="fw-bold my-3">BEST ONLINE SHOPPING SITE IN INDIA FOR FASHION</h6>
        <p>
            Be it clothing, footwear or accessories, Colourindigo offers the ideal combination of fashion and functionality 
            for men, women and kids. From affordable styles to luxury brands, Colourindigo as an online seller showcases a 
            wide array of styles with loyal customers all across India.
        </p>
        <p>
            So whether your brand is massy or premium or has a very niche audience, 
            if you are selling in India then you should be listed on Colourindigo.
        </p>
        <h6 class="fw-bold my-3">BEST PLACE FOR ONLINE FASHION</h6>
        <p>
           Colourindigo is one of the unique online sellers in India where fashion is accessible to all. With a bunch of amazing filters and search options, customers across India find a wide range of products and styles for every budget. If you are listed on Colourindigo selling 
           will be extremely easy as customers will come across your listed products through various touch points.
        </p>

    </div>
</div>

<footer class="bg-light pt-5 pb-3">
    <div class="container">
        <div class="row text-center text-md-start">
            <!-- About Section -->
            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">About Colourindigo</h6>
                <p>Colourindigo is a leading marketplace offering a wide range of products from trusted vendors across India.</p>
                <div class="d-flex  justify-content-start gap-2 mb-3 align-items-center">
                    <a href="https://www.facebook.com/people/Colour-indigo/61561099752863/"><img src="https://colourindigo.com/images/facebook.png" alt="Facebook" width="24" style="width:25px;height:25px"></a>
                    <a href="https://www.instagram.com/colour.indigo/?igsh=eTJpa2JvNGowNHp5#"><img src="https://colourindigo.com/images/instagram.png" alt="Instagram" width="27" style="width:25px;height:25px"></a>
                    <a href="https://www.youtube.com/@colourindigo?si=nlV7m0Kh3xIst7n3"><img src="https://colourindigo.com/images/youtube.png" alt="YouTube" width="33" style="width:26px;height:35px"></a>
                </div>
                <a href="https://play.google.com/store/apps/details?id=com.puraniya.colour_indigo"><img src="https://colourindigo.com/images/google_play.png" alt="Get it on Google Play" style="max-width: 140px;"></a>
            </div>

            <!-- Customer Service -->
            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">Customer Service</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-decoration-none text-dark">Contact Us</a></li>
                    <li><a href="#" class="text-decoration-none text-dark">FAQs</a></li>
                    <li><a href="https://colourindigo.com/shipping_policy" class="text-decoration-none text-dark">Shipping & Delivery</a></li>
                    <li><a href="https://colourindigo.com/return_policy" class="text-decoration-none text-dark">Returns & Exchanges</a></li>
                    <li><a href="https://colourindigo.com/terms" class="text-decoration-none text-dark">Terms & Conditions</a></li>
                    <li><a href="https://colourindigo.com/privacy_policy" class="text-decoration-none text-dark">Privacy Policy</a></li>
                    <li><a href="https://colourindigo.com/refund_policy" class="text-decoration-none text-dark">Refund Policy</a></li>
                </ul>
            </div>

            <!-- My Account -->
            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">My Account</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-decoration-none text-dark">My Profile</a></li>
                    <li><a href="#" class="text-decoration-none text-dark">Order History</a></li>
                    <li><a href="#" class="text-decoration-none text-dark">Wishlist</a></li>
                    <li><a href="#" class="text-decoration-none text-dark">Seller Login</a></li>
                    <li><a href="#" class="text-decoration-none text-dark">Become a Seller</a></li>
                </ul>
            </div>

            <!-- Contact Us -->
            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">Contact Us</h6>
                <ul class="list-unstyled">
                    <li><i class="bi bi-geo-alt me-2"></i> Haryana 127021</li>
                    <li><i class="bi bi-telephone me-2"></i> +919991110716</li>
                    <li><i class="bi bi-envelope me-2"></i> support@colourindigo.com</li>
                </ul>
            </div>
        </div>

        <hr>
        <div class="text-center small text-muted">
            © {{date('Y')}} Colourindigo Marketplace. All rights reserved.
        </div>
    </div>
</footer>


</div>

<script>
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>
@endsection
