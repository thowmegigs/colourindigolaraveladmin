@extends('layouts.vendor_auth.app')
@section('content')
<style>
  .focus-red:focus {
        outline: none !important;
        box-shadow: none !important;
        border: 1px solid #8B0000 !important; /* Deep Red */
    }</style>
<form action="{{ domain_route('ResetPasswordPost') }}" method="post">
    @csrf
    <!-- Include the token you send in the reset link -->
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="auth-box" style="max-width: 400px!important;">

        <!-- Logo -->
        <a href="/" class="text-center auth-logo mb-4 d-flex">
            <img src="https://colourindigo.com/logo.png" class="mx-auto" style="max-height:40px;" />
        </a>

        <!-- Heading -->
        <h5 class="mb-3">Reset Password</h4>
        <h6 class="fw-light mb-4">Enter your new password below to regain access to your account.</h6>

        <x-alert/>

        <!-- Email (Readonly for reference) -->
       
        <!-- New Password -->
        <div class="mb-3">
            <label class="form-label" for="password">New Password <span class="text-danger">*</span></label>
            <input type="password" id="password" name="password" class="focus-red form-control" 
                   placeholder="Enter new password" required>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label class="form-label" for="password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" 
                   class="focus-red form-control" placeholder="Re-enter new password" required>
        </div>

        <!-- Submit Button -->
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-danger">Reset Password</button>
        </div>

    </div>
</form>
@endsection
