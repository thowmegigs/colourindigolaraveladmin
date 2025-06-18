{!! Form::open()->route(route_prefix().$plural_lowercase . '.store')->id(strtolower($module) . '_form')->multipart() !!}
@if ($has_image && count($image_field_names) > 0)

    <div class="row">
        @if ($show_crud_in_modal)

           

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <div class="card-text">
                            <div class="row">
                                 <div class="col-md-12 mb-3">
                                <p style="font-weight:450;font-size: 14px;">Content Section Type</p>
                                <div class="align-items-center">
                                    <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Cart" value="Products"  checked="" class="form-check-input"><label for="inp-type-Cart"
                                            class="form-check-label">Products Only</label></div>
                                             <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Individual Quantity" value="Categories"
                                            class="form-check-input"><label for="inp-type-Individual Quantity" class="form-check-label">Categories Only
                                            </label></div>
                                              <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-BOGO" value="Collections" class="form-check-input"><label for="inp-type-BOGO"
                                            class="form-check-label">Collections Only</label></div>
                                    <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Bulk" value="Banner" class="form-check-input"><label
                                            for="inp-type-Bulk" class="form-check-label">Banner Only</label>
                                        </div>
                                    <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Bulk" value="Slider" class="form-check-input"><label
                                            for="inp-type-Bulk" class="form-check-label">Slider Only</label>
                                        </div>
                                        <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Bulk" value="Video" class="form-check-input"><label
                                            for="inp-type-Bulk" class="form-check-label">Videos Only</label>
                                        </div>
                                        <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Bulk" value="Coupons" class="form-check-input"><label
                                            for="inp-type-Bulk" class="form-check-label">Coupons List Only</label>
                                        </div>
                                   
                                    
                                  
                                   
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="product-title-input">Section Name </label>
                
                               <input type="text" name="section_title" id="inp-section_title" class="form-control">
                
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="product-title-input">Section Background Color </label>
                
                               <input type="color" name="section_background_color" id="section_background_color" class="form-control">
                
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="product-title-input">Section header Image </label>
                
                               <input type="file" name="header_image" id="section_header_imge" 
                               class="form-control">
                
                            </div>
                           
                           <div class="col-md-6 mb-3">
                                                   
                        <div class="form-group"><label for="inp-section_subtitle" class="">Section Display Subtitle</label>
                        <input type="text" name="section_subtitle" id="inp-section_subtitle" value="" class="form-control" placeholder="Enter section subtitle"></div>
           
                        
                                                </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="product-title-input">Category <span class="text-danger">*</span></label>
                
                                <select id="category_id" class="form-select" multiple  name="categories[]"
                                     onChange="showProductsonMultiCategorySelect()">
                                    {!! $category_options !!}
                                </select>
                
                            </div>
                        </div>
                            <x-forms :data="$data" column='2' />
                            @if (count($repeating_group_inputs) > 0)
                                @foreach ($repeating_group_inputs as $grp)
                                    <x-repeatable :data="$grp['inputs']" :label="$grp['label']" values="" :index="$loop->index"
                                        :hide="$grp['hide']" :indexWithModal="$grp['index_with_modal']" :modalInputBoxIdWhoseValueToSetInSelect="$grp['modalInputBoxIdWhoseValueToSetInSelect']" />
                                @endforeach
                            @endif
                           
                            <x-imageform :data="$data" column='2' />


                        </div>
                    </div>
                </div>
            </div>
           
        @else
            <x-forms :data="$data" column='1' />
            <x-imageform :data="$data" column='1' />
            @if (count($repeating_group_inputs) > 0)
                @foreach ($repeating_group_inputs as $grp)
                    <x-repeatable :data="$grp['inputs']" :label="$grp['label']" values="" :index="$loop->index"
                        :hide="$grp['hide']" :indexWithModal="$grp['index_with_modal']" :modalInputBoxIdWhoseValueToSetInSelect="$grp['modalInputBoxIdWhoseValueToSetInSelect']" />
                @endforeach
            @endif
        @endif
    </div>
@endif


<div class="row mt-2">
    <div class="col-sm-12 " style="text-align:right">
        @php
            $r = 'Submit';
        @endphp
        {!! Form::submit($r)->id(strtolower($module) . '_btn')->primary() !!}
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Close</button>
    </div>
</div>
{!! Form::close() !!}
