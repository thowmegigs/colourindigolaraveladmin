@extends('layouts.admin.app')
@section('content')
@php
    $colors=\DB::table('colors')->get();
    $str='';
    foreach($colors as $cl){
    $str.=$cl->name.'=='.$cl->hexcode.',';
    }
    $str=rtrim($str,',');
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
                <h4 class="mb-sm-0">Create Product</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Ecommerce</a></li>
                        <li class="breadcrumb-item active">Create Product</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    {!! Form::open()->route(route_prefix().$plural_lowercase . '.store')->id(strtolower($module) .
    '_form')->multipart()->attrs(['data-module' => $module]) !!}

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="product-title-input">Product Title <span
                                    class="text-danger">*</span></label>

                            <input type="text" class="form-control" name="name" id="product-title-input" value=""
                                placeholder="Enter product title" required>

                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="product-title-input">Category <span
                                    class="text-danger">*</span></label>

                            <select class="form-select" onChange="fetchCategoryAttributes(this.value)" name="category_id" required>
                                {!! $category_options !!}
                            </select>

                        </div>

                      

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label" for="product-title-input">MRP<span
                                        class="text-danger">*</span></label>


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
                                        class="text-danger">*</span></label>

                                <div class="input-group has-validation mb-3">
                                    <span class="input-group-text" id="pr3oduct-price-addon">{{ getCurrency() }}</span>
                                    <input type="number" class="form-control" name="sale_price" id="product-price-input"
                                        placeholder="Enter price" aria-label="Price"
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
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Package Dimensions</div>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <label class="form-label" for="product-title-input">Length(CM)</label>

                            <input type="number" class="form-control" name="package_length"
                                placeholder="Enter package length" value="" required>

                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label" for="product-title-input">Width(CM) </label>

                            <input type="number" class="form-control" name="package_width"
                                placeholder="Enter package width" value="" required>

                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label" for="product-title-input">Height(CM) </label>

                            <input type="number" class="form-control" name="package_height"
                                placeholder="Enter package height" value="" required>

                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label" for="product-title-input">Weight(Kg) </label>

                            <input type="number" class="form-control" name="package_weight"
                                placeholder="Enter package weight in kg" value="" required>

                        </div>


                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Inventory</div>
                </div>
                <div class="card-body">

                    <div class="row">
                        {{-- <div class="col-md-6 mb-4">
                                <!-- Base Example -->
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="notify_out_of_stock"
                                        id="formCheck1">
                                    <label class="form-check-label" for="formCheck1">
                                       Notify When reached minimum limit
                                    </label>
                                </div>


                            </div>
                            <div class="col-md-6 mb-4">
                                <!-- Base Example -->
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="continue_selling"
                                        id="formCheck2">
                                    <label class="form-check-label text-grey" for="formCheck1">
                                       Continue Selling when out of stock
                                    </label>
                                </div>


                            </div> --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="product-title-input">SKU <span
                                    class="text-danger">*</span></label>

                            <input type="text" class="form-control" name="sku" required value=""
                                placeholder="Enter sku">

                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="product-title-input">Quantity in Stock <span
                                    class="text-danger">*</span></label>

                            <input type="number" class="form-control" name="quantity" required value=""
                                placeholder="Enter quantity in stock">

                        </div>
                        {{-- <div class="col-md-6 mb-4">
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
                            </div>--}}
                        <div class="col-lg-6 col-sm-6">
                            <div class="mb-3">
                                <label class="form-label" for="stocks-input">Max Purchase Quantity Allowed <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="max_quantity_allowed"
                                    placeholder="Enter Max Quantity" value="3" required>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">

                <div class="card-body">

                    <div class="form-group mb-3">
                        <label class="form-group">Short Description </label>

                        <textarea class="form-control" name="short_description"
                            placeholder="Must enter minimum of a 100 characters" rows="3"></textarea>

                    </div>
                    {{--<div class="d-flex  justify-content-between">
                            <div class="form-group w-50">
                                <label class="form-group">Features </label>

                                <textarea class="form-control" name="features" placeholder="Enter Product feratures or benefits" rows="3"></textarea>

                            </div>
                            <div class="form-group w-50">
                                <label class="form-group">Specifications </label>

                                <textarea class="form-control" name="specifications" placeholder="Enter Product specification" rows="3"></textarea>

                            </div>
                        </div>--}}

                    <div class="form-group">
                        <label class="form-group">Long Description</label>
                        <textarea class="form-control summernote" name="description" rows="10"></textarea>
                    </div>
                </div>
            </div>
            <!--variant -->

            <!--variant end-->
        </div>
        <div class="col-md-5">
            <div class="card mb-1">

                <div class="card-body">


                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="choices-publish-status-input" class="form-label">Status</label>

                            <select class="form-select" name="status" id="choices-publish-status-input" data-choices
                                data-choices-search-false>
                                <option value="Active" selected>Active</option>

                                <option value="In-Active">In-Active</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-12">
                          
                            <div class="form-check form-switch">
                                <input class="form-check-input" name="is_returnable" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
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
                                <input name="product_images[]" type="file" class="form-control" multiple="multiple">
                            </div>
                            <div class="form-group mb-3 mt-3">
                                <label class="form-label">Size Chart Image </label>
                                <input name="size_chart_image" type="file" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tags</label>
                                <input type="text" name="tags" value="" data-role="tagsinput" />
                            </div>




                            <!-- end card -->



                            <!-- end card -->

                        </div>
                    </div>
                    <!-- end card body -->
                </div>
            </div>
            <div class="card mb-1">

                <div class="card-body">


                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Add To Collection</label>
                                <select class="form-select" multiple id="choices-category-input" name="collections[]">
                                    @foreach($collections as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>





                            <!-- end card -->



                            <!-- end card -->

                        </div>
                    </div>
                    <!-- end card body -->
                </div>
            </div>
            <div class="card mb-1">
                <div class="card-header">SEO</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label" for="meta-title-input">Meta title</label>
                                <input type="text" name="meta_title" class="form-control" placeholder="Enter meta title"
                                    id="meta-title-input">
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
                                <textarea name="meta_description" class="form-control"
                                    placeholder="Enter meta description" id="meta-keywords-input"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="accordion" id="default-accordion-example">
                                <div class="accordion-item shadow">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        Addtional Settings
                                    </button>

                                </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#default-accordion-example" style="max-height:300px;overflow-y:auto;">
                                <div class="accordion-body">
                                      
                                   <div id="feature" ></div>
                                </div>
                            </div>
                         </div>
                   

              
            {{--
                <div class="card mb-4">

                    <div class="card-body">


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Addon Products</label>
                                    <select class="form-select" multiple 
                                        name="addon_products[]">
@foreach($addon_products as $g)
                                            <option value="{{ $g->id }}">{{ $g->name }}</option>
            @endforeach
            </select>
        </div>
        @if(count($repeating_group_inputs) > 0)
            @foreach($repeating_group_inputs as $grp)
                <x-repeatable :data="$grp['inputs']" :label="$grp['label']" values="" :index="$loop->index"
                    :disableButtons="$grp['disable_buttons']" :hide="$grp['hide']"
                    :indexWithModal="$grp['index_with_modal']"
                    :modalInputBoxIdWhoseValueToSetInSelect="$grp['modalInputBoxIdWhoseValueToSetInSelect']" />
            @endforeach
        @endif




    </div>
</div>

</div>
</div>--}}


<!--<div class="card">-->
<!--    <div class="card-header">-->
<!--        <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">-->
<!--            <li class="nav-item">-->
<!--                <a class="nav-link active" data-bs-toggle="tab" href="#tax_panel" role="tab">-->
<!--                    Tax-->
<!--                </a>-->
<!--            </li>-->
<!--            <li class="nav-item">-->
<!--                <a class="nav-link" data-bs-toggle="tab" href="#addproduct-metadata" role="tab">-->
<!--                    SEO-->
<!--                </a>-->
<!--            </li>-->

<!--        </ul>-->
<!--    </div>-->
<!-- end card header -->
<!--    <div class="card-body">-->
<!--        <div class="tab-content">-->

<!-- end tab-pane -->

<!--            <div class="tab-pane" id="addproduct-metadata" role="tabpanel">-->
<!--                <div class="row">-->
<!--                    <div class="col-lg-6">-->
<!--                        <div class="mb-3">-->
<!--                            <label class="form-label" for="meta-title-input">Meta title</label>-->
<!--                            <input type="text" name="meta_title" class="form-control"-->
<!--                                placeholder="Enter meta title" id="meta-title-input">-->
<!--                        </div>-->
<!--                    </div>-->
<!-- end col -->

<!--                    <div class="col-lg-6">-->
<!--                        <div class="mb-3">-->
<!--                            <label class="form-label" for="meta-keywords-input">Meta Keywords</label>-->
<!--                            <input type="text" name="meta_keywords "class="form-control"-->
<!--                                placeholder="Enter meta keywords" id="meta-keywords-input">-->
<!--                        </div>-->
<!--                    </div>-->
<!-- end col -->
<!--                </div>-->
<!-- end row -->

<!--                <div>-->
<!--                    <label class="form-label" for="meta-description-input">Meta Description</label>-->
<!--                    <textarea class="form-control" name="meta_descrpion" id="meta-description-input"-->
<!--                        placeholder="Enter meta description" rows="3"></textarea>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="tab-pane active" id="tax_panel" role="tabpanel">-->
<!--                <div class="row">-->
<!--                    <div class="col-lg-4">-->
<!--                        <div class="mb-3">-->
<!--                            <label class="form-label" for="meta-title-input">SGST(%)</label>-->
<!--                            <input type="number" name="sgst" class="form-control"-->
<!--                                placeholder="Enter sgst">-->
<!--                        </div>-->
<!--                    </div>-->
<!-- end col -->

<!--                    <div class="col-lg-4">-->
<!--                        <div class="mb-3">-->
<!--                            <label class="form-label" for="meta-keywords-input">CGST(%)</label>-->
<!--                            <input type="number" name="cgst" class="form-control"-->
<!--                                placeholder="Enter cgst">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        <div class="mb-3">-->
<!--                            <label class="form-label" for="meta-keywords-input">IGST(%)</label>-->
<!--                            <input type="number" name="igst" class="form-control"-->
<!--                                placeholder="Enter igst">-->
<!--                        </div>-->
<!--                    </div>-->
<!-- end col -->
<!--                </div>-->
<!-- end row -->


<!--            </div>-->
<!-- end tab pane -->
<!--        </div>-->
<!-- end tab content -->
<!--    </div>-->
<!-- end card body -->
<!--</div>-->

</div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">

            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check mb-2">
                            <input class="form-check-input" name="has_variant"
                                onchange="toggleDivDisplay(this.value,'var_c','Yes')" value="Yes" type="checkbox"
                                id="formCheck3">
                            <label class="form-check-label" for="formCheck1">
                                Product has variant?
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12" id="var_c">

                        <div class="d-none" id="copy">


                            <div class="row mb-4">

                                <div class="col-md-6 p">


                                </div>

                            </div>

                        </div>


                        <div id="variant_container" style="padding: 18px; 
                                margin-top: 25px; 
                              {{-- border: 1px solid #dbe9ec;"> --}}

                                        <div class=" d-flex justify-content-between">
                            <h5 class="card-title mb-0" style="font-size:13px;">Create Variants</h5>
                            <div class="hstack flex-wrap gap-2 pull-right">
                                <button class="btn btn-soft-dark btn-border " type="button"
                                    onclick="dynamicAddRemoveRowSimple('add','repeatable_container')">
                                    Add More</button>
                                <button class="btn btn-soft-danger btn-border" type="button"
                                    onclick="dynamicAddRemoveRowSimple('minus','repeatable_container')">
                                    Remove Row</button>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-12 mb-4" id="repeatable_container">


                                <div class="row mb-4" id="row-0">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Attribute</label>
                                            <select data-select='{!! $str!!}' class="form-control no-select2"
                                                name="attributes[]" onChange="show(this.value,event)">
                                                <option value="">Select Attributes</option>
                                                @foreach($attributes as $b)
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
                            <div class="col-md-12 mb-4">
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
    
function fetchCategoryAttributes(cat){
 $.ajax({
            url: "/category_attributes", // Or route('facet_attributes.update') for edit
            method: "POST",
            data: {id:cat},
            success: function (response) {
              
                $('#feature').html(response.message);
                $('#feature select').select2();
                
            },
            error: function (xhr) {
               console.error(xhr.responseJSON?.message || 'Something went wrong!')
               
            }
        });
    }
</script>
@endpush