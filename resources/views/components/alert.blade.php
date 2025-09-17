@if($errors->any())
    {!! implode('', $errors->all('<div class="alert alert-danger">&#9888;&nbsp;&nbsp;:message</div>')) !!}
@endif
@if(\Session::has('success'))
<div class="alert alert-success alert-dismissible py-2 d-flex flex-row align-items-center" role="alert">
         
       <i class="fa fa-check-circle bi bi-check-circle-fill text-success fs-3 me-3 lh-1"></i>   
       <span class="fw-bolder text-success ">{{\Session::get('success')}}</span>
       
        </div>
 @endif
@if(\Session::has('error'))
<div class="alert alert-danger alert-dismissible py-2  d-flex flex-row align-items-center"" role="alert">
         <i class="fa fa-times-circle bi bi-x-circle-fill text-danger fs-3 me-3 lh-1"></i>
          <span class="fw-bolder text-danger ">{{\Session::get('error')}}</span>
         
        </div>
 @endif