@extends('layouts.frontend.app')
@section('content')


<section class="section pt-5 pb-5 osahan-not-found-page">
         <div class="container">
            <div class="row">
               <div class="col-md-12 text-center pt-5 pb-5">
                  <img class="img-fluid" src="{{asset('front_assets/img/thanks.png')}}" style="max-height:100px" alt="404">
                  <h1 class="mt-2 mb-2 text-success">Congratulations!</h1>
                  <p class="mb-5">You have successfully placed your order</p>
                  <a href="/my-account" id="pay_btn" class="btn btn-primary">Go to Order History </a>
               </div>
            </div>
         </div>
      </section>@endsection