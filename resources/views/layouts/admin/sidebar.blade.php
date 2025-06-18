 @php
$host = request()->getHost(); // returns 'admin.example.com'
$is_vendor=false;
if (str_contains($host, 'vendor')) {
   $is_vendor=true;
}
@endphp
<div class="app-menu navbar-menu " style="border:0">
            <!-- LOGO --> 
            <div class="navbar-brand-box " @if($is_vendor) style="background:#ffffff;height:69px" @endif>
                <!-- Dark Logo-->
                <a href="/" class="logo " style="font-family:fantasy;font-size:30px">
                 <img src="https://colourindigo.com/logo.png" style="width:100px;height:35px;margin:20px"/>
                     
                </a>
                <!-- Light Logo-->
               
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid"  @if($is_vendor) style="padding-top:30px" @endif>
                
                    <div id="two-column-menu">
                        
                    </div>
                     @include('layouts.admin.menu')
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
