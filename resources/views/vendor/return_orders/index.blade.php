@extends('layouts.vendor.app')
@section('content')
    <div class="container-xxl flex-grow-1 ">

      
        <!--Modal end-->
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between flex-wrap">
                    <h5>Return Shipments</h5>
                  
                </div>
                <br>
                <div class="d-flex justify-content-between flex-wrap ">
                   <x-filter :data="$filterable_fields" />
                    <x-search :searchableFields="$searchable_fields" />

                </div>




            </div>
            <div class="card-body">
                <x-alert/>
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                               <th>#
                                   <input  class="form-check-input" type="checkbox" id="check_all"  />

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
                            @include('admin.vendors.return_shipment_page')
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
