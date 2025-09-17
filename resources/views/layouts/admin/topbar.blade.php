<header id="page-topbar">
  <div class="layout-width">
      <div class="navbar-header">
          <div class="d-flex">
              <!-- LOGO -->
              <div class="navbar-brand-box horizontal-logo">
                  <a href="index-2.html" class="logo logo-dark">
                      <span class="logo-sm">
                          <img src="{{asset('assets/images/logo-sm.png')}}" alt="" height="22">
                      </span>
                      <span class="logo-lg">
                          <img src="{{asset('assets/images/logo-dark.png')}}" alt="" height="17">
                      </span>
                  </a>

                  <a href="index-2.html" class="logo logo-light">
                      <span class="logo-sm">
                          <img src="{{asset('assets/images/logo-sm.png')}}" alt="" height="22">
                      </span>
                      <span class="logo-lg">
                          <img src="{{asset('assets/images/logo-light.png')}}" alt="" height="17">
                      </span>
                  </a>
              </div>

              <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none" id="topnav-hamburger-icon">
                  <span class="hamburger-icon">
                      <span></span>
                      <span></span>
                      <span></span>
                  </span>
              </button>

              <!-- App Search-->
           
          </div>

          <div class="d-flex align-items-center">

           

             

              <div class="dropdown ms-sm-3 header-item topbar-user">
                  <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="d-flex align-items-center">
                          <img class="rounded-circle header-profile-user" src="{{asset('assets/images/users/avatar-1.jpg')}}" alt="Header Avatar">
                          <span class="text-start ms-xl-2">
                              <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{auth()->id()?'Admin':ucwords(auth()->guard('vendor')->user()->name)}}</span>
                              <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">Owner</span>
                          </span>
                      </span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">
                      <!-- item-->
                   <a class="dropdown-item" href="/profile"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Profile</span></a>
                   <a class="dropdown-item" href="/logout"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                  </div>
              </div>
          </div>
      </div>
  </div>
</header>