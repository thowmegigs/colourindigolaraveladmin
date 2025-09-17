 {!! Form::open()->put()->route(route_prefix().$plural_lowercase . '.update', [\Str::singular($plural_lowercase) => $model->id])->id(strtolower($module) . '_form') !!}

    <div class="row">
       
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <div class="card-text">
                            <x-forms :data="$data" column='1' />
                             <x-imageform :data="$data" column='2' />



                        </div>
                    </div>
                </div>
            </div>
          
     
    </div>

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