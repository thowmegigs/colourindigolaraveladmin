
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Colourindigo</title>

    <!-- Meta -->
    <meta name="description" content="Colourindigo " />
   
     <link rel="shortcut icon" href="https://colourindigo.com/favicon-16x16.png">

    <!-- *************
			************ CSS Files *************
		************* -->
    <link rel="stylesheet" href="{{asset('vendor_assets/fonts/bootstrap/bootstrap-icons.css')}}" />
    <link rel="stylesheet" href="{{asset('vendor_assets/css/main.min.css')}}" />

  </head>

  <body>

    <!-- Auth wrapper starts -->
    <div class="auth-wrapper">

      <!-- Form starts -->
      @yield('content')  
	

    </div>
    <!-- Auth wrapper ends -->
  <script src="{{ asset('commonjs/jquery.min.js') }}"></script>
  <script src="{{ asset('commonjs/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('commonjs/selection_related.js') }}"></script>
    <script src="{{ asset('commonjs/formvalidationcommon.js') }}?v=2"></script>
    <script src="{{ asset('commonjs/custom_form_validation.js') }}"></script>
    <script src="{{ asset('commonjs/commonjs_functions.js') }}?v=1"></script>
    <script>
        $(document).ready(function () {
    if ($("form").length > 0) initializeFormAjaxSubmitAndValidation();
    
   
});

    </script>
  </body>

</html>