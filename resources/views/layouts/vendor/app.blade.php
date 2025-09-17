<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Vendor Panel|Colour Indigo </title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Meta -->
    <meta name="description" content="Colour Indigo " />

        <link rel="shortcut icon" href="https://colourindigo.com/favicon-16x16.png">
    <link href="{{ asset('commonjs/select2/select2.css') }}" rel="stylesheet" type="text/css" />

    <!-- *************
   ************ CSS Files *************
  ************* -->
    <link rel="stylesheet" href="{{ asset('vendor_assets/fonts/bootstrap/bootstrap-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor_assets/css/main.min.css') }}" />
    <link href="{{ asset('commonjs/lightbox.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('commonjs/summernote.min.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{ asset('commonjs/bootstrap-tagsinput.css') }}" />
    <!-- *************
   ************ Vendor Css Files *************
  ************ -->
    <link href="{{ asset('assets/js/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css" />
     <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor_assets/vendor/overlay-scroll/OverlayScrollbars.min.css') }}" />
    <style>

        .flatpickr-calendar.open {
            display: inline-block;
            z-index: 10056;
        }
.flatpickr-weekdays,.flatpickr-months {
    background-color: #ba1654;
    text-align: center;font-weight:bold;color:white
}

        .flatpickr-rContainer {
            background: white !important;
        }

        .blockOverlay {
            z-index: 8000 !important;
        }

        .flatpickr-calendar.open {
            width: 238px !important;

        }

        .accordion-button:not(.collapsed) {
            color: black;
        }

        .accordion-button:focus {
            border-color: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .select2-search__field{

    border: 1px solid #cec8c8;
    border-radius: 6px;
        }
        .select2-container {
            border: 1px solid #c5c4caff;
            border-radius: 7px;
            padding: 5px 8px;
            height: 35px;
        }

        .input-group-text {
            border-radius: 0 !important;
            border-top-left-radius: 6px !important;
            border-bottom-left-radius: 6px !important;
        }
         .info-btn {
      background-color: #f8f9fa; /* light gray */
      border: 1px solid #ddd;
      border-radius: 50%;
      padding: 4px 8px;
      line-height: 1;
    }
    .info-btn:hover,
    .info-btn:focus {
      border: none;
      outline: none;
      background-color: #f8f9fa;
      box-shadow: none;
    }

    /* Tooltip: white background */
    .tooltip-inner {
      background-color: #fff;
      color: #000;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    .tooltip.bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow::before,
    .tooltip.bs-tooltip-auto[data-popper-placement^=bottom] .tooltip-arrow::before,
    .tooltip.bs-tooltip-auto[data-popper-placement^=left] .tooltip-arrow::before,
    .tooltip.bs-tooltip-auto[data-popper-placement^=right] .tooltip-arrow::before {
      border-top-color: #fff !important;
      border-bottom-color: #fff !important;
      border-left-color: #fff !important;
      border-right-color: #fff !important;
    }

        .form-check-input[type=checkbox] {

            width: 17px !important;
            height: 17px !important;
        }

        table th {
            font-size: 13px;
            font-weight: 500;
        }

        table td {
            font-size: 13px;
        }

        .btn-group>.btn:not(:last-child):not(.dropdown-toggle),
        .btn-group>.btn.dropdown-toggle-split:first-child,
        .btn-group>.btn-group:not(:last-child)>.btn {
            border-radius: 6px;

        }

        .select2-container--open {
            max-width: 250px !important;
        }

        /* Buttons */
        .btn:hover,
        .btn:focus,
        .btn:active,
        .btn:focus:active {
            outline: none !important;
            box-shadow: none !important;
            border-color: inherit !important;
        }

        /* Radios & Checkboxes */
        .form-check-input:hover,
        .form-check-input:focus,
        .form-check-input:active {
            outline: none !important;
            box-shadow: none !important;
            border-color: inherit !important;
        }

        /* Inputs, selects, and textareas */
        .form-control:hover,
        .form-control:focus,
        .form-control:active {
            outline: none !important;
            box-shadow: none !important;
            border-color: #777475ff !important;
        }

        .bootstrap-tagsinput .tag {
            margin-right: 2px;
            color: white;
            background: #4b4343;
            padding: 3px 6px;
            border-radius: 6px;
        }

        .select2-selection__choice {
            background: black;
            margin-inline: 3px;
            color: white;
        }

        .select2-container .select2-search--inline .select2-search__field {
            margin-top: 0px;

        }
    </style>
     <!-- <script src="https://cdn.jsdelivr.net/npm/turbolinks@5/dist/turbolinks.js"></script>
    <script>Turbolinks.start();</script> -->
</head>

<body>

    <!-- Page wrapper starts -->
    <div class="page-wrapper">

        <!-- Main container starts -->
        <div class="main-container">
            @include('layouts.vendor.sidebar')
            <!-- Sidebar wrapper starts -->
            <!-- Sidebar wrapper ends -->

            <!-- App container starts -->
            <div class="app-container">

                <!-- App header starts -->
                @include('layouts.vendor.header')
                <!-- App header ends -->

                <!-- App body starts -->
                @yield('content')
                <!-- App body ends -->

               
                <!-- App footer ends -->

            </div>
            <!-- App container ends -->

        </div>
        <!-- Main container ends -->

    </div>
    <!-- Page wrapper ends -->

    <!-- *************
   ************ JavaScript Files *************
  ************* -->
    <!-- Required jQuery first, then Bootstrap Bundle JS -->
    <script src="{{ asset('vendor_assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor_assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor_assets/js/moment.min.js') }}"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),

            }
        });
        var baseurl = window.location.origin;
    </script>
    <!-- *************
   ************ Vendor Js Files *************
  ************* -->

    <!-- Overlay Scroll JS -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- <script src="{{ asset('vendor_assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor_assets/vendor/overlay-scroll/custom-scrollbar.js') }}"></script>-->
    
    <script src="{{ asset('commonjs/lightbox.min.js') }}"></script> 
    <!-- Apex Charts -->
    <script src="{{ asset('vendor_assets/vendor/apex/apexcharts.min.js') }}"></script>
    <script src="{{ asset('commonjs/bootstrap-tagsinput.min.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('assets/js/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('commonjs/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('commonjs/select2/select2.js') }}"></script>
    <script src="{{ asset('vendor_assets/js/custom.js') }}"></script>
    <script src="{{ asset('commonjs/multiselect.min.js') }}"></script>
    <script src="{{ asset('assets/js/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('commonjs/block-ui.js') }}"></script>
    <script src="{{ asset('commonjs/spartan.js') }}"></script>
    <script src="{{ asset('commonjs/formvalidationcommon.js') }}?v=6"></script>
    <script src="{{ asset('commonjs/custom_form_validation.js') }}?v=1"></script>
    <script src="{{ asset('commonjs/commonjs_functions.js') }}?v=6"></script>
    <script src="{{ asset('commonjs/index_table_sort_pagination.js') }}?v=4"></script>
    <script src="{{ asset('commonjs/summernote.min.js') }}"></script>
    <script src="{{ asset('commonjs/repeatable_json_related.js') }}?v=4"></script>
    <script src="{{ asset('commonjs/file_input_related.js') }}?v=3"></script>
    <script src="{{ asset('commonjs/selection_related.js') }}?v=1"></script>
    <script src="{{ asset('commonjs/custom.js') }}?v=1"></script>
    @stack('scripts')
</body>

</html>
