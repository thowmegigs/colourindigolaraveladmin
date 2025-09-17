@extends('layouts.admin.app')
@section('content')
    <div class="container-fluid">
<div id="crud_modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">


                        <button type="button" class="btn btn-icon btn-outline-primary">
                            <span class="tf-icons bx bx-edit"></span>
                        </button> &nbsp;&nbsp;<h5 class="modal-title text-primary" id="modal-title">Add
                            {{ properSingularName($plural_lowercase) }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="spinner-border text-muted"></div>
                        
                    </div>
                    <div class="modal-footer">
                          
                    </div>
                </div>

            </div>
        </div>
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between flex-wrap">
                    <h5>All {{ isset($role)?$role.'s':'Customers' }}</h5>
                   
                </div>
                <br>
                <div class="d-flex justify-content-between flex-wrap">

                    <div class="d-flex flex-wrap justify-content-between " style="align-items: start;max-width:660px; ">

                        <x-filter :data="$filterable_fields" />
                    </div>
                    <x-search :searchableFields="$searchable_fields" />
                </div>



            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                @foreach ($table_columns as $t)
                                    @if ($t['sortable'] == 'Yes')
                                        <x-row column="{{ $t['column'] }}"
                                            label="{{ str_replace(' Id', '', $t['label']) }}" />
                                    @else
                                        <th>{{ str_replace(' Id', '', $t['label']) }}</th>
                                    @endif
                                @endforeach
                              
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="tbody">
                            @include('admin.' . $plural_lowercase . '.page')
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
