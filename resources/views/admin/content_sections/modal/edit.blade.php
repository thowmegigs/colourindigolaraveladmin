 {!! Form::open()->put()->route(route_prefix().$plural_lowercase . '.update', [\Str::singular($plural_lowercase) => $model->id])->id(strtolower($module) . '_form') !!}
@if ($has_image && count($image_field_names) > 0)
    <div class="row">
       
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <div class="card-text">
                            <div class="row">
                            <div class="col-md-12 mb-3">
                                <p style="font-weight:450;font-size: 14px;">Content Section Type</p>
                                <div class="align-items-center">
                                    <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Cart" 
                                            value="Products"  @if($model->content_type=='Products') checked @endif class="form-check-input"><label for="inp-type-Cart"
                                            class="form-check-label">Products Only</label></div>
                                             <div class="form-check form-check-inline
                                             input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Individual Quantity" 
                                            @if($model->content_type=='Categories') checked @endif 
                                            value="Categories"
                                            class="form-check-input"><label for="inp-type-Individual Quantity" class="form-check-label">Categories Only
                                            </label></div>
                                              <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-BOGO"  
                                           @if($model->content_type=='Collections') checked @endif 
                                             value="Collections" class="form-check-input"><label for="inp-type-BOGO"
                                            class="form-check-label">Collections Only</label></div>
                                    <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Bulk1" value="Banner"  
                                           @if($model->content_type=='Banner') checked @endif 
                                            class="form-check-input"><label
                                            for="inp-type-Bulk" class="form-check-label">Banner Only</label></div>
                                 <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Bulk3" value="Slider"  
                                          @if($model->content_type=='Slider') checked @endif 
                                             class="form-check-input"><label
                                            for="inp-type-Bulk" class="form-check-label">Slider Only</label>
                                        </div>
                                    <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Bulk5" value="Video" 
                                          @if($model->content_type=='Video') checked @endif  class="form-check-input"><label
                                            for="inp-type-Bulk" class="form-check-label">Videos Only</label>
                                        </div>
                                        <div class="form-check form-check-inline"><input onchange="toggleContentSections(this.value)" type="radio"
                                            name="content_type" id="inp-type-Bulk6" value="Coupons" 
                                           @if($model->content_type=='Coupons') checked @endif 
                                             class="form-check-input"><label
                                            for="inp-type-Bulk" class="form-check-label">Coupons List Only</label>
                                        </div>
                                    
                                  
                                   
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                                   
                                        <div class="form-group"><label for="inp-section_title" class="">Section Display Title</label>
                                        <input type="text" name="section_title" id="inp-section_title" value="{{$model->section_title}}" class="form-control" placeholder="Enter section title"></div>
                        
                        
                              </div>
                           <div class="col-md-6 mb-3">
                                                   
                        <div class="form-group"><label for="inp-section_subtitle" class="">Section Display Subtitle</label>
                        <input type="text" name="section_subtitle" id="inp-section_subtitle" value="{{$model->section_subtitle}}" class="form-control" placeholder="Enter section subtitle"></div>
           
                        
                                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="form-label" for="product-title-input">Category <span
                                            class="text-danger">*</span></label>
    
                                    <select id="category_id" class="form-select" multiple id="choices-category-input"
                                        name="categories[]" onChange="showProductsonMultiCategorySelect()">
                                        {!! $category_options !!}
                                    </select>
    
                                </div>
                            </div>
                            <x-forms :data="$data" column='1' />
                            @if (count($repeating_group_inputs) > 0)
                              @foreach ($repeating_group_inputs as $grp)
                                        <x-repeatable :data="$grp['inputs']" :label="$grp['label']" :values="$model->{$grp['colname']}"
                                          :hide="$grp['hide']" :index="$loop->index" :indexWithModal="$grp['index_with_modal']" :modalInputBoxIdWhoseValueToSetInSelect="$grp['modalInputBoxIdWhoseValueToSetInSelect']" />
                                    @endforeach
                            @endif



                        </div>
                    </div>
                </div>
            </div>
           
      
    </div>

@endif
<div class="row mt-2">
    <div class="col-sm-12 " style="text-align:right">
        @php
            $r = 'Submit';
        @endphp
        {!! Form::submit($r)->id(strtolower($module) . '_btn')->primary() !!}
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</div>
{!! Form::close() !!}