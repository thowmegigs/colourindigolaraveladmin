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
                {!! Form::open()->route('products.store')->id('productForm')->multipart()->attrs(['data-module' => 'Product']) !!}

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
                                       <option value="{{$cat->id}}">{{ucwords($cat->name)}}</option>
                                    @endforeach
                                  
                                </select>
                            </div>
                            <div class="form-group mb-2 col">
                                <label>Product Name <span class="required">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group mb-2 col">
                                <label>Price <span class="required">*</span></label>
                                <input type="text" name="price" class="form-control" required>
                            </div>
                            <div class="form-group mb-2 col">
                                <label>Sale Price <span class="required">*</span></label>
                                <input type="text" name="sale_price" class="form-control" required>
                            </div>
                            <div class="form-group mb-2">
                                <label>Short Description </label>
                                <textarea name="short_description" class="form-control" rows="1" ></textarea>
                            </div>
                            <div class="form-group">
                                <label>Description <span class="required">*</span></label>
                                <textarea name="description" class="summernote form-control" required></textarea>
                            </div>
                            </div>
                    </div>

                    <!-- Tab 2 -->
                    <div class="tab-pane fade" id="stock_info_tab">
                        <div class="form-group">
                            <label>Stock <span class="required">*</span></label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Maximum Purchase Allowed</label>
                            <input type="number" name="max_purchase_qty" value="3" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>SKU <span class="required">*</span></label>
                            <input type="text" name="sku" class="form-control" required>
                        </div>
                    </div>

                    <!-- Tab 3 -->
                    <div class="tab-pane fade" id="product_image_tab">
                    <div class="form-group mb-3">
                            <label>Product Main Image <span class="required">*</span></label>
                            <input type="file" name="image" id="single_prod_image" class="form-control" required/>
                            <div id="gallery1"></div>
                        </div>
                        <div class="form-group">
                            <label>Product Images</label>
                            <input type="file" name="images[]" id="multiple_prod_image" 
                            class="form-control" multiple>
                            <div id="multiple-preview" class="preview-container"></div>
                        </div>
                    </div>

                    <!-- Tab 4 -->
                    <div class="tab-pane fade" id="size_chart_tab">
                        <!-- Radio Options -->
                        <div class="form-group">
                            <label><strong>Size Chart Type</strong></label><br>
                            <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="size_chart_type" id="sizeChartImage"
                                    value="image" checked>
                                <label class="form-check-label" for="sizeChartImage">Upload Image</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="size_chart_type" id="sizeChartForm"
                                    value="form">
                                <label class="form-check-label" for="sizeChartForm">Create with Form</label>
                            </div>
                            </div>
                          
                        </div>

                        <!-- Image Upload Section -->
                        <div id="sizeChartImageSection">
                            <div class="form-group">
                                
                                <input type="file" class="form-control-file" name="size_chart_image"
                                    id="sizeChartImageFile" accept="image/*">
                                    <div id="chart-image-preview" class="preview-container"></div>
                            </div>
                        </div>

                        <!-- Tabbed Form Section (Initially Hidden) -->
                        <div id="sizeChartFormSection" style="display: none;">
                            <!-- Bootstrap Tabs -->
                            <ul class="nav nav-tabs" id="sizeChartTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="inch-tab" data-bs-toggle="tab" href="#inch1"
                                        role="tab">Inch</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="cm-tab" data-bs-toggle="tab" href="#cm1"
                                        role="tab">CM</a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <!-- Inch Tab -->
                                <div class="tab-pane fade show active" id="inch1" role="tabpanel">
                                <div id="inch-repeater-container">
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
                                    </div>
                                    <button id="add-inch-row" type="button" class="btn btn-primary mt-2">Add Inch Row</button>

                                </div>

                                <!-- CM Tab -->
                                <div class="tab-pane fade" id="cm1" role="tabpanel">
                                   <div id="cm-repeater-container">
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
                                        </div>
                                        <button id="add-cm-row" type="button" class="btn btn-primary mt-2">Add CM Row</button>
                                   
                                    </div>
                            </div>
                        </div>

                    </div>

                    <!-- Tab 5 -->
                    <div class="tab-pane fade" id="seo_tab">
                        <div class="form-group mb-2">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" class="form-control">
                        </div>
                        <div class="form-group mb-2">
                            <label>Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_description" class="form-control"></textarea>
                        </div>
                    </div>

                    <!-- Tab 6 -->
                    <div class="tab-pane fade" id="variant_tab">
                     <h5>Select Attributes</h5>
                    <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Sizes</label>
                        <select id="sizes" class="form-select"  multiple>
                        <option>Small</option>
                        <option>Medium</option>
                        <option>Large</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Colors</label>
                        <select id="colors" class="form-select" multiple>
                                <option value="White">White</option>
                                <option value="Black">Black</option>
                                <option value="Red">Red</option>
                                <option value="Green">Green</option>
                                <option value="Blue">Blue</option>
                                <option value="Yellow">Yellow</option>
                                <option value="Cyan">Cyan</option>
                                <option value="Magenta">Magenta</option>

                                <!-- Shades of Red -->
                                <option value="LightCoral">LightCoral</option>
                                <option value="Salmon">Salmon</option>
                                <option value="DarkSalmon">DarkSalmon</option>
                                <option value="LightSalmon">LightSalmon</option>
                                <option value="Crimson">Crimson</option>
                                <option value="FireBrick">FireBrick</option>
                                <option value="DarkRed">DarkRed</option>

                                <!-- Shades of Green -->
                                <option value="Lime">Lime</option>
                                <option value="LimeGreen">LimeGreen</option>
                                <option value="ForestGreen">ForestGreen</option>
                                <option value="DarkGreen">DarkGreen</option>
                                <option value="Olive">Olive</option>
                                <option value="OliveDrab">OliveDrab</option>
                                <option value="SpringGreen">SpringGreen</option>
                                <option value="MediumSpringGreen">MediumSpringGreen</option>
                                <option value="SeaGreen">SeaGreen</option>
                                <option value="MediumSeaGreen">MediumSeaGreen</option>
                                <option value="PaleGreen">PaleGreen</option>
                                <option value="LightGreen">LightGreen</option>

                                <!-- Shades of Blue -->
                                <option value="DodgerBlue">DodgerBlue</option>
                                <option value="MediumBlue">MediumBlue</option>
                                <option value="RoyalBlue">RoyalBlue</option>
                                <option value="SteelBlue">SteelBlue</option>
                                <option value="LightSteelBlue">LightSteelBlue</option>
                                <option value="CornflowerBlue">CornflowerBlue</option>
                                <option value="SlateBlue">SlateBlue</option>
                                <option value="MediumSlateBlue">MediumSlateBlue</option>
                                <option value="DarkSlateBlue">DarkSlateBlue</option>
                                <option value="LightBlue">LightBlue</option>
                                <option value="SkyBlue">SkyBlue</option>
                                <option value="DeepSkyBlue">DeepSkyBlue</option>
                                <option value="LightSkyBlue">LightSkyBlue</option>
                                <option value="PowderBlue">PowderBlue</option>

                                <!-- Shades of Yellow -->
                                <option value="Gold">Gold</option>
                                <option value="YellowGreen">YellowGreen</option>
                                <option value="Chartreuse">Chartreuse</option>
                                <option value="OliveYellow">OliveYellow</option>
                                <option value="LightYellow">LightYellow</option>

                                <!-- Shades of Orange -->
                                <option value="DarkOrange">DarkOrange</option>
                                <option value="OrangeRed">OrangeRed</option>
                                <option value="Coral">Coral</option>
                                <option value="Tomato">Tomato</option>
                                <option value="Orchid">Orchid</option>

                                <!-- Shades of Purple -->
                                <option value="Purple">Purple</option>
                                <option value="BlueViolet">BlueViolet</option>
                                <option value="Indigo">Indigo</option>
                                <option value="Violet">Violet</option>
                                <option value="MediumOrchid">MediumOrchid</option>
                                <option value="MediumPurple">MediumPurple</option>
                                <option value="DarkOrchid">DarkOrchid</option>
                                <option value="SlateBlue">SlateBlue</option>

                                <!-- Shades of Brown -->
                                <option value="SaddleBrown">SaddleBrown</option>
                                <option value="Sienna">Sienna</option>
                                <option value="Chocolate">Chocolate</option>
                                <option value="Peru">Peru</option>
                                <option value="BurlyWood">BurlyWood</option>
                                <option value="RosyBrown">RosyBrown</option>
                                <option value="Tan">Tan</option>

                                <!-- Shades of Gray -->
                                <option value="Gray">Gray</option>
                                <option value="LightGray">LightGray</option>
                                <option value="DarkGray">DarkGray</option>
                                <option value="DimGray">DimGray</option>
                                <option value="Gainsboro">Gainsboro</option>
                                <option value="Silver">Silver</option>
                                <option value="SlateGray">SlateGray</option>
                                <option value="LightSlateGray">LightSlateGray</option>

                                <!-- Others -->
                                <option value="Beige">Beige</option>
                                <option value="AntiqueWhite">AntiqueWhite</option>
                                <option value="Linen">Linen</option>
                                <option value="Lavender">Lavender</option>
                                <option value="Thistle">Thistle</option>
                                <option value="Plum">Plum</option>
                                <option value="MistyRose">MistyRose</option>
                                <option value="Azure">Azure</option>
                                <option value="AliceBlue">AliceBlue</option>
                                <option value="PapayaWhip">PapayaWhip</option>
                                <option value="BlanchedAlmond">BlanchedAlmond</option>
                            </select>

                    </div>
                    </div>
                    <button id="generate-combinations" type="button" class="btn btn-primary mb-4">Generate Variants</button>

                    <hr>

                    <h5>Generated Variant Combinations</h5>
                    <div id="variant-combinations-container"></div>
                   
                
                
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
                console.log($(tabs).length)
                let g = $(tabs).eq(n)[0];
               // console.log('ye rha tab',g[0])
                $(tabs).removeClass('active show')
                $(g).addClass('fade active show')
                let n1 = $(tabLinks).eq(n)[0];
                $(tabLinks).removeClass('active')
                $(n1).addClass('active')
           


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
                fileInput && fileInput.addEventListener('change', function() {
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
                        //document.getElementById('sizeChartImageFile').value = '';
                        $('#sizeChartImageSection').show();
                        $('#sizeChartFormSection').hide();
                        setupImagePreview('size_chart_image', 'chart-image-preview');
                        $('#sizeChartImageSection .image-wrapper').remove();

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
                                }
                                else if(target=="product_image_tab"){
                                    single_prod_image
                                    single_prod_image
                                    setupImagePreview('single_prod_image', 'single-preview');
                                    setupImagePreview('muliple_prod_image', 'multiple-preview');

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
                const formData = new FormData(this);
                $.ajax({
                    url: "{{ domain_route('products.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: res => {
                        console.log('res',res)
                        Swal.fire('Success', 'Product saved successfully!', 'success');
                       // this.reset();
                        //currentTab = 0;
                       // showTab(currentTab);
                    },
                    error: err => {
                        let errorMsg = '';
                        if (err.responseJSON.errors) {
                            Object.values(err.responseJSON.errors).forEach(e => {
                                errorMsg += `<p>${e[0]}</p>`;
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMsg
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection
