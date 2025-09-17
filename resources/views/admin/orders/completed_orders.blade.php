@extends('layouts.admin.app')
@section('content')
    <div class="container-fluid">
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between flex-wrap">
                    <h5>Completed & Unsettled Orders</h5>
                   
                </div>
                <br>
                <div class="d-flex justify-content-between flex-wrap">

                    <x-groupButtonIndexPage :filterableFields="$filterable_fields" :pluralLowercase="$plural_lowercase" :bulkUpdate="$bulk_update" :moduleTableName="$module_table_name" />

                    <x-search :searchableFields="$searchable_fields" />
                </div>



            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#
                                   <input  class="form-check-input" type="checkbox" id="check_all"  />

                                </th>
                                @foreach ($table_columns as $t)
                                    @if ($t['sortable'] == 'Yes')
                                        <x-row column="{{ $t['column'] }}"
                                            label="{{ str_replace(' Id', '', $t['label']) }}" />
                                    @else
                                        <th>{{ str_replace(' Id', '', $t['label']) }}</th>
                                    @endif
                                @endforeach
                               
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="tbody">
                            @include('admin.orders.completed_order_page')
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
