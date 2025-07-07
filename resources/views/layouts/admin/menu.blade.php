@php

    //dd(url()->full());
    $last_uri = str_contains(url()->full(), 'admin/') ? request()->segment(2) : request()->segment(1);

    $routes_arr = ['roles', 'categories', 'products', 'brands', 'attribute_famlies', 'product_discount_rules', 'combo_offers'];
@endphp
<ul class="navbar-nav" id="navbar-nav">
    <li class="nav-item">
        <a class="nav-link menu-link  @if ($last_uri == 'dashboard') active @endif" href="{{ url('dashboard') }}">
            <i class="mdi mdi-home"></i> <span data-key="t-widgets">Dashboard</span>
        </a>
    </li>
    @if (auth()->id())
        <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button"
                aria-expanded="false" aria-controls="sidebarDashboards">
                <i class="mdi mdi-speedometer"></i> <span data-key="t-dashboards">Master</span>
            </a>

            <div class="collapse menu-dropdown" id="sidebarDashboards">
                <ul class="nav nav-sm flex-column">


                    <li class="nav-item @if ($last_uri == 'users') active @endif">
                        <a href="{{ domain_route('users.index', ['role' => 'Customer']) }}" class="nav-link"
                            data-key="t-crm">
                            Manage
                            Customers </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ domain_route('vendors.index') }}" class="nav-link"
                            data-key="t-crm">
                            Manage
                            Vendors </a>
                    </li>
                   
                   
                       @if (auth()->user()->hasRole(['Admin']) || auth()->user()->can('list_attributes'))
                        <li class="nav-item @if ($last_uri == 'categories') active @endif">
                            <a href="{{ domain_route('categories.index') }}" class="nav-link">

                                <div data-i18n="Calendar">Manage Categories</div>
                            </a>
                        </li>
                    @endif
                    
                   
                       @if (auth()->user()->hasRole(['Admin']) || auth()->user()->can('list_attributes'))
                        <li class="nav-item @if ($last_uri == 'brands') active @endif">
                            <a href="{{ domain_route('collections.index') }}" class="nav-link">

                                <div data-i18n="Calendar">Manage Collections</div>
                            </a>
                        </li>
                    @endif
                       @if (auth()->user()->hasRole(['Admin']))
                        <li class="nav-item @if ($last_uri == 'brands') active @endif">
                            <a href="{{ domain_route('colors.index') }}" class="nav-link">

                                <div data-i18n="Calendar">Manage Colors</div>
                            </a>
                        </li>
                    @endif
                      @if (auth()->user()->hasRole(['Admin']) || auth()->user()->can('list_attributes'))
                        <li class="nav-item @if ($last_uri == 'brands') active @endif">
                            <a href="{{ domain_route('new_coupons.index') }}" class="nav-link">

                                <div data-i18n="Calendar">Manage Coupons</div>
                            </a>
                        </li>
                    @endif
                      @if (auth()->user()->hasRole(['Admin']) || auth()->user()->can('videos'))
                        <li class="nav-item @if ($last_uri == 'videos') active @endif">
                            <a href="{{ domain_route('videos.index') }}" class="nav-link">

                                <div data-i18n="Calendar">Manage Videos</div>
                            </a>
                        </li>
                    @endif
                      @if (auth()->user()->hasRole(['Admin']))
                        <li class="nav-item @if ($last_uri == 'website_sliders') active @endif">
                            <a href="{{ domain_route('website_sliders.index') }}" class="nav-link">

                                <div data-i18n="Calendar">Manage Sliders</div>
                            </a>
                        </li>
                    @endif
                      @if (auth()->user()->hasRole(['Admin']) || auth()->user()->can('list_attributes'))
                        <li class="nav-item @if ($last_uri == 'website_banners') active @endif">
                            <a href="{{ domain_route('website_banners.index') }}" class="nav-link">

                                <div data-i18n="Calendar">Manage Banners</div>
                            </a>
                        </li>
                    @endif
                    
                    
                    
                    

                </ul>
            </div>
        </li>
    @endif
    <li class="nav-item">
        <a class="nav-link menu-link  @if ($last_uri == 'payments') active @endif"
            href="{{ domain_route('products.index') }}">
            <i class="mdi mdi-package"></i> <span data-key="t-widgets">Products</span>
        </a>
    </li>
    @if(auth()->id())
       <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarDashboardsorders" data-bs-toggle="collapse" role="button"
                aria-expanded="false" aria-controls="sidebarDashboards">
                <i class="mdi mdi-package-variant"></i> <span data-key="t-dashboards">Orders</span>
            </a>

            <div class="collapse menu-dropdown" id="sidebarDashboardsorders">
                <ul class="nav nav-sm flex-column">
                    @if (auth()->user()->hasRole(['Admin']) || auth()->user()->can('list_settings'))
                      <li class="nav-item @if ($last_uri == 'settings') active @endif">
                        <a href="{{ domain_route('vendor_orders') }}" class="nav-link">

                            <div data-i18n="Calendar">Vendor Orders</div>
                        </a>
                    </li> 
                    @endif



                    @if (auth()->user()->hasRole(['Admin']) || auth()->user()->can('list_banners'))
                        <li class="nav-item @if ($last_uri == 'website_banners') active @endif">
                            <a href="{{ domain_route('orders.index') }}" class="nav-link">

                                <div data-i18n="Calendar">All Orders</div>
                            </a>
                        </li>
                    @endif
                     <li class="nav-item">
                            <a class="nav-link menu-link  @if ($last_uri == 'return_items') active @endif"
                                href="{{ domain_route('return_items.index') }}">
                            Return/Exchange Items
                            </a>
                    </li>
                     <li class="nav-item">
                    <a class="nav-link menu-link  @if ($last_uri == 'vendor_return_shipments') active @endif"
                        href="{{ domain_route('vendor_return_shipments') }}">
                       Return/Exchange Shipment 
                    </a>
                </li>
                   
                   



                </ul>
            </div>
        </li>
   
    @endif
    @if(!auth()->id())
       <li class="nav-item">
        <a class="nav-link menu-link"
            href="{{ domain_route('vendor_orders') }}">
            <i class="mdi mdi-package-variant"></i> <span data-key="t-widgets">Vendor Orders</span>
        </a>
    </li>
   
     <li class="nav-item">
        <a class="nav-link menu-link  @if ($last_uri == 'return_items') active @endif"
            href="{{ domain_route('return_items.index') }}">
            <i class="mdi mdi-refresh"></i> <span data-key="t-widgets">Return Orders</span>
        </a>
    </li>
    @endif
 

    @if(auth()->guard('vendor')->id())
    <li class="nav-item">
        <a class="nav-link menu-link"
            href="/profile">
            <i class="mdi mdi-lock"></i> <span data-key="t-widgets">Account Setting</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link menu-link"
            href="/bank">
            <i class="mdi mdi-bank"></i> <span data-key="t-widgets">Bank Details</span>
        </a>
    </li>
    <!-- <li class="nav-item">
        <a class="nav-link menu-link"
            href="/commission">
            <i class="mdi mdi-cash"></i> <span data-key="t-widgets">Earnings </span>
        </a>
    </li> -->
    <li class="nav-item">
        <a class="nav-link menu-link"
            href="/sales">
            <i class="mdi mdi-file-chart"></i> <span data-key="t-widgets">Items Sales Report </span>
        </a>
    </li>
    @endif
   
    @if (auth()->id())
        <li class="nav-item">
            <a class="nav-link menu-link  @if ($last_uri == 'payments') active @endif" href="/payments">
                <i class="mdi mdi-credit-card-outline"></i> <span data-key="t-widgets">Payments</span>
            </a>
        </li>
    @endif
   
   
    @if (auth()->id())
        <li class="nav-item">
            <a class="nav-link menu-link  @if ($last_uri == 'payments') active @endif" href="/website_content_sections">
                <i class="mdi mdi-credit-card-outline"></i> <span data-key="t-widgets">Website Front</span>
            </a>
        </li>
    @endif
    @if (auth()->id())
        <li class="nav-item">
            <a class="nav-link menu-link  @if ($last_uri == 'payments') active @endif" href="/content_sections">
                <i class="mdi mdi-credit-card-outline"></i> <span data-key="t-widgets">App Front</span>
            </a>
        </li>
    @endif
    
   
   
   
      
    @if (auth()->id())
    
        <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarDashboards3" data-bs-toggle="collapse" role="button"
                aria-expanded="false" aria-controls="sidebarDashboards">
                <i class="mdi mdi-cog-outline"></i> <span data-key="t-dashboards">Facet Attributes</span>
            </a>

            <div class="collapse menu-dropdown" id="sidebarDashboards3">
                <ul class="nav nav-sm flex-column">
                        <li class="nav-item @if ($last_uri == 'facet_attributes') active @endif">
                        <a href="{{ domain_route('facet_attributes.index') }}" class="nav-link">

                            <div data-i18n="Calendar">Manage Facet Attribute</div>
                        </a>
                    </li> 
                       <li class="nav-item @if ($last_uri == 'facet_attributes_values') active @endif">
                        <a href="{{ domain_route('facet_attributes_values.index') }}" class="nav-link">

                            <div data-i18n="Calendar"> Facet Attribute Values</div>
                        </a>
                    </li> 
                       <li class="nav-item @if ($last_uri == 'attribute_values_templates') active @endif">
                        <a href="{{ domain_route('attribute_values_templates.index') }}" class="nav-link">

                            <div data-i18n="Calendar"> Attribute Values Template</div>
                        </a>
                    </li> 
                    



                 


                </ul>
            </div>
        </li>
  
   
    
   
      


      
    @endif
   
   

   

</ul>


