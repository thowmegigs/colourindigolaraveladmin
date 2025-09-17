@extends('layouts.frontend.app')
@section('content')
<div class="page-head_agile_info_w3l">

	</div>
	<!-- //banner-2 -->
	<!-- page -->
	<div class="services-breadcrumb">
		<div class="agile_inner_breadcrumb">
			<div class="container">
				<ul class="w3_short">
					<li>
						<a href="/">Home</a>
						<i>|</i>
					</li>
					<li>About Us</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- //page -->
	<!-- about page -->
	<!-- welcome -->
	<div class="welcome">
		<div class="container">
			<!-- tittle heading -->
			<h3 class="tittle-w3l">Welcome to our Site
				<span class="heading-style">
					<i></i>
					<i></i>
					<i></i>
				</span>
			</h3>
			<!-- //tittle heading -->
			<div class="w3l-welcome-info">
				<div class="col-sm-6 col-xs-6 welcome-grids">
					<div class="welcome-img">
						<img src="{{asset('front/images/about.jpg')}}" class="img-responsive zoom-img" alt="">
					</div>
				</div>
				<div class="col-sm-6 col-xs-6 welcome-grids">
					<div class="welcome-img">
						<img src="{{asset('front/images/about2.jpg')}}" class="img-responsive zoom-img" alt="">
					</div>
				</div>
				<div class="clearfix"> </div>
			</div>
			<div class="w3l-welcome-text">
				<p>{{$setting->tagname}}({{$setting->company_name}}) is an online and offline grocery supermarket store that delivers daily use grocery products and gift items to customer.This online store is run by Online Mart And Grocery Hub  situated in 
				Ayodhya,Faizabad,UttarParadesh.
				</p>
				<p>Since our inception of offline store ,we have delivered best quality products to our customers .So we decided to provide our customer online facility to order products and have good experience</p>
			</div>
		</div>
	</div>
	<!-- //welcome -->
@endsection