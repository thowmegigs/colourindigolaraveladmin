<style>
    .sidebar-wrapper {
        width: 250px;
        height: 100vh;
        background: #fff;
        border-right: 1px solid #e5e5e5;
        display: flex;
        flex-direction: column;
    }

    /* Clean Brand Section */
    .app-brand {
        padding: 25px 15px;
        background: #fff; /* clean white */
        border-bottom: 1px solid #f0f0f0;
        text-align: center;
    }

    .app-brand img {
        width: 110px;
        transition: transform 0.3s ease;
    }

    .app-brand img:hover {
        transform: scale(1.05);
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        margin-bottom: 4px;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        border-radius: 6px;
        position: relative;
        transition: all 0.3s ease;
        padding: 10px 12px;
    }

    .sidebar-menu > li > a:hover {
        background: #ba1654;
        color: white;
    }

    .sidebar-link.active {
        background: #ba1654;
        color: #fff;
        font-weight: 600;
    }

    .sidebar-link.active::after {
        content: "";
        position: absolute;
        right: -10px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-left: 10px solid #ba1654;
    }

    .sidebarMenuScroll1 {
        flex-grow: 1;
        overflow: hidden;
        padding-top: 10px;
    }
</style>
@php
$me=auth()->guard('vendor')->user(); 
@endphp
<nav id="sidebar" class="sidebar-wrapper">

    <!-- Brand -->
    <div class="app-brand p-0" >
        <a href="/dashboard" class="justify-content-center pt-3" data-turbolinks="true">
            <img src="https://colourindigo.com/images/logo.png" alt="Vendor Panel Logo" />
        </a>
    </div>

    <!-- Sidebar Menu -->
    <div class="sidebarMenuScroll1">
        <ul class="sidebar-menu list-unstyled px-2" style="margin: 0; height: 100%;">
            <li>
                <a href="/dashboard" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-laptop me-2"></i> Dashboard
                </a>
            </li>
            @if($me->is_verified=='Yes')
            <li>
                <a href="{{ domain_route('products.index') }}"  class="sidebar-link {{ request()->is('products*') ? 'active' : '' }}">
                    <i class="bi bi-box me-2"></i> Inventory
                </a>
            </li>
            @endif
            <li>
                <a href="{{ domain_route('vendor_orders') }}" class="sidebar-link {{ request()->is('vendor_orders*') ? 'active' : '' }}">
                    <i class="bi bi-shop-window me-2"></i> Orders
                </a>
            </li>
            <li>
                <a href="{{ domain_route('return_items.index') }}" class="sidebar-link {{ request()->is('return_items*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Manage Return/Exchange
                </a>
            </li>
            <li>
                <a href="/profile" class="sidebar-link {{ request()->is('profile') ? 'active' : '' }}">
                    <i class="bi bi-person me-2"></i> Account Settings
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.bank.list') }}" class="sidebar-link {{ request()->is('vendor/bank*') ? 'active' : '' }}">
                    <i class="bi bi-bank me-2"></i> Bank Details
                </a>
            </li>
            <li>
                <a href="/sales" class="sidebar-link {{ request()->is('sales') ? 'active' : '' }}">
                    <i class="bi bi-graph-up me-2"></i> Sales Report
                </a>
            </li>
            <li>
                <a href="/earning_settlement" class="sidebar-link {{ request()->is('earning_settlement') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack me-2"></i> Earning Settlement
                </a>
            </li>
            <li>
                <a href="/training_videos" class="sidebar-link {{ request()->is('training_videos') ? 'active' : '' }}">
                    <i class="bi bi-camera-video me-2"></i> Training Videos
                </a>
            </li>
            <li>
                <a href="/tickets" class="sidebar-link {{ request()->is('support') ? 'active' : '' }}">
                    <i class="bi bi-headphones me-2"></i> Support
                </a>
            </li>
            <li>
                <a href="/logout" class="sidebar-link {{ request()->is('logout') ? 'active' : '' }}">
                    <i class="bi bi-power me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>

</nav>
