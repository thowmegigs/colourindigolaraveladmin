 {!! Form::open()->put()->route(route_prefix().$plural_lowercase . '.update', [\Str::singular($plural_lowercase) => $model->id])->id(strtolower($module) . '_form') !!}

   
            <x-forms :data="$data" column='1' />
           
            <div id="repeater-container">
            @if(isset($model))
                    @foreach($model->images as $index => $item)
                        <div class="repeater-item border row p-3 mb-3 position-relative">
                                <div class="col">
                                <div class="mb-3">
                                    <label for="name[]" class="form-label">Image</label>
                                    <input type="hidden" name="existing_ids[]" value="{{ $item->id ?? '' }}">
                                    <input type="file" name="images[]"
                                     class="form-control" >
                                        <x-singleFile 
                                            :fileName="$item->name"
                                            modelName="WebsiteBannerImage" 
                                            folderName="website_banners" 
                                            fieldName="name"
                                            :rowid="$item->id"
                                            />
                                  
                                </div></div>
                                <div class="col">
                                <div class="mb-3">
                                    <label for="collection[]" class="form-label">Collections</label>
                                    <select name="collection_id[]" class="form-select select2" >
                                            <option value="">Select Colection</option>
                                        @foreach($collections as $col)
                                        <option value="{{$col->id}}" @if($item->collection_id && $item->collection_id==$col->id) selected @endif>{{$col->name}}</option>
                                        @endforeach
                                        </select>
                                </div>
                                </div>
                                <button type="button" class="btn btn-icon btn-danger btn-sm remove-item position-absolute top-0 end-0 m-2"><i class="mdi mdi-minus"></i></button>
                            </div>
                    
                    @endforeach
                @else
                    <div class="repeater-item border row p-3 mb-3 position-relative">
                        <div class="col">
                        <div class="mb-3">
                            <label for="name[]" class="form-label">Image</label>
                            <input type="file" name="images[]" class="form-control" required>
                        </div></div>
                        <div class="col">
                        <div class="mb-3">
                            <label for="collection[]" class="form-label">Collections</label>
                            <select name="collection_id[]" class="form-select select2" >
                                    <option value="">Select Colection</option>
                                   @foreach($collections as $col)
                                   <option value="{{$col->id}}">{{$col->id}}</option>
                                   @endforeach
                                </select>
                        </div>
                        </div>
                        <button type="button" class="btn btn-icon btn-danger btn-sm remove-item position-absolute top-0 end-0 m-2 d-none"><i class="mdi mdi-minus"></i></button>
                    </div>
                @endif
                </div>

                        <button type="button" id="addMore" class="btn-icon btn btn-secondary mb-2"><i class="mdi mdi-plus"></i></button>
                  



                        <div class="row mt-2">
 
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