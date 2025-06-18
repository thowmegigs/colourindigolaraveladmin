@php
    $cats = \App\Models\Category::with('children.children')->whereNull('category_id')->get();
    
    $is_two_level_category = false;
@endphp
 <style>
        /* Offcanvas styles */
        .offcanvas1 {
            position: fixed;
            top: 0;
            left: -250px; /* Hide offcanvas */
            width: 250px;height: 50px;
            height: 100%;
           background: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5);
            transition: left 0.3s ease;
            z-index: 1050;
        }

        .offcanvas1.open {
            left: 0; /* Show offcanvas */
        }

        .offcanvas-header {
            padding: 15px;
            background: #ff5a5f;
            color: #fff;height:51px;
        }
        .offcanvas1 .submenu {
            display: none; /* Initially hide submenu */
            padding-left: 15px; /* Indentation for submenu */
        }

        .offcanvas1 .nav-item {
            position: relative; /* For submenu position */
        }

        .offcanvas1 .nav-item .submenu-toggle {
            cursor: pointer; /* Change cursor for submenu toggle */
        }
    </style>

<div class="main-nav shadow-sm">

     <div class="offcanvas1" id="offcanvasMenu1">
    <div class="offcanvas-header">
        <h5 style="float:left">Menu</h5>
        <button type="button" class="close" id="menu-close">&times;</button>
    </div>
    <div class="offcanvas-body">
         <ul class="navbar-nav mr-auto main-nav-left">
                    <li class="nav-item">
                        <a class="nav-link" style="color:black" href="/">HOME</a>
                    </li>
                    @if ($cats->count() > 0)
                    @foreach ($cats as $cat)
                    <li class="nav-item dropdown mega-drop-main">
                        <a class="nav-link dropdown-toggle" style="color:black" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{strtoupper($cat->name)}}
                        </a>
                            @if ($cat->children->count() > 0)
                        
                            <div class="dropdown-menu mega-drop  shadow-sm border-0" aria-labelledby="navbarDropdown">
                                <div class="row ml-0 mr-0">
                                @foreach ($cat->children as $child)
                                    <div class="col-lg-2 col-md-2">
                                        <div class="mega-list">
                                        
                                            @if($child->children->count()>0)
                                            <a class="mega-title" style="color:black" href="product-grid.html">{{ $child->name }}</a>
                                            @foreach ($child->children as $sub_child)
                                            <a style="color:black" href="/products/{{$sub_child->slug}}">{{ $sub_child->name }}</a>
                                            @endforeach
                                            @else
                                            <a class="mega-title" href="/products/{{$child->slug}}">{{ $child->name }}</a>
                                        @endif
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                            @endif
                        
                    </li>
                    @endforeach 
                    @endif
                  
                   
                    <li class="nav-item">
                        <a class="nav-link" style="color:black" href="/contactus">CONTACT US </a>
                    </li>
                  
                </ul>
    </div>
</div>
    <nav class="navbar navbar-expand-md navbar-light bg-white pt-0 pb-0 sticky-top">
        <div class="container ">
            <a class="navbar-brand mx-auto mx-sm-start" href="/">
                <img src="{{ asset('logo.png') }}" class="img-fluid" alt="MJS FAshion" style="max-height:73px">
               
            </a>
            <a class="toggle" href="#">
                <span></span>
            </a>
             @if(\Auth::check())
                <a href="{{\URL::to('my-account')}}"  class="d-md-none nav-link" style="color:black;border:0">
                            <i class="icofont-ui-user"></i> {{\Str::limit('shashikant verma',5,'...')}}
                  </a>
              @endif
             
                 <a data-toggle="offcanvas" class="d-md-none nav-item cart-nav nav-link  d-none" href="#" x-data style="color:black;border:0">
                                <i class="icofont-basket"></i> Cart
                                <span class="badge badge-danger" x-text="$store.cart.items.length"></span>
                 </a>
            
                            
                       
            <button class="navbar-toggler d-none" type="button" id="menu-toggle1"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto main-nav-left">
                    <li class="nav-item topl">
                        <a class="nav-link" href="/">HOME</a>
                    </li>
                    @if ($cats->count() > 0)
                    @foreach ($cats as $cat)
                    <li class="nav-item dropdown mega-drop-main topl">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{strtoupper($cat->name)}}
                        </a>
                            @if ($cat->children->count() > 0)
                        
                            <div class="dropdown-menu mega-drop  shadow-sm border-0" aria-labelledby="navbarDropdown">
                                <div class="row ml-0 mr-0">
                                @foreach ($cat->children as $child)
                                    <div class="col-lg-2 col-md-2">
                                        <div class="mega-list">
                                        
                                            @if($child->children->count()>0)
                                            <a class="mega-title" href="product-grid.html">{{ $child->name }}</a>
                                            @foreach ($child->children as $sub_child)
                                            <a href="/products/{{$sub_child->slug}}">{{ $sub_child->name }}</a>
                                            @endforeach
                                            @else
                                            <a class="mega-title" href="/products/{{$child->slug}}">{{ $child->name }}</a>
                                        @endif
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                            @endif
                        
                    </li>
                    @endforeach 
                    @endif
                  
                   
                    <li class="nav-item topl">
                        <a class="nav-link" href="/contactus">CONTACT US </a>
                    </li>
                  
                </ul>
                <!-- <form class="form-inline my-2 my-lg-0 top-search">
                    <button class="btn-link" type="submit"><i class="icofont-search"></i></button>
                    <input class="form-control mr-sm-2" type="search"
                        placeholder="Search for products, brands and more" aria-label="Search">
                </form> -->
                <ul class="navbar-nav ml-auto profile-nav-right">
                    <li class="nav-item">
                        @if(!\Auth::check())
                        <a href="#" data-target="#login" data-toggle="modal" class="nav-link ml-0">
                            <i class="icofont-ui-user"></i> Login/Sign Up
                        </a>
                        @else
                          <a href="{{\URL::to('my-account')}}"  class="nav-link ml-0">
                            <i class="icofont-ui-user"></i> {{ucwords(\Auth::user()->name)}}
                        </a>
                        @endif
                    </li>
                    <li class="nav-item cart-nav" x-data>
                       
                             <a data-toggle="offcanvas" class="nav-link" href="#">
                            <i class="icofont-basket"></i> Cart
                            <span class="badge badge-danger" x-text="$store.cart.items.length"></span>
                        </a>
                            
                       
                       
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
