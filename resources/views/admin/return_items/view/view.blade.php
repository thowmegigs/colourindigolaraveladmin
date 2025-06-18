@extends('layouts.admin.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ">
       

        <div class="row">
            <!-- Basic Layout -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">View {{ properSingularName($plural_lowercase) }}</h5>
                    </div>

                    <div class="card-body">
                      
<table class="table table-bordered" id="view_table">
    <tbody>

        @php
            $l = 0;
            $columns = array_column($view_columns, 'column');
        @endphp
        @foreach ($columns as $t)
            @php
                ++$l;
                $storage_folder =$storage_folder=='products'?$storage_folder.'/'.$row->id:$storage_folder;
            @endphp

            <tr>
                <th class="text-start">{{ properSingularName($view_columns[$l - 1]['label']) }}</th>
                 @if (str_contains($t, 'order_uid'))
                    <td>
                        {{$row->order?->uuid}}
                        <a class="btn btn-outlined btn-primary btn-sm" href="{{$view_columns[$loop->index]['link']}}">View Order</a>
                    </td>
                @elseif (str_contains($t, 'product'))
                    <td class="text-start">
                        {{$row->order_item?->product?->name}}
                    </td>
                     @elseif (str_contains($t, 'exchange_variant'))
                    <td class="text-start">
                        {{$row->exchange_variant?->name}}
                    </td>
                @elseif (str_contains($t, 'variant'))
                    <td class="text-start">
                        {{$row->order_item?->variant?->name}}
                    </td>
               
                @elseif(str_contains($t, '_at') || str_contains($t, '_date'))
                    <td class="text-start">{{ formateDate($row->{$t}) }}</td>
                @elseif(!empty($viewColumns[$l - 1]['link']))
                    <td class="text-start"><a target="" href="{{ $viewColumns[$l - 1]['link'] }}">View Detail</a></td>
                @elseif(isFieldPresentInRelation($model_relations, $t) >= 0)
                    @if (
                        $row->{$t} &&
                            (preg_match("/image$/", $t) ||
                                preg_match("/_image$/", $t) ||
                                preg_match("/_doc$/", $t) ||
                                preg_match("/_file$/", $t) ||
                                preg_match("/_pdf$/", $t)))
                        <td class="text-start">

                            <x-singleFile :fileName="$row->{$t}" :modelName="$module" :folderName="$storage_folder" :fieldName="$t"
                                :rowid="$row->id" />
                        </td>
                    @elseif(preg_match("/images$/", $t) ||
                            preg_match("/_images$/", $t) ||
                            preg_match("/_docs$/", $t) ||
                            preg_match("/_files$/", $t) ||
                            preg_match("/_pdfs$/", $t))
                        <td class="text-start">
                            @php
                                $inline1 = 'false';
                                if (isset($viewColumns[$l - 1]['inline_images'])) {
                                    if ($viewColumns[$l - 1]['inline_images']) {
                                        $inline1 = 'true';
                                    } else {
                                        $inline1 = 'false';
                                    }
                                }
                            @endphp
                            <x-showImages :inline="$inline1" :row=$row :fieldName=$t :storageFolder=$storage_folder
                                :tableName="getTableNameFromImageFieldList($image_field_names, $t)" />
                        </td>
                    @else
                        <td class="text-start">
                            {{ getForeignKeyFieldValue($model_relations, $row, $t) }}
                        </td>
                    @endif
                @elseif(isFieldPresentInRelation($model_relations, $t) < 0 &&
                        $row->{$t} &&
                        (preg_match("/image$/", $t) ||
                            preg_match("/_image$/", $t) ||
                            preg_match("/_doc$/", $t) ||
                            preg_match("/_file$/", $t) ||
                            preg_match("/_pdf$/", $t)))
                    <td class="text-start">
                       
                        <x-singleFile :fileName="$row->{$t}" :modelName="$module" :folderName="$storage_folder" :fieldName="$t"
                            :rowid="$row->id" />
                    </td>
                @elseif(isFieldPresentInRelation($model_relations, $t) < 0 &&
                        (preg_match("/images$/", $t) ||
                            preg_match("/_images$/", $t) ||
                            preg_match("/_docs$/", $t) ||
                            preg_match("/_files$/", $t) ||
                            preg_match("/_pdfs$/", $t)))
                    <td class="text-start">

                        @php
                            $inline1 = 'false';
                            if (isset($viewColumns[$l - 1]['inline_images'])) {
                                if ($viewColumns[$l - 1]['inline_images']) {
                                    $inline1 = 'true';
                                } else {
                                    $inline1 = 'false';
                                }
                            }
                        @endphp
                        <x-showImages :inline="$inline1" :row=$row :fieldName=$t :storageFolder=$storage_folder
                            :tableName="getTableNameFromImageFieldList($image_field_names, $t)" />
                    </td>
                @else
                    <td class="text-start">
                        @php
                          
                                if (is_array(json_decode($row->{$t}, true))) {
                                    $tr = json_decode($row->{$t}, true);

                                    $by_json_key = isset($viewColumns[$l - 1]['by_json_key']) ? $viewColumns[$l - 1]['by_json_key'] : 'id';
                                    if ($tr !== null) {
                                        $hide_columns = isset($viewColumns[$l - 1]['hide_columns_in_json_view']) ? $viewColumns[$l - 1]['hide_columns_in_json_view'] : [];
                                        if (!empty($repeatingGroupInputs) && in_array($t, array_column($repeatingGroupInputs, 'colname'))) {
                                           
                                            if (count($hide_columns) > 0) {
                                                $tr = array_map(function ($v) use ($hide_columns) {
                                                    foreach ($hide_columns as $col) {
                                                        unset($v[$col]);
                                                    }
                                                    return $v;
                                                }, $tr);
                                            }
                                            if (isset($viewColumns[$l - 1]['show_json_button_click'])) {
                                                if ($viewColumns[$l - 1]['show_json_button_click']) {
                                                    echo showArrayInColumn($tr, $l, $by_json_key);
                                                } else {
                                                    
                                                    echo showArrayInColumnNotButtonForm($tr, $l, $by_json_key);
                                                }
                                            } else {
                                               
                                                echo showArrayInColumn($tr, $l, $by_json_key);
                                            }
                                        } else {
                                            if (!isPlainArray($tr)) {
                                                echo showArrayWithNamesOnly($tr);
                                            } else {
                                                echo $row->{$t};
                                            }
                                        }
                                    } else {
                                        echo $row->{$t};
                                    }
                                } else {
                                    echo $row->{$t};
                                }
                            

                        @endphp

                    </td>
                @endif
            </tr>
        @endforeach

    </tbody>
</table> 
                    </div><br>
                </div>
            </div>
        </div>
    </div>
@endsection
