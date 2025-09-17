@extends('layouts.vendor_auth.app')
@section('content')
<style>
  .focus-red:focus {
        outline: none !important;
        box-shadow: none !important;
        border: 1px solid #8B0000 !important; /* Deep Red */
    }</style>
<form action="{{ domain_route('ForgetPasswordPost') }}" method="post">
@csrf
    <div class="auth-box p-4" style="max-width: 400px; background: #fff;">

        <!-- Logo -->
        <div class="text-center mb-4">
            <a href="/" class="d-inline-block">
                <img src="https://colourindigo.com/logo.png" class="mx-auto" style="height: 45px;" alt="Logo"/>
            </a>
        </div>

        <!-- Heading -->
        <h5 class="mb-3 fw-bold text-center" style="color: #ba1654;">Forgot Password?</h4>
        <p class="text-muted text-center mb-4" style="font-size: 14px;">
            Enter the email address you registered with, and we will send you a link to reset your password.
        </p>

        <!-- Alerts -->
        <x-alert/>

        <!-- Email Field -->
        <div class="mb-3">
            <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
            <input type="email" id="email" name="email" class="focus-red form-control" placeholder="Enter your email">
        </div>

        <!-- Submit Button -->
        <div class="d-grid">
            <button type="submit" class="btn btn-danger">Send Reset Link</button>
        </div>

        <!-- Back to Login -->
        <div class="text-center mt-3">
            <a href="{{ domain_route('login') }}" class="text-decoration-none" style="color:#ba1654;">
                <i class="bi bi-arrow-left me-1"></i> Back to Login
            </a>
        </div>

    </div>
</form>
@endsection
