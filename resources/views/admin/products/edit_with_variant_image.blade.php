@extends('layouts.admin.app')
@section('content')
 <style>
        .bootstrap-tagsinput {
            width: 100% !important;
        }

        .accordion-button {
            background: white !important;
            border: 1px solid #d5cccc !important;
        }

        #var_c1 {
            display: none;
        }

        .image_place .del_ic {
            position: absolute;
            top: -15px;
            right: -3px;
            z-index: 9999;
            cursor: pointer;
        }

        .image_place {
            position: relative;
            width: 80px;
            height: 80px;
            margin-top: 10px;
        }
</style>
@php 
    $colors=\DB::table('colors')->get();
    $str='';$str1='';
    foreach($colors as $cl){
        $str.=$cl->name.'=='.$cl->hexcode.',';
    $str1.="<option value='".$cl->name."'>".$cl->name."</option>";
    }
    $str=rtrim($str,',');
@endphp
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
        {!! Form::open()->put()->route(route_prefix().$plural_lowercase . '.update', [\Str::singular($plural_lowercase) => $model->id])->id(strtolower($module) . '_form')->attrs(['data-module' => $module]) !!}

        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <input type="hidden" value="{{ $model->id }}" id="model_id" />
                                <label class="form-label" for="product-title-input">Product Title <span
                                        class="text-danger">*</span></label>

                                <input type="text" class="form-control" name="name" value="{{ $model->name }}"
                                    id="product-title-input" value="" placeholder="Enter product title" required>

                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="product-title-input">Category <span
                                        class="text-danger">*</span></label>

                                <select class="form-select" id="choices-category-input" onChange="fetchCategoryAttributes(this.value,'{!! $model->id!!}')" name="category_id" required>
                                    {!! $category_options !!}
                                </select>

                            </div>

                          

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="product-title-input">MRP<span
                                            class="text-danger">*</span></label>


                                    <div class="input-group has-validation mb-3">
                                        <span class="input-group-text" id="produect-price-addon">{{ getCurrency() }}</span>
                                        <input required type="number" class="form-control" value="{{ $model->price }}"
                                            name="price" id="product-price-input" placeholder="Enter price"
                                            aria-label="Price" aria-describedby="product-price-addon">

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="product-title-input">Sale Price <span
                                            class="text-danger">*</span></label>

                                    <div class="input-group has-validation mb-3">
                                        <span class="input-group-text" id="pr3oduct-price-addon">{{ getCurrency() }}</span>
                                        <input type="number" class="form-control" name="sale_price"
                                            id="product-price-input" placeholder="Enter sale price" aria-label="Price"
                                            aria-describedby="product-price-addon" value="{{ $model->sale_price }}">

                                    </div>

                                </div>
                            </div>





                        </div>
                    </div>
                </div>
                <!-- <div class="card">
                    <div class="card-header">
                        <div class="card-title">Package Dimensions</div>
                    </div>
                    <div class="card-body">
                        @php 
                        $dimesnion=json_decode($model->package_dimension,true);
                        @endphp
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <label class="form-label" for="product-title-input">Length(CM)</label>

                                <input type="number" class="form-control" name="package_length" 
                                    placeholder="Enter package length" value="{{ $dimesnion['length']??'' }}">

                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="form-label" for="product-title-input">Width(CM) </label>

                                <input type="number" class="form-control" name="package_width" 
                                    placeholder="Enter package width" value="{{  $dimesnion['width']??'' }}">

                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="form-label" for="product-title-input">Height(CM) </label>

                                <input type="number" class="form-control" name="package_height" 
                                    placeholder="Enter package height" value="{{  $dimesnion['height']??'' }}">

                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="form-label" for="product-title-input">Weight(Kg.) </label>

                                <input type="number" class="form-control" name="package_weight" 
                                    placeholder="Enter package weight" value="{{  $dimesnion['weight']??'' }}">

                            </div>


                        </div>
                    </div>
                </div> -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Inventory</div>
                    </div>
                    <div class="card-body">

                        <div class="row">
                               <div class="col-md-6 mb-4">
                            <label class="form-label" for="product-title-input">SKU <span
                                    class="text-danger">*</span></label>

                            <input type="text" class="form-control" name="sku" required value="{{ $model->sku }}"
                                placeholder="Enter sku">

                        </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="product-title-input">Quantity in Stock <span
                                        class="text-danger">*</span></label>

                                <input type="number" class="form-control" name="quantity" required
                                    placeholder="Enter quantity in stock" value="{{ $model->quantity }}">

                            </div>


                            <div class="col-lg-6 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label" for="stocks-input">Max Purchase Quantity
                                        Allowed<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control"
                                        value="{{ $model->max_quantity_allowed }}" name="max_quantity_allowed"
                                        placeholder="Enter Max Quantity" required>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">

                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-group">Short Description </label>

                            <textarea 
                                class="form-control" " name="short_description" placeholder="Must enter minimum of a 100 characters" rows="3">{{ $model->short_description }}</textarea>

                        </div>

                        <div class="form-group">
                            <label class="form-group">Long Description</label>
                            <textarea class="form-control" name="description" rows="10">{!! $model->description !!}</textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-5">
                <div class="card mb-1">

                    <div class="card-body">


                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="choices-publish-status-input" class="form-label">Status</label>

                                <select class="form-select no-select2" name="status" id="choices-publish-status-input"
                                    data-choices data-choices-search-false>
                                    <option value="Active" @if ($model->status == 'Active') selected @endif>
                                        Active</option>

                                    <option value="In-Active" @if ($model->status == 'In-Active') selected @endif>
                                        In-Active</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-12">
                          
                            <div class="form-check form-switch">
                                <input class="form-check-input" name="is_returnable" 
                                type="checkbox" role="switch" id="flexSwitchCheckChecked"\
                                 @if($model->is_return_eligible=='Yes') checked @endif>
                                <label class="form-check-label" for="flexSwitchCheckChecked">Is Returnable?</label>
                            </div>
                        </div>

                            <div class="mb-3 col-md-12">
                                <label for="choices-publish-visibility-input" class="form-label">Visibility</label>
                                <select class="form-select no-select2" name="visibility"
                                    id="choices-publish-visibility-input" data-choices data-choices-search-false>
                                    <option value="Public" @if ($model->visibility == 'Public') selected @endif>
                                        Public</option>
                                    <option value="Hidden" @if ($model->visibility == 'Hidden') selected @endif>
                                        Hidden</option>
                                </select>
                            </div>
                            {{-- <div class="mb-3 col-md-12 ps-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_combo" role="switch"
                                        value="Yes" id="flexSwitchCheckChecked" " @if ($model->is_combo == 'Yes') checked @endif>
                                            <label class="form-check-label" for="flexSwitchCheckChecked">Combo Offer</label>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-12 ps-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"  name="featured"
                                            role="switch" value="Yes" id="flexSwitchCheckChecked" @if ($model->featured == 'Yes') checked @endif>
                                            <label class="form-check-label" for="flexSwitchCheckChecked">Featured</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div> --}}
                        </div>

                    </div>


                </div>
                <div class="card mb-1">

                    <div class="card-body">


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Main Image <span class="text-danger">*</span></label>
                                    <input name="image" type="file" class="form-control">
                                </div>
                                @if ($model->image)
                                    <div class="image_place" id="img_div">
                                        <i class="bx bx-trash text-danger del_ic"
                                            onClick="deleteFileSelf('{!! $model->image !!}', 'Product', 'products/{!! $model->id !!}',
                'image','{!! $model->id !!}')"></i>
                                        <a href="{{ asset('storage/products/' . $model->id . '/' . $model->image) }}"
                                            data-lightbox="image-1">
                                            <img src="{{ asset('storage/products/' . $model->id . '/' . $model->image) }}"
                                                style="width:100%;height:100%;object-fit:fit" />

                                        </a>

                                    </div>
                                @endif
                                <div class="form-group mt-3">
                                    <label class="form-label">Gallery(<small>Multiple Images</small>)</label>
                                    <input name="product_images" type="file" class="form-control"
                                        multiple="multiple">
                                </div>
                                @if (count($model->images) > 0)
                                    <div class="hstack gap-2 mt-3">
                                        @foreach ($model->images as $img)
                                            <div class="image_place" id="img_div-{{ $img->id }}">
                                                <i class="bx bx-trash text-danger del_ic"
                                                    onclick="deleteFileFromTable('{!! $img->id !!}', 'product_images', 'products/{!! $model->id !!}', '{!! route('deleteTableFile') !!}')"></i>
                                                <a href="{{ asset('storage/products/' . $model->id . '/' . $img->name) }}"
                                                    data-lightbox="image-1">

                                                    <img src="{{ asset('storage/products/' . $model->id . '/' . $img->name) }}"
                                                        style="width:100%;height:100%;object-fit:fit" />
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif




                                <div class="form-group mt-3 mb-3">
                                    <label class="form-label">Size Chart Image </label>
                                    <input name="size_chart_image" type="file" class="form-control">
                                </div>
                                @if ($model->size_chart_image)
                                    <div class="image_place" id="img_divd">
                                        <i class="bx bx-trash text-danger del_ic"
                                            onClick="deleteFileSelf('{!! $model->size_chart_image !!}', 'Product', 'products/{!! $model->id !!}',
                'size_chart_image','{!! $model->id !!}')"></i>
                                        <a href="{{ asset('storage/products/' . $model->id . '/' . $model->size_chart_image) }}"
                                            data-lightbox="image-3">
                                            <img src="{{ asset('storage/products/' . $model->id . '/' . $model->size_chart_image) }}"
                                                style="width:100%;height:100%;object-fit:fit" />

                                        </a>

                                    </div>
                                @endif

                            </div>
                        </div>

                    </div>



                </div>
                <div class="card mb-1">

                    <div class="card-body">

                        <div class="form-group">
                            <label class="form-label">Tags</label>
                            <input type="text" name="tags" value="{{$model->tags}}" data-role="tagsinput" />
                        </div>


                    </div>

                </div>




                <div class="card mb-1">
                    <div class="card-header">SEO</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label" for="meta-title-input">Meta title</label>
                                    <input type="text" name="meta_title" class="form-control"
                                        placeholder="Enter meta title" id="meta-title-input" value="{{$model->meta_title}}" >
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label" for="meta-keywords-input">Meta Keywords</label>
                                    <input type="text" name="meta_keywords" class="form-control"
                                        placeholder="Enter meta keywords" id="meta-keywords-input" value="{{$model->meta_keywords}}" >
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label" for="meta-keywords-input">Meta Description</label>
                                    <textarea  name="meta_description" class="form-control"
                                        placeholder="Enter meta description" id="meta-keywords-input" >{{$model->meta_description}}</textarea>
                            
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
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#default-accordion-example" style="max-height:300px;overflow-y:auto;">
                                <div class="accordion-body">
                                     <div id="feature" >
                                        @if(!is_null($facet_atributes))
                                            @foreach($facet_atributes as $at)
                                                @php
                                                    $name=$at->name;
                                                    $slug=\Str::slug($at->name);
                                                    $values=json_decode($at->attribute_values?->attribute_value_template?->values_json,true);
                                                    $value_for_product_for_this_attribute=isset($product_existing_features[$at->id])?$product_existing_features[$at->id]['value']:null;
                                                @endphp
                                                   @if($values)
                                                <div class="mb-3">
                                                    <label class="form-label" for="meta-title-input">{{ $name }}</label>
                                                    <select class="form-select no-select2" name="facet_attribute__{{ $slug.'==='.$at->id }}">
                                                        <option value="">Select {{ $name }} type</option>
                                                   
                                                        @foreach($values as $f)
                                                          @php
                                                         $product_feature_row=null;
                                                        if($product_existing_features){
                                                                $i=0;
                                                                foreach ($product_existing_features as $x) {
                                                                        if (isset($x->attribute_id) && $x->attribute_id === $at->id) {
                                                                            $product_feature_row = $product_existing_features[$i];
                                                                            break;
                                                                        }
                                                                        $i++;
                                                                    }
                                                            } 
                                                        $selected= $product_feature_row?($product_feature_row->value==$f['name']?'selected':''):'';
                                                        
                                                        @endphp
                                                            <option value="{{ $f['name'] }}" {{$selected}}>{{ ucwords($f['name']) }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            @endif
                                            @endforeach
                                            @endif

                                     </div>
                                </div>
                            </div>
                         </div>
            </div>

            </div>
            <div class="col-md-12">
                <div class="card mb-4">

                    <div class="card-body">


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" name="has_variant"
                                        onchange="toggleDivDisplay(this.value,'var_c','Yes')" value="Yes"
                                        type="checkbox" id="formCheck3" @if ($model->has_variant == 'Yes') checked @endif>
                                    <label class="form-check-label" for="formCheck1">
                                        Product has variant?
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12" id="var_c"
                                @if($model->has_variant=='No') style="display:none" @endif >

                                <div class="d-none" id="copy">


                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Attribute</label>
                                                <select class="form-control no-select2" name="xattribute"
                                                    onChange="show(this.value,event)">
                                                    <option value="">Select Attributes</option>
                                                    @foreach ($attributes as $b)
                                                        <option value="{{ $b->id }}">{{ $b->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 p">


                                        </div>

                                    </div>

                                </div>


                                <div id="variant_container"
                                    style="padding: 18px;
                                            margin-top: 25px;
                                            border: 1px solid #dbe9ec;">


                                    <div class="d-flex justify-content-between">
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
                                            @if ($model->has_variant=='Yes' && $model->attributes)
                                                @php
                                                    $attributes_s = json_decode($model->attributes, true);
                                                @endphp
                                                @if (!empty($attributes_s) && $attributes_s[0])
                                                    @foreach ($attributes_s as $k => $v)
                                                    @php 
                                                    $inputtag_val=is_array($v['value'])?implode(',',$v['value']):$v['value'];
                                                    @endphp
                                                        <div class="row mb-4" id="row-{{ $loop->index }}">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label">Attribute</label>
                                                                    <select class="form-control no-select2"
                                                                        name="attributes[]"  data-select='{!! $str!!}'
                                                                        onChange="show(this.value,event)">
                                                                        <option value="">Select Attributes
                                                                        </option>
                                                                        @foreach ($attributes as $b)
                                                                            <option value="{{ $b->id }}"
                                                                                @if ($v['id'] == $b->id) selected @endif>
                                                                                {{ $b->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 p">
                                                                <div class="form-group">
                                                                    <label class="form-label">Value </label>
                                                                    @if($v['name']!='Color')
                                                                    <div>
                                                                        <input  class="form-control attribute_values"
                                                                         name="value-{{$v['id']}}" value="{{$inputtag_val}}"  data-role="tagsinput" />
                                                                    </div>
                                                                    @else 
                                                                    <div>
                                                                        <select class="form-control attribute_values select_tag" data-selected='@json($v["value"])' multiple="multiple" name="value-{{$v['id']}}[]" >
                                                                        {!!$str1!!}
                                                                        </select>
                                                                    </div>

                                                                    @endif
                                                                  
                                                                </div>

                                                            </div>

                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="row mb-4" id="row-0">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">Attribute</label>
                                                                <select class="form-control no-select2" 
                                                                name="attributes[]"
                                                                    onChange="show(this.value,event)">
                                                                    <option value="">Select Attributes
                                                                    </option>
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
                                                @endif
                                            @else
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
                                            @endif
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <div class="accordion" id="accord">
                                                @if (!empty($model->variants))
                                                    @foreach ($model->variants as $variant)
                                                        <div class="accordion-item shadow mb-2" data-id="{{$variant->id}}">
                                                            <h2 class="accordion-header d-flex flex-between">
                                                              
                                                        <button class="btn btn-outline-danger" type="button">
                                                        <i class="mdi mdi-close-outline" onClick="deleteAccordian(this,'{!!$variant->id!!}')" style="font-sze:20px;"></i>
                                                        </button> 
                                                         <button class="accordion-button" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse{{ $loop->index }}"
                                                                    aria-expanded="true" aria-controls="collapse${i}">
                                                                    {{ $variant->name }}
                                                                </button>
                                                              
                                                            </h2>
                                                            <div id="collapse{{ $loop->index }}"
                                                                class="accordion-collapse collapse"
                                                                aria-labelledby="headingOne"
                                                                data-bs-parent="#default-accordion-example">
                                                                <div class="accordion-body">
                                                                    <div class="row">

                                                                        <div class="col-md-2">
                                                                            <input type="hidden"
                                                                                name="variant_id__{{ $variant->name }}"
                                                                                value="{{ $variant->id }}" />
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="product-title-input">
                                                                                    Price</label>

                                                                                <input type="number" class="form-control"
                                                                                    name="variant_price__{{ $variant->name }}"
                                                                                    placeholder="Price"
                                                                                    value="{{ $variant->price }}">



                                                                            </div>


                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="product-title-input">Sale
                                                                                    Price</label>

                                                                                <input type="number" class="form-control"
                                                                                    name="variant_sale_price__{{ $variant->name }}"
                                                                                    id="product-price-input"
                                                                                    placeholder="Sale Price"
                                                                                    value="{{ $variant->sale_price }}">



                                                                            </div>


                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="product-title-input">Stock
                                                                                    Quantity
                                                                                </label>

                                                                                <input type="number" class="form-control"
                                                                                    name="variant_quantity__{{ $variant->name }}"
                                                                                    id="product-price-inputo"
                                                                                    placeholder="Stock Quantity"
                                                                                    value="{{ $variant->quantity }}">



                                                                            </div>


                                                                        </div>
                                                                       

                                                                        <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label class="form-label"
                                                                                        for="product-title-input">Main
                                                                                        Image
                                                                                    </label>

                                                                                    <input type="file"
                                                                                        class="form-control"
                                                                                        name="variant_image__{{ $variant->name }}" />



                                                                                </div>
                                                                                @if ($variant->image)
                                                           <div class="image_place mt-4"
                                                                                        id="variant_img_div">

                                                                                        <i class="bx bx-trash text-danger del_ic"
                                                                                            onClick="deleteFileSelf('{!! $variant->image !!}', 'ProductVariant', 'products/{!! $model->id !!}/variants/',
                                                                'image','{!! $variant->id !!}')"></i>
                                                                                        <a href="{{ asset('storage/products/' . $model->id . '/variants/' . $variant->image) }}"
                                                                                            data-lightbox="image-1">
                                                                                            <img src="{{ asset('storage/products/' . $model->id . '/variants/' . $variant->image) }}"
                                                                                                style="width:100%;height:100%;object-fit:fit" />

                                                                                        </a>

                                                                                    </div>
                                                                                @endif

                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label class="form-label"
                                                                                        for="product-title-input">Gallery
                                                                                    </label>

                                                                                    <input type="file" multiple
                                                                                        class="form-control"
                                                                                        name="variant_product_images__{{ $variant->name }}[]" />



                                                                                </div>
                                                                                @if (count($variant->images) > 0)
                                                                                    <div class="hstack gap-2 mt-2">
                                                                                        @foreach ($variant->images as $img)
                                                                                            <div class="image_place"
                                                                                                id="variant_img_div-{{ $img->id }}">
                                                                                                <i class="bx bx-trash text-danger del_ic"
                                                                                                    onclick="deleteFileFromTable('{!! $img->id !!}', 'product_variant_images', 'products/{!! $model->id !!}/variants/', '{!! route('deleteTableFile') !!}')"></i>
                                                                                                <a href="{{ asset('storage/products/' . $model->id . '/variants/' . $img->name) }}"
                                                                                                    data-lightbox="image-1">

                                                                                                    <img src="{{ asset('storage/products/' . $model->id . '/variants/' . $img->name) }}"
                                                                                                        style="width:100%;height:100%;object-fit:fit" />
                                                                                                </a>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                @endif 

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                            </div><!--acrdian end-->
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>

                                </div>





                            </div>

                        </div>
                    </div>
                    <!-- end card body -->
                </div>
            </div>

            <div class="text-end mb-3 mt-3">
                <button type="submit" class="btn btn-success w-sm">Submit</button>
            </div>


            </form>




        </div>
    @endsection
@push('scripts')
<script >
document.addEventListener('DOMContentLoaded', function () {
  console.log('okok nabse')
 
    $('.select_tag').each(function () {
            const $select = $(this);
            const selected = $select.data('selected');

            $select.select2({
                tags: true,
                tokenSeparators: [',']
            });

            if (Array.isArray(selected)) {
                $select.val(selected).trigger('change');
            }
        });
});
function fetchCategoryAttributes(cat,product_id){
 $.ajax({
            url: "/category_attributes", // Or route('facet_attributes.update') for edit
            method: "POST",
            data: {id:cat,product_id},
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