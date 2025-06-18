@extends('layouts.auth.auth_app')
@section('title')
    Login
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card mt-4">

                <div class="card-body p-4">
                    <div class="text-center mt-2">
                        <img src="https://colourindigo.com/logo.png" style="width:150px;height:55px;"/>
                       <p class="text-muted mt-2">Sign in to continue.</p>
                     
                    </div>
                    <div class="p-2 mt-4">
                        <x-alert/>
                        <div id="validation_errors"></div>
                        <form  data-module="Login" class="mb-3" action="{{ domain_route('login') }}" id="login_form" method="POST">

                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="username" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="Enter email">
                                </div>
                            </div>

                            <div class="mb-3">
                              <div class="float-end">
                                
                                    <a href="/forget-password" class="text-muted">Forgot password?</a>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="password-input">Password</label>
                                    <div class="position-relative auth-pass-inputgroup mb-3">

                                        <input type="password" name="password" class="form-control pe-5 password-input" id="password"
                                            placeholder="Enter password">
                                        <button
                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon"
                                            type="button" id="password-addon"><i
                                                class="ri-eye-fill align-middle"></i></button>
                                    </div>
                                </div>
                            </div>



                            <div class="mt-4">
                                <button class="btn btn-success w-100" type="submit" id="login_btn">Sign In</button>
                            </div>
                             <div class="mt-4 text-center">
                <p class="mb-0">Don't have an account ? <a href="/register"
                        class="fw-semibold text-primary text-decoration-underline"> Register here </a> </p>
            </div>
            

                            
                        </form>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
         

        </div>
    </div>
@endsection
