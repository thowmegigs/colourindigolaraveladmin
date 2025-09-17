{!! Form::open()->route(route_prefix().$plural_lowercase . '.store')->id(strtolower($module) . '_form')->multipart() !!}

    <x-forms :data="$data" column='1' />
    <div id="repeater-container">
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
                                   <option value="{{$col->id}}">{{$col->name}}</option>
                                   @endforeach
                                </select>
                        </div>
                        </div>
                        <button type="button" class="btn btn-icon btn-danger btn-sm remove-item position-absolute top-0 end-0 m-2 d-none"><i class="mdi mdi-minus"></i></button>
                    </div>
                </div>

                        <button type="button" id="addMore" class="btn-icon btn btn-secondary mb-2"><i class="mdi mdi-plus"></i></button>
                  



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
