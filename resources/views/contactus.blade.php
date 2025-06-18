@extends('layouts.frontend.app')
@section('content')
<section class="py-5 bg-light border-top">
<div class="container">
<div class="row">
<div class="col-lg-4 col-md-4">
<div class="p-4 h-100 bg-white rounded overflow-hidden position-relative shadow-sm">
<h4 class="mt-0 mb-4 text-dark">Get In Touch</h4>
<h6 class="text-dark"><i class="icofont-location-pin pr-1"></i> Address :</h6>
<p class="pl-4">{{$setting->address}}</p>
<h6 class="text-dark"><i class="icofont-smart-phone pr-1"></i> Phone :</h6>
<p class="pl-4"> +91{{$setting->phone}}</p>

<h6 class="text-dark"><i class="icofont-email pr-1"></i> Email :</h6>
<p class="pl-4">{{$setting->email}}</p>

</div>
</div>
<div class="col-lg-4 col-md-4">
<div class="p-4 bg-white rounded overflow-hidden position-relative shadow-sm">
<h4 class="mt-0 mb-4 text-dark">Feedback</h4>
<form name="sentMessage" id="contactForm" novalidate>
<div class="control-group form-group">
<div class="controls">
<label>Full Name <span class="text-danger">*</span></label>
<input type="text" placeholder="Full Name" class="form-control" id="name" required data-validation-required-message="Please enter your name.">
<p class="help-block"></p>
</div>
</div>
<div class="row">
<div class="control-group form-group col-md-6">
<label>Phone Number <span class="text-danger">*</span></label>
<div class="controls">
<input type="tel" placeholder="Phone Number" class="form-control" id="phone" required data-validation-required-message="Please enter your phone number.">
</div>
</div>
<div class="control-group form-group col-md-6">
<div class="controls">
<label>Email Address <span class="text-danger">*</span></label>
<input type="email" placeholder="Email Address" class="form-control" id="email" required data-validation-required-message="Please enter your email address.">
</div>
</div>
</div>
<div class="control-group form-group">
<div class="controls">
<label>Message <span class="text-danger">*</span></label>
<textarea rows="4" cols="100" placeholder="Message" class="form-control" id="message" required data-validation-required-message="Please enter your message" maxlength="999" style="resize:none"></textarea>
</div>
</div>
<div id="success"></div>

<button type="submit" class="btn btn-primary btn-sm float-right">Send Message</button>
</form>
</div>
</div>
<div class="col-lg-4 col-md-4">
<div class="h-100 p-4 bg-white rounded overflow-hidden position-relative shadow-sm">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d57258.51125361129!2d72.97209534863282!3d26.2403338!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39418b0025d1c4f7%3A0x1b5089f0895f75ca!2sSHIV%20VIHAR%20COLONY!5e0!3m2!1sen!2sin!4v1729605999311!5m2!1sen!2sin" width="100%" frameborder="0" height="370" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
</div>
</div>
</div>
</section>
@endsection