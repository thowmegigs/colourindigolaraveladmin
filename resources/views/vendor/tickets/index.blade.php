@extends('layouts.vendor.app')
@section('content')
@php 
$setting=\App\Models\Setting::first();
@endphp
<div class="container-xxl flex-grow-1 container-p-y">
  <div id="crud_modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">


                        <h5 class="modal-title text-primary" id="modal-title">Add
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
    <div class="row">
        <!-- Left Sidebar: WhatsApp & Call Support -->
        <div class="col-md-3 mb-4">
            <div class="d-flex flex-column gap-3">

                <!-- WhatsApp Support -->
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="mb-3 text-success" style="font-size: 2.5rem;">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <h6 class="card-title">WhatsApp</h6>
                        <p class="card-title">+91{{$setting->phone}}</p>
                        <p class="text-muted small">Chat with us instantly for quick support.</p>
                        <a href="https://wa.me/+91{{$setting->phone}}" target="_blank" class="btn btn-success w-100">
                            <i class="bi bi-whatsapp"></i> Start Chat
                        </a>
                    </div>
                </div>

                <!-- Call Support -->
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="mb-3 text-warning" style="font-size: 2.5rem;">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <h6 class="card-title">Phone Support</h6>
                         <p class="card-title">+91{{$setting->phone}}</p>
                        <p class="text-muted small">Call us during business hours for immediate help.</p>
                        <a href="tel:+91{{$setting->phone}}" class="btn btn-warning text-white w-100">
                            <i class="bi bi-telephone-fill"></i> Call Now 
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Side: Ticket Table -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between flex-wrap">
                        <h5>All {{ properPluralName($plural_lowercase) }}</h5>
                        <div class="d-flex">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                @if (auth()->guard('vendor')->user())
                                    @if ($show_crud_in_modal)
                                        <button class=" btn btn-danger" type="button"
                                            onclick="load_form_modal('{!! $module !!}','{!! domain_route($plural_lowercase . '.create') !!}','{!! $crud_title !!}','Add')">
                                            <i class="bx bx-plus-circle" style="margin-top:-3px"></i> Create Ticket
                                        </button>
                                    @else
                                        <button class=" btn btn-danger" type="button"
                                            onclick="load_form_offcanvas('{!! $module !!}','{!! domain_route($plural_lowercase . '.create') !!}','{!! $crud_title !!}','Add')">
                                            <i class="bx bx-plus-circle" style="margin-top:-3px"></i> Add New
                                        </button>
                                    @endif
                                @endif

                                @if ($has_export)
                                    <button type="button"
                                        class="rounded-0 dt-button buttons-collection btn btn-label-primary dropdown-toggle me-2"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <span><i class="bx bx-export me-sm-2"></i> <span
                                                class="d-none d-sm-inline-block">Export</span></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ domain_route(strtolower($module) . '.export', ['type' => 'excel']) }}?{{ http_build_query($_GET) }}">
                                                <i class="bx bx-printer me-2"></i>XLS
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ domain_route(strtolower($module) . '.export', ['type' => 'csv']) }}?{{ http_build_query($_GET) }}">
                                                <i class="bx bx-file me-2"></i>CSV
                                            </a>
                                        </li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between flex-wrap mt-3">
                        <x-groupButtonIndexPage :filterableFields="$filterable_fields" :whichButtonsToHideArray="['trash','bulk']" :pluralLowercase="$plural_lowercase" :bulkUpdate="$bulk_update" :moduleTableName="$module_table_name" />
                        <x-search :searchableFields="$searchable_fields" />
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th># </th>
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
                                @include('vendor.' . $plural_lowercase . '.page')
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
    </div>

</div>
@endsection
