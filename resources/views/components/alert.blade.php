@if($errors->any())
    {!! implode('', $errors->all('<div class="alert alert-danger">&#9888;&nbsp;&nbsp;:message</div>')) !!}
@endif
@if(\Session::has('success'))
<div class="alert alert-success alert-dismissible" role="alert">
         
          <span>{{\Session::get('success')}}</span>
       
        </div>
 @endif
@if(\Session::has('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
         
          <span>{{\Session::get('error')}}</span>
         
        </div>
 @endif