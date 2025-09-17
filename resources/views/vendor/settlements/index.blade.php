@extends('layouts.vendor.app')
@section('content')
    <div class="container-fluid">

      
        <!-- Modal --->
     
        <!--Modal end-->
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between flex-wrap">
                    <h5>All Settlements</h5>
                   
                </div>
               
                <div class="d-flex justify-content-between flex-wrap mt-3">
                    <x-groupButtonIndexPage :whichButtonsToHideArray="['trash','bulk']" :filterableFields="$filterable_fields" :pluralLowercase="$plural_lowercase" :bulkUpdate="$bulk_update" :moduleTableName="$module_table_name" />

                  

                </div>




            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                               <th>#
                                   
                                </th>
                                @foreach ($table_columns as $t)
                                    @if ($t['sortable'] == 'Yes')
                                        <x-row column="{{ $t['column'] }}" label="{{ $t['label'] }}" />
                                    @else
                                        <th>{{ $t['label'] }}</th>
                                    @endif
                                @endforeach
                              <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="tbody">
                            @include('vendor.settlements.page')
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="hidden_page" id="hidden_page"
                    value="{{ !empty($_GET['page']) ? $_GET['page'] : '1' }}" />
                <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="" />
                <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
                <input type="hidden" name="search_by" id="search_by" value="" />

            </div>
        </div>
    </div>
@endsection
