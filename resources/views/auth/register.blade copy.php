<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Seller Registration | Colourindigo Seller Registration</title>
    <!-- Bootstrap 5 CSS -->
      <link rel="shortcut icon" href="https://colourindigo.com/favicon-16x16.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{asset('commonjs/select2/select2.css')}}">
    <style>
        :root {
            --primary-color: #ae1313;
            --primary-dark: #ae1313;
            --secondary-color: #ae1313;
            --success-color: #0bb4aa;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --border-radius: 8px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .seller-registration-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
    .input-group-text{
      height:38px;
    }
        .registration-card {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .card-header {
            /* background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white; */
            background:#c629291a;
            border-bottom: none;
            padding: 1rem;
        }

        .card-header h1 {
            font-size: 1.3rem;
            margin-bottom: 0;
        }

        .card-header p {
            font-size: 0.9rem;
            margin-bottom: 0;
            color:black;
        }

        .section-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .section-subtitle {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.75rem;
            font-size: 1rem;
            padding-bottom: 0.25rem;
            border-bottom: 1px solid #eee;
        }

        /* Compact Form Styles */
        .form-control,
        .form-select {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            height: auto;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
            border-color: var(--primary-color);
        }

        .form-label {
            font-weight: 500;
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        .form-text {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        /* Compact Button Styles */
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, var(--primary-dark), var(--secondary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .btn-success {
            background: linear-gradient(to right, var(--success-color), #06d6a0);
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-success:hover {
            background: linear-gradient(to right, #09a398, #05c091);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(11, 180, 170, 0.3);
        }

        .btn-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
        }

        .btn-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Compact Progress Steps */
        .steps-container {
            padding: 0 0.5rem;
            margin-bottom: 1.5rem;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .steps::before {
            content: "";
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .step-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            margin-bottom: 6px;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }

        .step-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .step-active .step-icon {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
        }

        .step-active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Compact OTP Input */
        .otp-input {
            font-size: 1.25rem;
            letter-spacing: 0.5rem;
            font-weight: 600;
        }

        /* Compact Verification and Success Icons */
        .verification-icon,
        .success-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .verification-icon {
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            color: var(--primary-color);
        }

        .success-icon {
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            color: var(--success-color);
        }

        /* Compact Details Card */
        .detail-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: rgba(67, 97, 238, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            margin-right: 0.75rem;
            flex-shrink: 0;
            font-size: 0.8rem;
        }

        .detail-label {
            font-size: 0.75rem;
            color: #6c757d;
            margin-bottom: 0.1rem;
        }

        .detail-value {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.9rem;
        }

        /* Compact spacing */
        .card-body {
            padding: 1.5rem;
        }

        .mb-4 {
            margin-bottom: 1rem !important;
        }

        .mb-3 {
            margin-bottom: 0.75rem !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .step-label {
                font-size: 0.7rem;
            }

            .step-icon {
                width: 25px;
                height: 25px;
            }
        }

        /* Input group with icon */
        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
            color: #6c757d;
            padding: 0.5rem 0.75rem;
        }

        .input-group > .form-control {
            height: 38px;
        }

        .input-group > .form-select {
            height: 38px;
        }
        .select2-selection__rendered{
              font-size: 14px;
    color: #7f8077;
        }
  .select2-selection--single{
    height:38px!important;
  }
  .select2-container--default .select2-results__option,.select2-container--default .select2-selection--single .select2-selection__rendered  {
  font-size: 11px;color: #7f8077;
}
 .select2-container--default .select2-selection--single .select2-selection__rendered  {
  font-size: 14px;color: #7f8077;
}
.input-group .select2-container {
  flex: 1 1 auto;
  width: 1% !important;
}

/* Restore border & padding like Bootstrap's form-select */
.select2-container--default .select2-selection--single {
  height: calc(2.375rem + 2px); /* Matches .form-select height (Bootstrap 5) */
  padding: 0.375rem 0.75rem;
  border: 1px solid #ced4da;
  border-left: none; /* Avoid double border next to input-group-text */
  border-radius: 0 0.375rem 0.375rem 0;
  background-color: #fff;
  box-sizing: border-box;
}

/* Align arrow vertically */
.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 100%;
  right: 10px;
}

/* Adjust position inside input group */
.input-group .select2-selection--single {
  border-left: none;
}
        /* Password strength */
        .password-requirements {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .password-requirements ul {
            padding-left: 1rem;
            margin-bottom: 0;
        }

        .password-requirements li {
            margin-bottom: 0.1rem;
        }

        /* Form sections */
        .form-section {
            margin-bottom: 1.25rem;
        }

        /* Password toggle */
        .password-toggle {
            cursor: pointer;
        }

        /* Error text */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.75rem;
        }

        /* Verification status */
        .verification-status {
            display: inline-flex;
            align-items: center;
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
            margin-left: 0.5rem;
        }

        .verification-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .verification-success {
            background-color: #d4edda;
            color: #155724;
        }

        /* OTP verification tabs */
        .verification-tabs {
            display: flex;
            margin-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .verification-tab {
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-weight: 500;
            font-size: 0.9rem;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .verification-tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .verification-tab-content {
            display: none;
        }

        .verification-tab-content.active {
            display: block;
        }
    </style>
</head>

<body>
    <div class="seller-registration-container">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="card registration-card">
                        <div class="card-header text-center" style="padding-top: 20px;">
                            <img src="https://colourindigo.com/logo.png" style="height:40px;width:100px;" class="text-center"/>
                            <div class="d-flex align-items-center justify-content-center mb-1">

                                <!-- <i class="bi bi-shop text-dark me-2 fs-4"></i> -->
                                <h1>Seller Registration</h1>
                            </div>
                            <p class="text-dark">Join our marketplace and start selling today</p>
                        </div>

                        <div class="card-body">
                            <!-- Progress Steps -->
                            <div class="steps-container">
                                <div class="steps">
                                    <div class="step step-active" id="step1">
                                        <div class="step-icon">
                                            <i class="bi bi-shop"></i>
                                        </div>
                                        <div class="step-label">Business Info</div>
                                    </div>
                                    <div class="step" id="step2">
                                        <div class="step-icon">
                                            <i class="bi bi-shield-check"></i>
                                        </div>
                                        <div class="step-label">Verification</div>
                                    </div>
                                    <div class="step" id="step3">
                                        <div class="step-icon">
                                            <i class="bi bi-check-circle"></i>
                                        </div>
                                        <div class="step-label">Completion</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 1: Business Registration Form -->
                            <div id="businessInfoForm">
                                <form id="initialForm">
                                    @csrf

                                    <!-- Business Details Section -->
                                    <div class="form-section">
                                        <h4 class="section-subtitle">Business Details</h4>
                                        <div class="mb-3">
                                            <label for="business_name" class="form-label">
                                                Business Name
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-shop"></i></span>
                                                <input type="text" class="form-control" id="businessName" name="business_name" placeholder="Your store or business name" required>
                                            </div>
                                        </div>
                                     <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">
                                                Business Email
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Your business email address" required>
                                            </div>
                                            <div class="form-text">We'll send verification code to this email</div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">
                                                Contact Number
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Business contact number" required>
                                            </div>
                                            <div class="form-text">We'll send verification code to this phone</div>
                                        </div>
                                    </div>
                                    </div>

                                    <!-- Business Address Section -->
                                    <div class="form-section">
                                        <h4 class="section-subtitle">Business Address</h4>
                                        <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="streetAddress" class="form-label">
                                                Address 1 
                                               </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                                <input type="text" class="form-control" placeholder="Enter address including house no/flat no and locality name" id="streetAddress" name="address"  minlength="10" maxlength="80" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="streetAddress" class="form-label">
                                                 Address 2
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                                <input type="text" class="form-control" id="streetAddress2" name="address2" placeholder="Enter remaining address part" minlength="10" maxlength="80" required>
                                            </div>
                                        </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="storeCategory" class="form-label">
                                                    State
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-map"></i></span>
                                                    <select class="form-select" id="state_id" name="state_id" required>
                                                        <option value="">Select State</option>
                                                      @foreach($states as $st)
                                                         <option value="{{$st->id}}">{{$st->name}}</option>
                                                      @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="state" class="form-label">
                                                    City
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-map"></i></span>
                                                    <select class="form-select select2-ajax" id="city_id" name="city_id" required disabled>
                                                        <option value="">Select City</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="postalCode" class="form-label">
                                                    Postal/ZIP Code
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-mailbox"></i></span>
                                                    <input type="number" class="form-control" id="postalCode" name="pincode" placeholder="Postal/ZIP code" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Account Security Section -->
                                    <div class="form-section">
                                        <h4 class="section-subtitle">Account Security</h4>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">
                                                Password
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Create a secure password" required>
                                                <span class="input-group-text password-toggle" id="togglePassword">
                                                    <i class="bi bi-eye"></i>
                                                </span>
                                            </div>
                                            <div class="password-requirements">
                                                <small>Password must contain:</small>
                                                <ul>
                                                    <li>At least 8 characters</li>
                                                    <li>At least one uppercase letter</li>
                                                    <li>At least one number</li>
                                                    <li>At least one special character</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">
                                                Confirm Password
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
                                                <span class="input-group-text password-toggle" id="toggleConfirmPassword">
                                                    <i class="bi bi-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 mt-3">
                                        Continue to Verification
                                        <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Step 2: OTP Verification -->
                            <div id="verificationForm" style="display: none;">
                                <div class="text-center mb-3">
                                    <div class="verification-icon mb-2">
                                        <i class="bi bi-shield-check fs-2"></i>
                                    </div>
                                    <h3 class="section-title">Verify Your Business</h3>
                                    <p class="text-muted small">We've sent verification codes to your email and phone</p>
                                </div>

                                <!-- Email OTP Verification -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0 fs-6 fw-bold">Email Verification</h5>
                                        <span id="emailVerificationStatus" class="verification-status verification-pending">
                                            <i class="bi bi-clock me-1"></i> Pending
                                        </span>
                                    </div>
                                    <div class="card bg-light border-0 mb-3">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="detail-icon">
                                                    <i class="bi bi-envelope"></i>
                                                </div>
                                                <div>
                                                    <div class="detail-label">Email Address</div>
                                                    <div class="detail-value" id="emailDisplay"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <form id="emailOtpForm">
                                        @csrf
                                        <input type="hidden" name="verification_method" value="email">
                                        <input type="hidden" name="email" id="verificationEmail">
                                        <div class="mb-3">
                                            <label for="emailOtp" class="form-label">
                                                Enter Email Verification Code
                                            </label>
                                            <input type="text" class="form-control text-center otp-input" id="emailOtp" name="otp" placeholder="6-digit code" maxlength="6" required>
                                            <div class="form-text text-center">Enter the 6-digit code we sent to your email</div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <button type="button" class="btn btn-link" id="resendEmailOtp">
                                                <i class="bi bi-arrow-repeat me-1"></i> Resend Code
                                            </button>
                                            <span id="emailResendTimer" class="text-muted small" style="display: none;"></span>
                                            <button type="submit" class="btn btn-primary">
                                                Verify Email
                                                <i class="bi bi-check-circle ms-2"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Phone OTP Verification -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0 fs-6 fw-bold">Phone Verification</h5>
                                        <span id="phoneVerificationStatus" class="verification-status verification-pending">
                                            <i class="bi bi-clock me-1"></i> Pending
                                        </span>
                                    </div>
                                    <div class="card bg-light border-0 mb-3">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="detail-icon">
                                                    <i class="bi bi-telephone"></i>
                                                </div>
                                                <div>
                                                    <div class="detail-label">Phone Number</div>
                                                    <div class="detail-value" id="phoneDisplay"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <form id="phoneOtpForm">
                                        @csrf
                                        <input type="hidden" name="verification_method" value="phone">
                                        <input type="hidden" name="phone" id="verificationPhone">
                                        <div class="mb-3">
                                            <label for="phoneOtp" class="form-label">
                                                Enter Phone Verification Code
                                            </label>
                                            <input type="text" class="form-control text-center otp-input" id="phoneOtp" name="otp" placeholder="6-digit code" maxlength="6" required>
                                            <div class="form-text text-center">Enter the 6-digit code we sent to your phone</div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <button type="button" class="btn btn-link" id="resendPhoneOtp">
                                                <i class="bi bi-arrow-repeat me-1"></i> Resend Code
                                            </button>
                                            <span id="phoneResendTimer" class="text-muted small" style="display: none;"></span>
                                            <button type="submit" class="btn btn-primary">
                                                Verify Phone
                                                <i class="bi bi-check-circle ms-2"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-link" id="backToBusinessInfo">
                                        <span class="me-1">←</span> Back to Business Information
                                    </button>
                                </div>
                            </div>

                            <!-- Step 3: Success and Final Submission -->
                            <div id="completionForm" style="display: none;">
                                <form id="registrationForm">
                                    @csrf
                                    <input type="hidden" name="email" id="finalEmail">
                                    <input type="hidden" name="phone" id="finalPhone">
                                    <div class="text-center mb-3">
                                        <div class="success-icon mb-2">
                                            <i class="bi bi-check-circle fs-2"></i>
                                        </div>
                                        <h3 class="section-title">Verification Successful!</h3>
                                        <p class="text-muted small">
                                            Your business has been verified. You're almost ready to start selling!
                                        </p>
                                    </div>

                                    <div class="mb-3">
                                        <h5 class="mb-2 fs-6 fw-bold">Business Details:</h5>
                                        <div class="card bg-light border-0">
                                            <div class="card-body py-2">
                                                <div class="d-flex mb-2">
                                                    <div class="detail-icon">
                                                        <i class="bi bi-shop"></i>
                                                    </div>
                                                    <div>
                                                        <div class="detail-label">Business Name</div>
                                                        <div class="detail-value" id="summaryBusinessName"></div>
                                                    </div>
                                                </div>

                                                <div class="d-flex mb-2">
                                                    <div class="detail-icon">
                                                        <i class="bi bi-envelope"></i>
                                                    </div>
                                                    <div>
                                                        <div class="detail-label">Business Email</div>
                                                        <div class="detail-value" id="summaryEmail"></div>
                                                    </div>
                                                </div>

                                                <div class="d-flex mb-2">
                                                    <div class="detail-icon">
                                                        <i class="bi bi-telephone"></i>
                                                    </div>
                                                    <div>
                                                        <div class="detail-label">Contact Number</div>
                                                        <div class="detail-value" id="summaryPhone"></div>
                                                    </div>
                                                </div>

                                                <div class="d-flex mb-2">
                                                    <div class="detail-icon">
                                                        <i class="bi bi-geo-alt"></i>
                                                    </div>
                                                    <div>
                                                        <div class="detail-label">Address</div>
                                                        <div class="detail-value" id="summaryAddress"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">
                                        Complete Seller Registration
                                        <i class="bi bi-check-circle ms-2"></i>
                                    </button>

                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-link" id="editBusinessInfo">
                                            <span class="me-1">←</span> Edit Business Information
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('commonjs/select2/select2.js')}}"></script>

    <script>
        $(document).ready(function() {
            // Setup CSRF token for all AJAX requests
            $('#state_id').select2({
                minimumResultsForSearch: 5,
                width: '100%'
            })
            $('#city_id').select2({
                width: '100%',
                placeholder: 'Select City',
                allowClear: true
            });
            
            // On state change, fetch cities via AJAX
            $('#state_id').on('change', function () {
                let stateId = $(this).val();

                if (stateId) {
                    $.ajax({
                        url: '/getCities',
                        type: 'POST',
                        data: {state_id: stateId},
                        success: function (data) {
                            $('#city_id').empty().append('<option value="">Select City</option>');
                            $('#city_id').html(data['message']);
                            
                            $('#city_id').prop('disabled', false).trigger('change');
                        },
                        error: function () {
                            alert('Unable to fetch cities');
                        }
                    });
                } else {
                    $('#city_id').empty().append('<option value="">Select City</option>').prop('disabled', true);
                }
            });
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Form data object
            let formData = {
                business_name: '',
                email: '',
                phone: '',
                address: '',
                city_id: '',
                state_id: '',
                pincode: '',
                password: '',
                password_confirmation: '',
                isEmailVerified: false,
                isPhoneVerified: false
            };

            // Step management
            let currentStep = 1;

            // Password toggle functionality
            $('#togglePassword').click(function() {
                const passwordInput = $('#password');
                const icon = $(this).find('i');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            $('#toggleConfirmPassword').click(function() {
                const passwordInput = $('#password_confirmation');
                const icon = $(this).find('i');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            // Update steps UI
            function updateSteps(step) {
                $('.step').removeClass('step-active');
                $('#step1').addClass('step-active');
                
                if (step >= 2) {
                    $('#step2').addClass('step-active');
                }
                
                if (step >= 3) {
                    $('#step3').addClass('step-active');
                }
            }

            // Show the appropriate form based on current step
            function showCurrentForm() {
                $('#businessInfoForm, #verificationForm, #completionForm').hide();
                
                if (currentStep === 1) {
                    $('#businessInfoForm').show();
                } else if (currentStep === 2) {
                    $('#verificationForm').show();
                } else if (currentStep === 3) {
                    $('#completionForm').show();
                }
                
                updateSteps(currentStep);
            }

            // Update summary in completion form
            function updateSummary() {
                $('#summaryBusinessName').text(formData.business_name);
                $('#summaryEmail').text(formData.email);
                $('#summaryPhone').text(formData.phone);
                
                // Format address
                const address = `${formData.address},${formData.address2}, ${$('#city_id option:selected').text()}, ${$('#state_id option:selected').text()} ${formData.pincode}`;
                $('#summaryAddress').text(address);
            }

            // Clear validation errors
            function clearValidationErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            }

            // Display validation errors
            function displayValidationErrors(errors) {
                // Clear previous errors
                clearValidationErrors();
                
                // Display new errors
                $.each(errors, function(field, messages) {
                    const inputField = $('#' + field);
                    if (inputField.length) {
                        inputField.addClass('is-invalid');
                        
                        // Add error message after the input group
                        inputField.closest('.input-group').after('<div class="invalid-feedback">' + messages[0] + '</div>');
                    }
                });
            }

            // Format validation errors for SweetAlert
            function formatValidationErrorsForSwal(errors) {
                let errorHtml = '<ul style="text-align: left; margin-bottom: 0;">';
                $.each(errors, function(field, messages) {
                    errorHtml += '<li>' + messages[0] + '</li>';
                });
                errorHtml += '</ul>';
                return errorHtml;
            }

            // Start resend timer
            function startResendTimer(type) {
                const timerElement = type === 'email' ? $('#emailResendTimer') : $('#phoneResendTimer');
                const resendButton = type === 'email' ? $('#resendEmailOtp') : $('#resendPhoneOtp');
                
                let seconds = 60;
                timerElement.text(`Resend in ${seconds}s`);
                timerElement.show();
                resendButton.addClass('disabled');
                
                const timer = setInterval(function() {
                    seconds--;
                    timerElement.text(`Resend in ${seconds}s`);
                    
                    if (seconds <= 0) {
                        clearInterval(timer);
                        timerElement.hide();
                        resendButton.removeClass('disabled');
                    }
                }, 1000);
            }

            // Check if both email and phone are verified
            function checkVerificationStatus() {
                if (formData.isEmailVerified && formData.isPhoneVerified) {
                    // Move to completion step
                    currentStep = 3;
                    updateSummary();
                    showCurrentForm();
                    
                    // Show success message
                    Swal.fire({
                        title: 'Verification Complete!',
                        text: 'Both your email and phone have been verified successfully.',
                        icon: 'success',
                        confirmButtonText: 'Continue',
                        confirmButtonColor: '#4361ee'
                    });
                }
            }

            // Handle initial form submission to send OTP
            $('#initialForm').on('submit', function(e) {
                e.preventDefault();
                
                // Clear previous validation errors
                clearValidationErrors();
                
                // Get form data
                const formValues = $(this).serialize();
                
                // Show loading
                Swal.fire({
                    title: 'Sending Verification Codes...',
                    text: 'Please wait while we send verification codes to your email and phone',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request to Laravel backend
                $.ajax({
                    url: '/pre_register',
                    type: 'POST',
                    data: formValues,
                    success: function(response) {
                        // Update form data
                        formData.business_name = $('#businessName').val();
                        formData.email = $('#email').val();
                        formData.phone = $('#phone').val();
                        formData.address = $('#streetAddress').val();
                        formData.address2 = $('#streetAddress2').val();
                        formData.city_id = $('#city_id').val();
                        formData.state_id = $('#state_id').val();
                        formData.pincode = $('#postalCode').val();
                        formData.password = $('#password').val();
                        formData.password_confirmation = $('#password_confirmation').val();
                        
                        // Update verification form fields
                        $('#emailDisplay').text(formData.email);
                        $('#phoneDisplay').text(formData.phone);
                        $('#verificationEmail').val(formData.email);
                        $('#verificationPhone').val(formData.phone);
                        $('#finalEmail').val(formData.email);
                        $('#finalPhone').val(formData.phone);
                        
                        // Reset verification status
                        formData.isEmailVerified = false;
                        formData.isPhoneVerified = false;
                        $('#emailVerificationStatus').html('<i class="bi bi-clock me-1"></i> Pending').removeClass('verification-success').addClass('verification-pending');
                        $('#phoneVerificationStatus').html('<i class="bi bi-clock me-1"></i> Pending').removeClass('verification-success').addClass('verification-pending');
                        
                        // Move to verification step
                        currentStep = 2;
                        showCurrentForm();
                        
                        // Start resend timers
                        startResendTimer('email');
                        startResendTimer('phone');
                        
                        // Show success message
                        Swal.fire({
                            title: 'Verification Codes Sent!',
                            html: `We've sent verification codes to:<br>
                                  <strong>Email:</strong> ${formData.email}<br>
                                  <strong>Phone:</strong> ${formData.phone}`,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4361ee'
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            
                            // Display validation errors in the form
                            displayValidationErrors(errors);
                            
                            // Show validation errors in SweetAlert
                            Swal.fire({
                                title: 'Validation Error',
                                html: formatValidationErrorsForSwal(errors),
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4361ee'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Something went wrong. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4361ee'
                            });
                        }
                    }
                });
            });

            // Handle email OTP verification
            $('#emailOtpForm').on('submit', function(e) {
                e.preventDefault();
                
                // Clear previous validation errors
                clearValidationErrors();
                
                // Get form data
                const formValues = $(this).serialize();
                
                // Show loading
                Swal.fire({
                    title: 'Verifying Email...',
                    text: 'Please wait while we verify your email',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request to Laravel backend
                $.ajax({
                    url: '/seller/verify-otp',
                    type: 'POST',
                    data: formValues,
                    success: function(response) {
                        // Update verification status
                        formData.isEmailVerified = true;
                        $('#emailVerificationStatus').html('<i class="bi bi-check-circle me-1"></i> Verified').removeClass('verification-pending').addClass('verification-success');
                        
                        // Check if both email and phone are verified
                        checkVerificationStatus();
                        
                        // Show success message
                        Swal.fire({
                            title: 'Email Verified!',
                            text: 'Your email has been verified successfully',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4361ee'
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            
                            // Display validation errors in the form
                            const otpField = $('#emailOtp');
                            otpField.addClass('is-invalid');
                            otpField.after('<div class="invalid-feedback">' + errors.otp[0] + '</div>');
                            
                            // Show validation errors in SweetAlert
                            Swal.fire({
                                title: 'Invalid Code',
                                text: errors.otp[0],
                                icon: 'error',
                                confirmButtonText: 'Try Again',
                                confirmButtonColor: '#4361ee'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Verification Failed',
                                text: xhr.responseJSON?.message || 'The verification code is invalid or has expired.',
                                icon: 'error',
                                confirmButtonText: 'Try Again',
                                confirmButtonColor: '#4361ee'
                            });
                        }
                    }
                });
            });

            // Handle phone OTP verification
            $('#phoneOtpForm').on('submit', function(e) {
                e.preventDefault();
                
                // Clear previous validation errors
                clearValidationErrors();
                
                // Get form data
                const formValues = $(this).serialize();
                
                // Show loading
                Swal.fire({
                    title: 'Verifying Phone...',
                    text: 'Please wait while we verify your phone number',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request to Laravel backend
                $.ajax({
                    url: '/seller/verify-otp',
                    type: 'POST',
                    data: formValues,
                    success: function(response) {
                        // Update verification status
                        formData.isPhoneVerified = true;
                        $('#phoneVerificationStatus').html('<i class="bi bi-check-circle me-1"></i> Verified').removeClass('verification-pending').addClass('verification-success');
                        
                        // Check if both email and phone are verified
                        checkVerificationStatus();
                        
                        // Show success message
                        Swal.fire({
                            title: 'Phone Verified!',
                            text: 'Your phone number has been verified successfully',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4361ee'
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            
                            // Display validation errors in the form
                            const otpField = $('#phoneOtp');
                            otpField.addClass('is-invalid');
                            otpField.after('<div class="invalid-feedback">' + errors.otp[0] + '</div>');
                            
                            // Show validation errors in SweetAlert
                            Swal.fire({
                                title: 'Invalid Code',
                                text: errors.otp[0],
                                icon: 'error',
                                confirmButtonText: 'Try Again',
                                confirmButtonColor: '#4361ee'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Verification Failed',
                                text: xhr.responseJSON?.message || 'The verification code is invalid or has expired.',
                                icon: 'error',
                                confirmButtonText: 'Try Again',
                                confirmButtonColor: '#4361ee'
                            });
                        }
                    }
                });
            });

            // Handle resend email OTP
            $('#resendEmailOtp').on('click', function(e) {
                e.preventDefault();
                
                if ($(this).hasClass('disabled')) {
                    return;
                }
                
                // Show loading
                Swal.fire({
                    title: 'Resending Email Code...',
                    text: 'Please wait while we send a new verification code to your email',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request to Laravel backend
                $.ajax({
                    url: '/seller/resend-otp',
                    type: 'POST',
                    data: {
                        verification_method: 'email',
                        email: formData.email
                    },
                    success: function(response) {
                        // Start resend timer
                        startResendTimer('email');
                        
                        // Show success message
                        Swal.fire({
                            title: 'Code Resent!',
                            text: `We've sent a new verification code to ${formData.email}`,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4361ee'
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to resend verification code. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4361ee'
                        });
                    }
                });
            });

            // Handle resend phone OTP
            $('#resendPhoneOtp').on('click', function(e) {
                e.preventDefault();
                
                if ($(this).hasClass('disabled')) {
                    return;
                }
                
                // Show loading
                Swal.fire({
                    title: 'Resending SMS Code...',
                    text: 'Please wait while we send a new verification code to your phone',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request to Laravel backend
                $.ajax({
                    url: '/seller/resend-otp',
                    type: 'POST',
                    data: {
                        verification_method: 'phone',
                        phone: formData.phone
                    },
                    success: function(response) {
                        // Start resend timer
                        startResendTimer('phone');
                        
                        // Show success message
                        Swal.fire({
                            title: 'Code Resent!',
                            text: `We've sent a new verification code to ${formData.phone}`,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4361ee'
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to resend verification code. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4361ee'
                        });
                    }
                });
            });

            // Handle final form submission
            $('#registrationForm').on('submit', function(e) {
                e.preventDefault();
                
                // Show loading alert
                Swal.fire({
                    title: 'Processing Application...',
                    text: 'Please wait while we complete your registration',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request to Laravel backend
                $.ajax({
                    url: '/seller/complete-registration',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log('git rep',response)
                    if(response['success']){
                        Swal.fire({
                            title: 'Welcome Aboard!',
                            text: 'Your seller account has been created successfully',
                            icon: 'success',
                            confirmButtonText: 'Login to  Seller Dashboard',
                            confirmButtonColor: '#4361ee'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to seller dashboard
                                window.location.href = '/login';
                            }
                        });
                    }else{
                         Swal.fire({
                            title: 'Form Errors',
                            text: response['message'],
                            icon: 'error',
                            confirmButtonText: 'Try Again',
                            confirmButtonColor: '#4361ee'
                        });
                    }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Registration Failed',
                            text: xhr.responseJSON?.message || 'We couldn\'t complete your registration at this time',
                            icon: 'error',
                            confirmButtonText: 'Try Again',
                            confirmButtonColor: '#4361ee'
                        });
                    }
                });
            });

            // Handle back button in verification form
            $('#backToBusinessInfo').on('click', function() {
                currentStep = 1;
                showCurrentForm();
            });

            // Handle edit button in completion form
            $('#editBusinessInfo').on('click', function() {
                currentStep = 1;
                formData.isEmailVerified = false;
                formData.isPhoneVerified = false;
                showCurrentForm();
            });

            // Initialize the form
            showCurrentForm();
        });
    </script>
</body>

</html>