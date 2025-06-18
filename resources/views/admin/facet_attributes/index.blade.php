@extends('layouts.admin.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasEndLabel" class="offcanvas-title" style="text-transform:capitalize;">
                    {{ properPluralName($plural_lowercase) }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0">
                <p class="text-center">Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out
                    print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century
                    who is thought to have scrambled parts of Cicero's De Finibus Bonorum et Malorum for use in a type
                    specimen book.</p>
                <button type="button" class="btn btn-primary mb-2 d-grid w-100">Continue</button>
                <button type="button" class="btn btn-label-secondary d-grid w-100"
                    data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </div>
        <!-- Modal --->
        <div id="crud_modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">

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
        <!--Modal end-->
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between flex-wrap">
                    <h5>All {{ properPluralName($plural_lowercase) }}</h5>
                    <div class="d-flex">

                        <div class="btn-group" role="group" aria-label="Basic example">
                            @if (auth()->user()->hasRole(['Admin']) ||
                                    auth()->user()->can('create_' . $plural_lowercase))
                                @if ($show_crud_in_modal)
                                    <button class="rounded-0  btn btn-primary" type="button"
                                        onclick="load_form_modal('{!! $module !!}','{!! domain_route($plural_lowercase . '.create') !!}','{!! $crud_title !!}','Add')"
                                        aria-controls="offcanvasEnd"> <i class="bx bx-plus-circle"
                                            style="margin-top:-3px"></i>
                                        Add New</button>
                                @else
                                    <button class="rounded-0  btn btn-primary" type="button"
                                        onclick="load_form_offcanvas('{!! $module !!}','{!! domain_route($plural_lowercase . '.create') !!}','{!! $crud_title !!}','Add')"
                                        aria-controls="offcanvasEnd"> <i class="bx bx-plus-circle"
                                            style="margin-top:-3px"></i> Add New</button>
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
                                            href="{{ domain_route(strtolower($module) . '.export', ['type' => 'excel']) }}?{{ http_build_query($_GET) }}"><span><i
                                                    class="bx bx-printer me-2"></i>XLS</span></a>
                                        <a class="dropdown-item"
                                            href="{{ domain_route(strtolower($module) . '.export', ['type' => 'csv']) }}?{{ http_build_query($_GET) }}"><span><i
                                                    class="bx bx-file me-2"></i>CSV</span></a>


                                    </li>

                                </ul>
                            @endif

                        </div>
                    </div>
                </div>
                <br>
                <div class="d-flex justify-content-between flex-wrap mt-3">
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
                                        <x-row column="{{ $t['column'] }}" label="{{ $t['label'] }}" />
                                    @else
                                        <th>{{ $t['label'] }}</th>
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
@push('scripts')

<script>
$(document).ready(function () {
    // Open modal and reset form or load existing data
    $('#crud_modal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget); // Button that triggered the modal
        const modal = $(this);
        const mode = button.data('mode') || 'add'; // Default to add
        const categoryId = button.data('category-id');
        const attributes = button.data('attributes') || [];

        // Reset the form
       // modal.find('#facetAttributeForm')[0].reset();
        modal.find('#attributeRepeater').empty();

        // Set category if provided
        if (categoryId) {
            modal.find('#category_id').val(categoryId);
        }

        // Populate attributes if in edit mode
        if (mode === 'edit' && attributes.length > 0) {
            attributes.forEach(attr => {
                modal.find('#attributeRepeater').append(`
                    <div class="repeater-item mb-2 d-flex">
                        <input type="text" name="attributes[]" class="form-control me-2" value="${attr}" required>
                        <button type="button" class="btn btn-danger remove-btn">Remove</button>
                    </div>
                `);
            });
        } else {
            // Add one empty row by default
            modal.find('#attributeRepeater').append(`
                <div class="repeater-item mb-2 d-flex">
                    <input type="text" name="attributes[]" class="form-control me-2" placeholder="Attribute Name" required>
                    <button type="button" class="btn btn-danger remove-btn">Remove</button>
                </div>
            `);
        }
       $(document).on('click', '#addAttributeBtn', function () {
          
                    $('#attributeRepeater').append(`
                        <div class="repeater-item mb-2 d-flex">
                            <input type="text" name="attributes[]" class="form-control me-2" placeholder="Attribute Name" required>
                            <button type="button" class="btn btn-danger remove-btn">Remove</button>
                        </div>
                    `);
            });

    // Remove item
    $(document).on('click', '.remove-btn', function () {
        $(this).closest('.repeater-item').remove();
    });

    $(document).on('submit', '#facetAttributeForm', function (e) {
   
        e.preventDefault();
      
        Swal.fire({
            title: 'Saving...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: "{{ domain_route('facet_attributes.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: response.message
                });

                $('#facetAttributeModal').modal('hide');
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Something went wrong.'
                });
            }
        });
    });
    });

    // Add new repeater item
    
});
</script>
@endpush