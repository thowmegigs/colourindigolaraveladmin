@extends('layouts.admin.app')

@section('content')
<style>
    .upload-area {
    border: 2px dashed #aaa;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    background-color: #fafafa;
    transition: background 0.3s, border-color 0.3s;
    position: relative;
}
.ind-image{
    width:50px;height:50px;border:1px solid red;margin:2px;
}
.upload-area:hover {
    background-color: #f0f0f0;
    border-color: #333;
}

.upload-label {
    font-family: Arial, sans-serif;
    font-size: 18px;
    color: #555;
    display: block;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.upload-area.dragover {
    background-color: #e0f7fa;
    border-color: #0288d1;
}
.image-wrapper{
    position:relative;margin-top:5px;display:inline-block;
}
.image-delete-btn{
    position:absolute;
    top:0;right:0;color:red;
    border: 0;z-index:1;
    /* width: 16px; */
    /* height: 16px; */
    border-radius: 50%;
    background: black;
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
        <div class="card">
            <div class="card-body">
            {!! Form::open()->post()->route('products.update', ['product' => $model->id])->id('productForm')->multipart() !!}

            @method('PUT')
                <ul class="nav nav-tabs nav-justified" id="formTabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="profile-tab"
                     data-bs-toggle="tab"
                       href="#product_info_tab">Product Info</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#stock_info_tab">Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#product_image_tab">Images</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#size_chart_tab">Size Chart</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#seo_tab">SEO</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#variant_tab">Variant</a></li>
                </ul>

                <div class="tab-content border p-3 bg-white" id="tabContent">
                    <!-- Tab 1 -->
                    <div class="tab-pane fade show active" id="product_info_tab">
                        <div class="row">
                          <div class="form-group mb-2 col">
                                <label>Category <span class="required">*</span></label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    {!! $category_options !!}
                                  
                                </select>
                            </div>
                          <div class="form-group mb-2 col">
                                <label>Brand <span class="required">*</span></label>
                                <select name="brand_id" class="form-control" required>
                                    <option value="">Select Brands</option>
                                    @foreach($brands as $cat)
                                       <option value="{{$cat->id}}" @if($cat->id==$model->brand_id) selected @endif>{{ucwords($cat->name)}}</option>
                                    @endforeach
                                  
                                </select>
                            </div>
                            <div class="form-group mb-2 col">
                                <label>Product Name <span class="required">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{$model->name}}" required>
                            </div>
                            <div class="form-group mb-2 col">
                                <label>Price <span class="required">*</span></label>
                                <input type="text" name="price" class="form-control" value="{{$model->price}}" required>
                            </div>
                            <div class="form-group mb-2 col">
                                <label>Sale Price <span class="required">*</span></label>
                                <input type="text" name="sale_price" class="form-control" value="{{$model->sale_price}}" required>
                            </div>
                            <div class="form-group mb-2">
                                <label>Short Description </label>
                                <textarea name="short_description" class="form-control" rows="1" >{{$model->short_description}}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Description <span class="required">*</span></label>
                                <textarea name="description" class="summernote form-control" required>{!! $model->description !!}</textarea>
                            </div>
                            </div>
                    </div>

                    <!-- Tab 2 -->
                    <div class="tab-pane fade" id="stock_info_tab">
                        <div class="form-group mb-2">
                            <label>Stock <span class="required">*</span></label>
                            <input type="number" name="quantity" class="form-control"  value="{{$model->quantity}}" required>
                        </div>
                        <div class="form-group mb-2">
                            <label>Maximum Purchase Allowed</label>
                            <input type="number" name="max_purchase_qty" value="3" value="{{$model->max_purchase_qty}}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>SKU <span class="required">*</span></label>
                            <input type="text" name="sku" class="form-control" value="{{$model->sku}}" required>
                        </div>
                    </div>

                    <!-- Tab 3 -->
                    <div class="tab-pane fade" id="product_image_tab">
                    <div class="form-group mb-3">
                            <label>Product Main Image <span class="required">*</span></label>
                            <input type="file" name="image" id="single_prod_image" class="form-control" 
                             @if(is_null($model->image)) required @endif/>
                            @if($model->image) 
                                  <div class="image-wrapper">
                                          <button class="image-delete-btn" type="button" data-id="{{$model->id}}" data-column="image" data-image_name=''>&times;</button>
                                    <a href="{{asset('storage/'.$model->image)}}" target="_blank">
                                        <img class="ind-image" src="{{asset('storage/'.$model->image)}}" />
                                    </a>
                                    </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Product Images</label>
                            <input type="file" name="images[]" id="multiple_prod_image" 
                            class="form-control" multiple>
                            <div id="multiple-preview" class="preview-container"></div>
                            <div class="gallery1 d-flex flex-row">
                            @if($model->additional_images)
                                @foreach(json_decode($model->additional_images,true) as $img) 
                                  <div class="image-wrapper">
                                          <button class="image-delete-btn"  type="button" data-id="{{$model->id}}" data-column="additional_images" data-image_name='{{$img}}'>&times;</button>
                                          <a href="{{asset('storage/'.$img)}}" target="_blank">
                                        <img class="ind-image" src="{{asset('storage/'.$img)}}" />
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                               
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4 -->
                    <div class="tab-pane fade" id="size_chart_tab">
                        <!-- Radio Options -->
                        <div class="form-group">
                            <label><strong>Size Chart Type</strong></label><br>
                            @php 
                            $size_chart=$model->size_chart?json_decode($model->size_chart,true):null;
                            @endphp
                            <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="size_chart_type" id="sizeChartImage"
                                    value="image" @if($size_chart && $size_chart['size_chart_type']=='image') checked @endif>
                                <label class="form-check-label" for="sizeChartImage">Upload Image</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="size_chart_type" id="sizeChartForm"
                                    value="form" @if($size_chart && $size_chart['size_chart_type']=='form') checked @endif>
                                <label class="form-check-label" for="sizeChartForm">Create with Form</label>
                            </div>
                            </div>
                          
                        </div>

                       
                        <div id="sizeChartImageSection" style="display:@if($size_chart && $size_chart['size_chart_type']=='image') block @else none @endif">
                            <div class="form-group">
                                
                                <input type="file" class="form-control-file" name="size_chart_image"
                                    id="sizeChartImageFile" accept="image/*">
                                    <div id="chart-image-preview" class="preview-container"></div>
                                    @if($size_chart && $size_chart['size_chart_type']=='image')
                                       
                                        <div class="image-wrapper">
                                                <button class="image-delete-btn" type="button" data-id="{{$model->id}}" data-column="size_chart" data-image_name="{{$size_chart['image']}}">&times;</button>
                                                <a href="{{asset('storage/'.$size_chart['image'])}}" target="_blank">
                                                    <img class="ind-image" src="{{asset('storage/'.$size_chart['image'])}}" />
                                                </a>
                                                </div>
                                    @endif

                            </div>
                        </div>
                      

                        <!-- Tabbed Form Section (Initially Hidden) -->
                        <div id="sizeChartFormSection" style="display:  @if($size_chart && $size_chart['size_chart_type']=='form') black @else none @endif;">
                            <!-- Bootstrap Tabs -->
                            <ul class="nav nav-tabs" id="sizeChartTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="inch-tab" data-bs-toggle="tab" href="#inch1"
                                        role="tab">Inch</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " id="cm-tab" data-bs-toggle="tab" href="#cm1"
                                        role="tab">CM</a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <!-- Inch Tab -->
                                <div class="tab-pane fade show active" id="inch1" role="tabpanel">
                                <div id="inch-repeater-container">
                                    @if($size_chart && $size_chart['size_chart_type']=='form')
                                    
                                       @foreach($size_chart['inch_chest'] as $index=>$val )
                                            <div class="inch-repeater-item row mb-2">
                                                <div class="col-md-3">
                                                <input type="text" name="inch_label[]" value="{{$size_chart['inch_label'][$index]}}" class="form-control" placeholder="Size Label">
                                                </div>
                                                <div class="col-md-3">
                                                <input type="text" name="inch_chest[]" value="{{$size_chart['inch_chest'][$index]}}" class="form-control" placeholder="Chest (in)">
                                                </div>
                                                <div class="col-md-2">
                                                <input type="text" name="inch_shoulder[]" value="{{$size_chart['inch_shoulder'][$index]}}" class="form-control" placeholder="Shoulder (in)">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="inch_waist[]" value="{{$size_chart['inch_waist'][$index]}}" class="form-control" placeholder="Waist (in)">
                                                </div>
                                                <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-row">Remove</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else 
                                    <div class="inch-repeater-item row mb-2">
                                        <div class="col-md-3">
                                        <input type="text" name="inch_label[]" class="form-control" placeholder="Size Label">
                                        </div>
                                        <div class="col-md-3">
                                        <input type="text" name="inch_chest[]" class="form-control" placeholder="Chest (in)">
                                        </div>
                                        <div class="col-md-2">
                                        <input type="text" name="inch_shoulder[]" class="form-control" placeholder="Shoulder (in)">
                                        </div>
                                        <div class="col-md-2">
                                        <input type="text" name="inch_waist[]" class="form-control" placeholder="Waist (in)">
                                        </div>
                                        <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                                        </div>
                                    </div>
                                    @endif 
                                    </div>
                                    <button id="add-inch-row" type="button" class="btn btn-primary mt-2">Add Inch Row</button>

                                </div>

                                <!-- CM Tab -->
                                <div class="tab-pane fade" id="cm1" role="tabpanel">
                                   <div id="cm-repeater-container">
                                   @if($size_chart && $size_chart['size_chart_type']=='form')
                                   @foreach($size_chart['cm_chest'] as $index=>$val )
                                        <div class="cm-repeater-item row mb-2">
                                            <div class="col-md-3">
                                            <input type="text" name="cm_label[]" value="{{$size_chart['cm_label'][$index]}}" class="form-control" placeholder="Size Label">
                                            </div>
                                            <div class="col-md-3">
                                            <input type="text" name="cm_chest[]" value="{{$size_chart['cm_chest'][$index]}}" class="form-control" placeholder="Chest (cm)">
                                            </div>
                                            <div class="col-md-2">
                                            <input type="text" name="cm_shoulder[]" value="{{$size_chart['cm_shoulder'][$index]}}" class="form-control" placeholder="Shoulder (cm)">
                                            </div>
                                            <div class="col-md-2">
                                            <input type="text" name="cm_waist[]" value="{{$size_chart['cm_waist'][$index]}}" class="form-control" placeholder="Waist (cm)">
                                            </div>
                                            <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-row">Remove</button>
                                            </div>
                                        </div>
                                        @endforeach
                                        @else 
                                        <div class="cm-repeater-item row mb-2">
                                            <div class="col-md-3">
                                            <input type="text" name="cm_label[]" class="form-control" placeholder="Size Label">
                                            </div>
                                            <div class="col-md-3">
                                            <input type="text" name="cm_chest[]" class="form-control" placeholder="Chest (cm)">
                                            </div>
                                            <div class="col-md-2">
                                            <input type="text" name="cm_shoulder[]" class="form-control" placeholder="Shoulder (cm)">
                                            </div>
                                            <div class="col-md-2">
                                            <input type="text" name="cm_waist[]" class="form-control" placeholder="Waist (cm)">
                                            </div>
                                            <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-row">Remove</button>
                                            </div>
                                        </div>
                                        @endif 
                                        </div>
                                        <button id="add-cm-row" type="button" class="btn btn-primary mt-2">Add CM Row</button>
                                   
                                    </div>
                            </div>
                        </div>

                    </div>

                    <!-- Tab 5 -->
                     @php 
                        $seo=$model->seo?json_decode($model->seo):null
                     @endphp
                    <div class="tab-pane fade" id="seo_tab">
                        <div class="form-group mb-2">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" value="{{$seo?$seo->meta_title:''}}" class="form-control">
                        </div>
                        <div class="form-group mb-2">
                            <label>Meta Keywords</label>
                            <input type="text" name="meta_keywords" value="{{$seo?$seo->meta_keywords:''}}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_description"  class="form-control">{{$seo?$seo->meta_description:''}}</textarea>
                        </div>
                    </div>

                    <!-- Tab 6 -->
                    <div class="tab-pane fade" id="variant_tab">
                     <h5>Select Attributes</h5>
                    <div class="row mb-3">
                    <div class="col-md-6">
                        @php 
                         $variants=($model->variants)?json_decode($model->variants,true):null;
                           $sizes=($variants)?array_column($variants,'size'):null;
                           $colors=($variants)?array_column($variants,'color'):null;
                        @endphp
                        <label>Sizes</label>
                        <select id="sizes" class="form-select"  multiple>
                        <option value="S" @if($sizes && in_array('S',$sizes)) selected @endif>S</option>
                        <option value="M" @if($sizes && in_array('M',$sizes)) selected @endif>M</option>
                        <option value="L" @if($sizes && in_array('L',$sizes)) selected @endif>L</option>
                        <option value="XL" @if($sizes && in_array('XL',$sizes)) selected @endif>XL</option>
                        <option value="XXL" @if($sizes && in_array('XXL',$sizes)) selected @endif>XXL</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Colors</label>
                        @php 
                           $predefined_colors=colors();
                        @endphp
                        <select id="colors" class="form-select" multiple>
                                <option value="">Select Color</option>
                                @foreach($predefined_colors as $color )
                                <option value="{{$color}}"  @if($colors && in_array($color,$colors)) selected @endif style="background-color:{{$color}}">{{$color}}</option>
                               
                                @endforeach
                         </select>

                    </div>
                    </div>
                    <button id="generate-combinations" type="button" class="btn btn-primary mb-4">Generate Variants</button>

                    <hr>

                    <h5>Generated Variant Combinations</h5>
                    <div id="variant-combinations-container">
                        @if($variants)
                          @foreach($variants as $index=>$t)
                                <div class="variant-item border rounded p-2 mb-2">
                                        <div class="row align-items-center gx-2">
                                            <div class="col-auto">
                                            <strong>{{$t['size']}}/{{$t['color']}}</strong>
                                            <input type="hidden" name="variant_size[]" value="{{$t['size']}}">
                                            <input type="hidden" name="variant_color[]" value="{{$t['color']}}">
                                            </div>
                                            <div class="col">
                                            <input type="text" name="variant_sku[]" value="{{$t['sku']}}" class="form-control form-control-sm" placeholder="SKU">
                                            </div>
                                            <div class="col">
                                            <input type="number" name="variant_price[]" value="{{$t['price']}}" class="form-control form-control-sm" placeholder="Price">
                                            </div>
                                            <div class="col">
                                            <input type="number" name="variant_stock[]" value="{{$t['stock']}}" class="form-control form-control-sm" placeholder="Stock">
                                            </div>
                                            <div class="col">
                                            <input type="file" name="variant_images_{{$index}}[]" class="form-control form-control-sm" multiple>
                                            @if(count($t['images'])>0)
                                            @foreach($t['images'] as $img)
                                                <div class="d-flex flex-row">
                                                <div class="image-wrapper">
                                                    <button class="image-delete-btn" type="button" data-variant_at_index="{{$index}}"  data-id="{{$model->id}}" data-column="variants" data-image_name='{{$img}}'>&times;</button>
                                                    <a href="{{asset('storage/'.$img)}}" target="_blank">
                                                    <img class="ind-image" src="{{asset('storage/'.$img)}}" />
                                                        </a>
                                                </div>
                                                </div>
                                            @endforeach
                                            @endif
                                            </div>
                                        </div>
                                </div>
                            @endforeach
                             @endif
                    </div>
                   
                
                
                   </div>

                <!-- Navigation Buttons -->
                <div class="mt-3 text-right">
                    <button type="button" class="btn btn-outline-secondary" id="prevBtn">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">Submit</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            let currentTab = 0;
            const tabs = document.querySelectorAll("#tabContent > .tab-pane");

            const tabLinks = document.querySelectorAll("#formTabs > .nav-item > .nav-link");
           
           function showTab(n) {
               
                let g = $(tabs).eq(n)[0];
               
                $(tabs).removeClass('active show')
                $(g).addClass('fade active show')
                let n1 = $(tabLinks).eq(n)[0];
                $(tabLinks).removeClass('active')
                $(n1).addClass('active')
              if(n>=2){
                   document.querySelectorAll('.image-delete-btn').forEach(button => {
                                                button.addEventListener('click', function () {
                                                   
                                                const id = this.dataset.id;
                                                const image_name = this.dataset.image_name;
                                                const column = this.dataset.column;
                                                const variant_at_index = this.dataset.variant_at_index;
                                               deleteImage(id,column,image_name,variant_at_index)
                                                // Add your delete logic here (AJAX, confirmation, etc.)
                                                });
                                            });

                                 
              }


                $("#prevBtn").toggle(n > 0);
                $("#nextBtn").toggle(n < tabs.length - 1);
                $("#submitBtn").toggle(n === tabs.length - 1);
            }

            function validateTab(n) {
                let valid = true;
                $(tabs).eq(n).find(":input[required]").each(function() {
                    if (!$(this).val()) {
                        Swal.fire('Error', 'Please fill out all required fields.', 'error');
                        valid = false;
                        return false;
                    }
                });
                return valid;
            }
            function setupImagePreview(fileInputId, previewContainerId) {
              
                const fileInput = document.getElementById(fileInputId);
                const previewContainer = document.getElementById(previewContainerId);
                console.log('preview container',previewContainer)
                fileInput &&  fileInput.addEventListener('change', function() {
                previewContainer.innerHTML = ''; // clear previous previews

                const files = Array.from(fileInput.files);

                files.forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'image-wrapper';

                    const img = document.createElement('img');
                    img.src = e.target.result;

                    const deleteBtn = document.createElement('button');
                    deleteBtn.className = 'delete-btn';
                    deleteBtn.innerHTML = '×';

                    // Delete this image on click
                    deleteBtn.addEventListener('click', () => {
                        wrapper.remove();
                        // If needed: also remove the file from input.files (advanced, optional)
                    });

                    wrapper.appendChild(img);
                    wrapper.appendChild(deleteBtn);
                    previewContainer.appendChild(wrapper);
                    };

                    reader.readAsDataURL(file);
                });
                });
             }
             function generateVairantCallback(){
                $('#generate-combinations').click(function () {
                                        
                                        const sizes = $('#sizes').val();
                                        const colors = $('#colors').val();
                                        const container = $('#variant-combinations-container');
                                        container.empty();
    
                                        if (!sizes.length || !colors.length) {
                                            alert("Please select at least one size and one color.");
                                            return;
                                        }
    
                                        let index = 0;
                                        sizes.forEach(size => {
                                            colors.forEach(color => {
                                            const row = `
                                                <div class="variant-item border rounded p-2 mb-2">
                                                <div class="row align-items-center gx-2">
                                                    <div class="col-auto">
                                                    <strong>${size} / ${color}</strong>
                                                    <input type="hidden" name="variant_size[]" value="${size}">
                                                    <input type="hidden" name="variant_color[]" value="${color}">
                                                    </div>
                                                    <div class="col">
                                                    <input type="text" name="variant_sku[]" class="form-control form-control-sm" placeholder="SKU">
                                                    </div>
                                                    <div class="col">
                                                    <input type="number" name="variant_price[]" class="form-control form-control-sm" placeholder="Price">
                                                    </div>
                                                    <div class="col">
                                                    <input type="number" name="variant_stock[]" class="form-control form-control-sm" placeholder="Stock">
                                                    </div>
                                                    <div class="col">
                                                    <input type="file" name="variant_images_${index}[]" class="form-control form-control-sm" multiple>
                                                    </div>
                                                </div>
                                                </div>
                                            `;
                                            container.append(row);
                                            index++;
                                            });
                                        });
                                        });
             }
            function handleRepeater(addBtnId, containerId, itemClass) {
              
                    $(addBtnId).click(function () {
                    let clone = $(`${containerId} .${itemClass}:first`).clone();
                       clone.find('input').val('');
                       $(containerId).append(clone);
                    });

                    $(containerId).on('click', '.remove-row', function () {
                    if ($(containerId + ' .' + itemClass).length > 1) {
                        $(this).closest('.' + itemClass).remove();
                    } else {
                        alert("At least one row is required.");
                    }
                    });
            }

            $(document).ready(function() {
                $('#profile-tab').tab('show');
                showTab(currentTab);
                $('input[name="size_chart_type"]').change(function() {
                    if ($(this).val() === 'image') {
                       // document.getElementById('sizeChartImageFile').value = '';
                        $('#sizeChartImageSection').show();
                        $('#sizeChartFormSection').hide();
                        setupImagePreview('size_chart_image', 'chart-image-preview');
                      //  $('#sizeChartImageSection .image-wrapper').remove();
                        document.querySelectorAll('.image-delete-btn').forEach(button => {
                                    button.addEventListener('click', function () {
                                       
                                    const id = this.dataset.id;
                                    const image_name = this.dataset.image_name;
                                    const column = this.dataset.column;
                                    const variant_at_index = this.dataset.variant_at_index;
                                    deleteImage(id,column,image_name,variant_at_index)
                                    // Add your delete logic here (AJAX, confirmation, etc.)
                                    });
                                });

                    } else {
                        $('#sizeChartImageSection').hide();
                        $('#sizeChartFormSection').show();
                       
                        $('#inch1').addClass('fade active show');
                        handleRepeater('#add-inch-row', '#inch-repeater-container', 'inch-repeater-item');

                        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function(tab) {
                            tab.addEventListener('shown.bs.tab', function(e) {
                                // Optional: reinit or log tab switch
                                const target = e.target.getAttribute('href'); // #inch or #cm
                                console.log('Now viewing:', target);

                                if (target == '#cm1') {
                                    handleRepeater('#add-cm-row', '#cm-repeater-container', 'cm-repeater-item');    
                                } else {
                                    handleRepeater('#add-inch-row', '#inch-repeater-container', 'inch-repeater-item');

                                }
                            });
                        });


                    }
                });
                document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function(tab) {
                            tab.addEventListener('shown.bs.tab', function(e) {
                                // Optional: reinit or log tab switch
                                const target = e.target.getAttribute('href'); // #inch or #cm
                                console.log('Now viewing parent tab:', target);

                                if (target == '#variant_tab') {
                                  
                                    generateVairantCallback();
                                    document.querySelectorAll('.image-delete-btn').forEach(button => {
                                                button.addEventListener('click', function () {
                                                    alert()
                                                const id = this.dataset.id;
                                                const image_name = this.dataset.image_name;
                                                const column = this.dataset.column;
                                                const variant_at_index = this.dataset.variant_at_index;
                                                deleteImage(id,column,image_name,variant_at_index)
                                                // Add your delete logic here (AJAX, confirmation, etc.)
                                                });
                                            });
                                }
                                else if(target=="product_image_tab"){
                                    setupImagePreview('single_prod_image', 'single-preview');
                                    setupImagePreview('muliple_prod_image', 'multiple-preview');
                                  
                                    document.querySelectorAll('.image-delete-btn').forEach(button => {
                                                button.addEventListener('click', function () {
                                                    alert()
                                                const id = this.dataset.id;
                                                const image_name = this.dataset.image_name;
                                                const column = this.dataset.column;
                                                const variant_at_index = this.dataset.variant_at_index;
                                                deleteImage(id,column,image_name,variant_at_index)
                                                // Add your delete logic here (AJAX, confirmation, etc.)
                                                });
                                            });

                                } 
                            });
                        });

            });

            $("#nextBtn").click(() => {
                if (validateTab(currentTab)) {
                    console.log('currentab',currentTab)
                    generateVairantCallback()
                    currentTab++;
                    showTab(currentTab);
                }
            });

            $("#prevBtn").click(() => {
                currentTab--;
                showTab(currentTab);
            });

            $("#addSizeInch").click(() => {
                const index = $('#sizeRepeater .form-row').length;
                $('#sizeRepeater').append(`
            <div class="form-row">
                <div class="col">
                    <input type="text" name="sizes[${index}][inch]" class="form-control mb-2" placeholder="Size (inch)">
                </div>
                <div class="col">
                    <input type="text" name="sizes[${index}][inch]" class="form-control mb-2" placeholder="Size (cm)">
                </div>
            </div>
        `);
            });
            $("#addSizeCm").click(() => {
                const index = $('#sizeCmRepeater .form-row').length;
                $('#sizeCmRepeater').append(`
            <div class="form-row">
                <div class="col">
                    <input type="text" name="sizes[${index}][cm]" class="form-control mb-2" placeholder="Size (inch)">
                </div>
                <div class="col">
                    <input type="text" name="sizes[${index}][cm]" class="form-control mb-2" placeholder="Size (cm)">
                </div>
            </div>
        `);
            });


            $("#productForm").on("submit", function(e) {
                e.preventDefault();
                $('#submitBtn').html('Please wait')
                const formData = new FormData(this);
                $.ajax({
                    url: "{{ domain_route('products.update', ['product' => $model->id]) }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: res => {
                      
                        $('#submitBtn').html('Submit')
                       if(res['success']){
                        Swal.fire('Success',res['message'], 'success');
                       // location.href='/admin/products';
                       }
                       else{
                        Swal.fire({
                            icon: 'error',
                            title: '',
                            html: res['message']
                        });
                       }
                    },
                    error: err => {
                        $('#submitBtn').html('Submit')
                        let errorMsg = '';
                        console.log('error',err)
                        if (err.responseJSON) {
                            
                                errorMsg = err.responseJSON.errors_html;
                                Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMsg
                        });
                            
                        }
                       
                    }
                });
            });
            function deleteImage(id,column,image_name) {
                let wrapper=$(event.target).closest('.image-wrapper');
                console.log('wrapper',wrapper,wrapper[0])
                
                $.ajax({
                    url: "{{ domain_route('delete_product_image') }}",
                    type: "POST",
                    data: {id,column,image_name},
                    
                    success: res => {
                        console.log('res',res)
                        Swal.fire('Success', 'Image deleted successfully!', 'success');
                        console.log('find image',wrapper.find('.ind-image'))
                        wrapper.find('.ind-image').hide();
                      
                    },
                    error: err => {
                        let errorMsg = '';
                        
                        console.error(err)
                        Swal.fire({
                            icon: 'error',
                            title: ' Error ocurred',
                            html: "d"
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
