@extends('layouts.vendor.app')
@section('content')
    @php
        $colors = \DB::table('colors')->get();
        $str = '';
        foreach ($colors as $cl) {
            $str .= $cl->name . '==' . $cl->hexcode . ',';
        }
        $str = rtrim($str, ',');
    @endphp
    <style>
        .bootstrap-tagsinput {
            width: 100% !important;
        }

        .accordion-button {
            background: white !important;
            border: 1px solid #d5cccc !important;
        }

        #var_c {
            display: none;
        }
    </style>
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="my-3">Create Product</h4>



                </div>
            </div>
        </div>
        <!-- end page title -->
        {!! Form::open()->route(route_prefix() . $plural_lowercase . '.store')->id(strtolower($module) . '_form')->multipart()->attrs(['data-module' => $module]) !!}

        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="product-title-input">Product Title <span
                                        class="text-danger">*</span></label>

                                <input type="text" class="form-control" name="name" id="product-title-input"
                                    value="" placeholder="Enter product title" required>

                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="product-title-input">Category <span
                                        class="text-danger">*</span></label>

                                <select class="form-select" onChange="fetchCategoryAttributes(this.value)"
                                    name="category_id" required>
                                    {!! $category_options !!}
                                </select>

                            </div>



                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="product-title-input">MRP<span
                                            class="text-danger">*</span>
                                            <small class="text-muted ms-1" style="font-size:11px">(Lowest Price of all variants)</small></label>


                                    <div class="input-group has-validation mb-3">
                                        <span class="input-group-text" id="produect-price-addon">{{ getCurrency() }}</span>
                                        <input required type="number" class="form-control" name="price"
                                            id="product-price-input" placeholder="Enter price" aria-label="Price"
                                            aria-describedby="product-price-addon" required>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="product-title-input">Selling Price<span
                                            class="text-danger">*</span> <small class="text-muted ms-1" style="font-size:11px">(Lowest sale price of all variants)</small></label>

                                    <div class="input-group has-validation mb-3">
                                        <span class="input-group-text" id="pr3oduct-price-addon">{{ getCurrency() }}</span>
                                        <input type="number" class="form-control" name="sale_price"
                                            id="product-price-input" placeholder="Enter price" aria-label="Price"
                                            aria-describedby="product-price-addon" required>

                                    </div>

                                </div>
                            </div>
                            {{--   <div class="col-md-6 mb-3">
                                <div class="form-group ">
                                    <label class="form-label" for="product-title-input">Discount Type</label>

                                    <select class="form-control " name="discount_type">
                                        <option value="">Select Discount Type</option>
                                        <option value="Flat">Flat</option>
                                        <option value="Percent">Percentage</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="product-title-input">Discount</label>

                                    <input type="number" class="form-control" name="discount" id="product-price-input"
                                        placeholder="Enter discount value" aria-label="Price"
                                        aria-describedby="product-price-addon">

                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="product-title-input">Package Weight</label>
                                    <div class="d-flex">
                                        <input type="text" class="form-control"
                                            style="border-right:0!important; border-top-right-radius:0;border-bottom-right-radius:0;"
                                            name="package_weight" id="product-price-input" placeholder="Ex. Kg,g,"
                                            aria-label="Price" aria-describedby="product-price-addon">
                                        <select class="form-select " name="package_weight_unit"
                                            style="width:40%;border: 1.5px solid #d0dcdd!important; border-top-left-radius:0;border-bottom-left-radius:0;">
                                            <option>Kg</option>
                                            <option>g</option>
                                        </select>
                                    </div>


                                </div>
                            </div>
 --}}

                        </div>
                    </div>
                </div>
                <!-- <div class="card">
                        <div class="card-header">
                            <div class="card-title">Package Dimensions</div>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="product-title-input">Length(CM)</label>

                                    <input type="number" class="form-control" name="package_length"
                                        placeholder="Enter package length" value="" required>

                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="product-title-input">Width(CM) </label>

                                    <input type="number" class="form-control" name="package_width"
                                        placeholder="Enter package width" value="" required>

                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="product-title-input">Height(CM) </label>

                                    <input type="number" class="form-control" name="package_height"
                                        placeholder="Enter package height" value="" required>

                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="product-title-input">Weight(Kg) </label>

                                    <input type="number" class="form-control" name="package_weight"
                                        placeholder="Enter package weight in kg" value="" required>

                                </div>


                            </div>
                        </div>
                    </div> -->
                <div class="card my-2">
                    <div class="card-header mb-0 pb-0">
                        <h5 class="card-title">Inventory</h5>
                    </div>
                    <div class="card-body py-2">

                        <div class="row">
                            {{-- <div class="col-md-6 mb-3">
                                <!-- Base Example -->
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="notify_out_of_stock"
                                        id="formCheck1">
                                    <label class="form-check-label" for="formCheck1">
                                       Notify When reached minimum limit
                                    </label>
                                </div>


                            </div>
                            <div class="col-md-6 mb-3 ">
                                <!-- Base Example -->
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="continue_selling"
                                        id="formCheck2">
                                    <label class="form-check-label text-grey" for="formCheck1">
                                       Continue Selling when out of stock
                                    </label>
                                </div>


                            </div> --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="product-title-input">SKU <span
                                        class="text-danger">*</span><small class="text-muted">(Same for all only size variants)</small></label>

                                <input type="text" id="inp-sku" class="form-control" name="sku" required value=""
                                    placeholder="Enter sku">

                            </div>
                            <div class="col-md-6 mb-3 d-none">
                                <label class="form-label" for="product-title-input">Quantity in Stock <span
                                        class="text-danger">*</span></label>

                                <input type="hidden" class="form-control" name="quantity" required value="0"
                                    placeholder="Enter quantity in stock">

                            </div>
                            {{-- <div class="col-md-6 mb-3">
                                <label class="form-label" for="product-title-input">Minimum Quantity to notify</label>

                                <input type="number" class="form-control" name="minimum_qty_alert" value=""
                                    placeholder="Enter miniumum quantity">

                            </div> 
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="product-title-input">Per Quantity of <span
                                            class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <input type="number" class="form-control"
                                            style="border-right:0!important; border-top-right-radius:0;border-bottom-right-radius:0;"
                                            name="per_quantity_of" id="product-price-input" placeholder="Enter quantity "
                                            aria-label="Price" aria-describedby="product-price-addon">
                                        <select class="form-select no-select2" name="per_unit" required
                                            style="width:40%;border: 1.5px solid #d0dcdd!important;
                                             border-top-left-radius:0;border-bottom-left-radius:0;border-top-right-radius:0;border-bottom-right-radius:0;">
                                            <option value="Kg">Kg</option>
                                            <option value="g">g</option>
                                            <option value="L">L</option>
                                            <option value="ml">ml</option>
                                        </select>
                                        <input type="number" name="per_price" class="form-control"
                                            style="border-left:0;border-top-left-radius:0;border-bottom-left-radius:0"
                                            name="per_price" value="" placeholder="Enter price ">
                                    </div>


                                </div>
                            </div> --}}
                            <div class="col-lg-6 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label" for="stocks-input">Max Purchase Quantity Allowed <span
                                            class="text-danger">*</span></label>
                                    <input type="number" id="max_qty" class="form-control" name="max_quantity_allowed"
                                        placeholder="Enter Max Quantity" value="3" required>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">

                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Product Description</label>
                            <textarea class="form-control" name="description" rows="10" placeholder="Enter description about product"></textarea>
                        </div>
                    </div>
                </div>
                <!--variant -->

                <!--variant end-->
            </div>
            
            <div class="col-md-5">
                  <div class="accordion" id="default-accordion-example">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                Product Features
                            </button>

                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#default-accordion-example" style="max-height:300px;overflow-y:auto;">
                            <div class="accordion-body">

                                <div id="feature">
                                    <small class="text-muted">Feature appears here based on selected Category</small>
                                </div>
                            </div>
                        </div>
                    </div>



                  

                </div>
                <div class="card my-2">

                    <div class="card-body">


                        <div class="row">
                            
                           
                            <div class="mb-3 col-md-12">

                                <div class="form-check form-switch">
                                    <input class="form-check-input" name="is_returnable" type="checkbox" role="switch"
                                        id="flexSwitchCheckChecked" checked>
                                    <label class="form-check-label" for="flexSwitchCheckChecked">Is Returnable?</label>
                                </div>
                            </div>

                            {{--  <div class="mb-3 col-md-12">
                                <label for="choices-publish-visibility-input" class="form-label">Visibility</label>
                                <select class="form-select no-select2" name="visibility"
                                    id="choices-publish-visibility-input" data-choices data-choices-search-false>
                                    <option value="Public" selected>Public</option>
                                    <option value="Hidden">Hidden</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-12 ps-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"  name="is_combo" role="switch" value="Yes" id="flexSwitchCheckChecked" >
                                    <label class="form-check-label" for="flexSwitchCheckChecked">Combo Offer</label>
                                </div>
                            </div> --}}
                            {{-- <div class="mb-3 col-md-12 ps-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"  name="featured" role="switch" value="Yes" id="flexSwitchCheckChecked" >
                                    <label class="form-check-label" for="flexSwitchCheckChecked">Featured</label>
                                </div>
                            </div> --}}

                        </div>
                        <!-- end card body -->
                    </div>
                </div>

                <div class="card mb-1">

                    <div class="card-body">


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Main Image <span class="text-danger">*</span></label>
                                    <input name="image" type="file" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Gallery(<small>Multiple Images</small>)</label>
                                    <input name="product_images[]" type="file" class="form-control"
                                        multiple="multiple">
                                </div>
                                <div class="form-group mb-3 mt-3">
                                    <label class="form-label">Size Chart Image </label>
                                    <input name="size_chart_image" type="file" class="form-control">
                                </div>
                             


                            </div>
                        </div>
                        <!-- end card body -->
                    </div>
                </div>
             
               
                <!-- <div class="card mt-1">
                    <h6 class="card-header">SEO</h6>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label" for="meta-title-input">Meta title</label>
                                    <input type="text" name="meta_title" class="form-control"
                                        placeholder="Enter meta title" id="meta-title-input">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label" for="meta-keywords-input">Meta Keywords</label>
                                    <input type="text" name="meta_keywords" class="form-control"
                                        placeholder="Enter meta keywords" id="meta-keywords-input">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label" for="meta-keywords-input">Meta Description</label>
                                    <textarea name="meta_description" class="form-control" placeholder="Enter meta description"
                                        id="meta-keywords-input"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div> -->
               
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">

                        <div class="card-body">


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" name="has_variant"
                                            onchange="toggleDivDisplay(this.value,'var_c','Yes')" value="Yes"
                                            type="checkbox" id="formCheck3">
                                        <label class="form-check-label" for="formCheck1">
                                            Product has variant?
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12" id="var_c">

                                    <div class="d-none" id="copy">


                                        <div class="row mb-3">

                                            <div class="col-md-6 p">


                                            </div>

                                        </div>

                                    </div>


                                    <div id="variant_container" style="padding: 13px;">

                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title mb-2" style="font-size:13px;">Create Variants</h5>
                                            <div class="hstack flex-wrap gap-2 pull-right">
                                                <button class="btn btn-dark btn-sm rounded-sm " type="button"
                                                    onclick="dynamicAddRemoveRowSimple('add','repeatable_container')">
                                                    Add More</button>
                                                <button class="btn btn-danger btn-sm rounded-sm" type="button"
                                                    onclick="dynamicAddRemoveRowSimple('minus','repeatable_container')">
                                                    Remove Row</button>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-12 mb-3" id="repeatable_container">


                                                <div class="row mb-3" id="row-0">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Attribute</label>
                                                            <select data-select='{!! $str !!}'
                                                                class="form-control no-select2" name="attributes[]"
                                                                onChange="show(this.value,event)">
                                                                <option value="">Select Attributes</option>
                                                                @foreach ($attributes as $b)
                                                                    <option value="{{ $b->id }}">
                                                                        {{ $b->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 p">


                                                    </div>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="accordion" id="accord">

                                                </div>
                                            </div>

                                        </div>





                                    </div>

                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                    </div>

                </div>
                <div class="text-end mb-3 mt-3">
                    <button type="submit" class="btn btn-success w-sm" id="product_btn">Submit</button>
                </div>
            </div>
            <!-- end col -->


            </form>




        </div>
    @endsection
    @push('scripts')
        <script>
            function fetchCategoryAttributes(cat) {
                $.ajax({
                    url: "/category_attributes", // Or route('facet_attributes.update') for edit
                    method: "POST",
                    data: {
                        id: cat
                    },
                    success: function(response) {

                        $('#feature').html(response.message);
                        $('#feature select').select2();

                    },
                    error: function(xhr) {
                        console.error(xhr.responseJSON?.message || 'Something went wrong!')

                    }
                });
            }
        </script>
    @endpush
