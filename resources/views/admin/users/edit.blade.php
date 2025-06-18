@extends('layouts.admin.app')
@section('content')
    <div class="container-fluid">
        

        <div class="row">
            <!-- Basic Layout -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Edit {{ properSingularName($plural_lowercase) }}</h5>
                    </div>

                    <div class="card-body">
                        <!--modalable content-->
                        {!! Form::open()->put()->route(route_prefix().$plural_lowercase . '.update', [\Str::singular($plural_lowercase) => $model->id])->id(strtolower($module) . '_form')->attrs(['data-edit'=>1,'data-module' => $module]) !!}
                        <x-forms :data="$data" column='2' />

                        @if (count($repeating_group_inputs) > 0)
                            @foreach ($repeating_group_inputs as $grp)
                                <x-repeatable :data="$grp['inputs']" :label="$grp['label']" :values="$model->{$grp['colname']}" :index="$loop->index" />
                            @endforeach
                        @endif
                        <div id="toggle_div"></div>
                        <div class="row">
                            <div class="col-sm-10">
                                @php
                                    $r = 'Submit';
                                @endphp
                                {!! Form::submit($r)->id(strtolower($module) . '_btn')->primary() !!}
                            </div>
                        </div>


                        {!! Form::close() !!}



                        <!--modal ends here-->
                    </div><br>
                </div>
            </div>
        </div>
    </div>

@endsection
