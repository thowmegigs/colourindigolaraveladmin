<!Doctype html>
 <!-- @php
$host = request()->getHost(); // returns 'admin.example.com'
$is_vendor=false;
if (str_contains($host, 'vendor')) {
   $is_vendor=true;
}
@endphp -->
<!--
@if($is_vendor)
<html lang="en" data-layout="vertical" data-topbar="dark" data-sidebar="dark"
 data-sidebar-size="lg" data-sidebar-image="img-1" data-preloader="enable" data-sidebar-visibility="show" data-layout-style="default" data-layout-mode="light" 
 data-layout-width="fluid" data-layout-position="fixed">
@else

@endif -->
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="yes" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Colorindigo  {{auth()->id()?'Admin':'Seller Panel'}}   </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Colorindigo {{auth()->id()?'Admin':'Seller Panel'}}" name="description" />
   
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="https://colourindigo.com/favicon-16x16.png">

    <!-- Layout config Js -->
    <script src="{{asset('assets/js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('assets/css/custom.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('commonjs/lightbox.min.css')}}" rel="stylesheet" type="text/css" />
   
    <link href="{{asset('assets/js/flatpickr/flatpickr.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('commonjs/select2/select2.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('commonjs/jquery.filer.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('commonjs/summernote.min.css')}}" rel="stylesheet" type="text/css" />
    
    <link rel="stylesheet" href="{{ asset('commonjs/bootstrap-tagsinput.css') }}" />
<style>
    .note-editor .note-toolbar .note-btn i {
    color: black; /* your desired color */
}

/* Optional: Change hover color */
.note-editor .note-toolbar .note-btn:hover i {
    color: white;
}
 .blockOverlay{
    z-index:8000!important;
}
    .bootstrap-tagsinput .tag {
        background: #4b38b3;
    padding: 3px 7px;
    border-radius: 5px;
}
.select2-selection__choice__remove{
    top: 0px!important;
    right: -13px!important;
    opacity: 1!important;
    color: white!important;
}
.bootstrap-tagsinput  {
      
    padding: 8px 6px!important;
  
}
.flatpickr-rContainer{
    background:white!important;
}
.flatpickr-calendar.open{
    width:238px!important;

}
    .select2-container .select2-selection--single {
   
    height: 39px!important;
    }
    .select2-selection__choice{
        margin:5px;
    }
    .select2-results__option--highlighted {
    color: white!important;
    }
    .select2-selection__placeholder{
        color:grey!important;
    }
    .select2-results__option .select2-results__option--highlighted{
 color: black!important;
    }
    </style>
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

     {{-- @include('layouts.admin.topbar')--}}

    @include('layouts.admin.sidebar')
       
        <div class="vertical-overlay"></div>

        <div class="main-content">

            <div class="page-content py-3">
       @yield('content')
            </div>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading..</span>
            </div>
        </div>
    </div>

  @include('layouts.admin.footer')
</body>

</html>