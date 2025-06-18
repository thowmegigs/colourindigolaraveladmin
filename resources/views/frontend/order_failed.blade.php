@extends('layouts.frontend.app')
@section('content')

 <section class="section pt-5 pb-5 osahan-not-found-page">
         <div class="container">
            <div class="row">
               <div class="col-md-12 text-center pt-5 pb-5">
                  <img class="img-fluid" src="{{asset('front_assets/img/error.png')}}" style="max-height:200px" alt="404">
                  <h1 class="mt-2 mb-2 text-danger">Oops!</h1>
                  <h2 class="heading-2 mb-5">Sorry ,Failed to place order</h2>
                  <a  href="/checkout" id="pay_btn" class="btn btn-primary">Try Again </a>
               </div>
            </div>
         </div>
      </section>
@endsection
