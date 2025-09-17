@extends('layouts.admin.app')
@section('content')
    <div class="container-fluid flex-grow-1">


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
        <div class="card my-0">
            <div class="card-header">
                <div class="d-flex justify-content-between flex-wrap">
                    <h5>All {{ properPluralName($plural_lowercase) }}</h5>

                </div>

                <div class="d-flex justify-content-between flex-wrap mt-3">
                    <x-groupButtonIndexPage :filterableFields="$filterable_fields" :pluralLowercase="$plural_lowercase" :bulkUpdate="$bulk_update" :moduleTableName="$module_table_name"
                        :whichButtonsToHideArray="['trash']" />
                    <x-search :searchableFields="$searchable_fields" />

                </div>




            </div>
            <div class="card-body">
            <!-- Status Tabs -->
                <ul class="nav nav-tabs mb-3" id="statusTabs">
                    @php
                        $statuses = ['Ordered', 'APPROVED', 'CANCELLED', 'DELIVERED','ALL'];
                        $currentStatus = request('delivery_status', 'Ordered'); // Default is ORDERED
                    @endphp

                    @foreach ($statuses as $status)
                        <li class="nav-item">
                            <a class="nav-link {{ $currentStatus === $status ? 'active' : '' }}"
                            href="{{ $status!='ALL'?request()->fullUrlWithQuery(['delivery_status' => $status, 'page' => 1]):request()->url() }}">
                                {{ ucfirst(strtolower($status=='Ordered'?'New Order':$status)) }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="table-responsive text-nowrap">
                    <x-alert />
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#
                                    <input class="form-check-input" type="checkbox" id="check_all" />

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
                            @php

                                $table_columns1 = array_column($table_columns, 'column');
                            @endphp
                            @if ($list->total() > 0)
                                @php
                                    $i = $list->perPage() * ($list->currentPage() - 1) + 1;
                                    $l = 0;
                                @endphp
                                @foreach ($list as $r)
                                    <tr id="row-{{ $r->id }}">
                                        <td>
                                            {{ $i++ }}
                                            <input name="ids[]" class="form-check-input" type="checkbox"
                                                value="{{ $r->id }}" />
                                            <br />
                                            <a style="cursor:pointer;text-decoration:underline!important"
                                                data-vendor_id="{{ $r->vendor_id }}" data-order_id="{{ $r->order_id }}"
                                                data-bs-toggle="modal" data-bs-target="#exampleModal"
                                                class=" open-ajax-modal">View Items</a><br />
                                            @if (auth()->user() && $r->is_completed=='No')
                                                <a 
                                                    data-vendor_order_id="{{ $r->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#awbModal" class="m-1 btn btn-primary btn-sm  open-awb-modal">Add Awb</a><br />

                                                <!-- Add Shipping Charge Link -->
                                                <a 
                                                    data-vendor_order_id="{{ $r->id }}"
                                                    data-current_shipping="{{ $r->shipping_cost }}" data-bs-toggle="modal"
                                                    data-bs-target="#shippingModal" class="m-1 btn btn-info btn-sm open-shipping-modal">Update
                                                    Shipping</a>
                                            @endif

                                        </td>
                                        @foreach ($table_columns1 as $t)
                                            @php   ++$l;@endphp
                                            @if (str_contains($t, 'delivery_status'))
                                                <td>{{ getFriendlyShipmentStatus($r->delivery_status) }}</td>
                                            @elseif(str_contains($t, 'shiprocket_order_id'))
                                                <td>{{ $r->shiprocket_order_id }}</td>
                                            @elseif(str_contains($t, 'net_profit'))
                                                <td>
                                                    <table class="table table-bordered table-sm" style="max-width: 400px;">
                                                        <tbody>
                                                            <tr>
                                                                <th>Sales Total</th>
                                                                <td>{{ getCurrency() }}{{ $r->vendor_total }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Refund</th>
                                                                <td>-{{ getCurrency() }}{{ $r->refunded_amount }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Delivery Charge</th>
                                                                <td id="shipping-cost-{{ $r->id }}">
                                                                    -{{ getCurrency() }}{{ $r->shipping_cost }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Commission</th>
                                                                <td>-{{ getCurrency() }}{{ $r->commission_total }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Net Profit</th>
                                                                <td><strong
                                                                        id="net-profit-{{ $r->id }}">{{ getCurrency() }}{{ $r->vendor_total - ($r->refunded_amount + $r->shipping_cost + $r->commission_total) }}</strong>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                </td>
                                            @elseif(str_contains($t, '_at') || str_contains($t, 'date'))
                                                <td>{{ formateDate($r->{$t}) }}</td>
                                            @elseif(isFieldPresentInRelation($model_relations, $t) >= 0)
                                                @if (
                                                    $r->{$t} &&
                                                        (preg_match("/image$/", $t) ||
                                                            preg_match("/_image$/", $t) ||
                                                            preg_match("/_doc$/", $t) ||
                                                            preg_match("/_file$/", $t) ||
                                                            preg_match("/_pdf$/", $t)))
                                                    <td>

                                                        <x-singleFile :fileName="$r->{$t}" :modelName="$module" :folderName="$storage_folder"
                                                            :fieldName="$t" :rowid="$r->id" />
                                                    </td>
                                                @elseif(preg_match("/images$/", $t) ||
                                                        preg_match("/_images$/", $t) ||
                                                        preg_match("/_docs$/", $t) ||
                                                        preg_match("/_files$/", $t) ||
                                                        preg_match("/_pdfs$/", $t))
                                                    <td>
                                                        <!-- here image list is list of table row in object form *****-->

                                                        <x-showImages :row=$r :fieldName=$t :storageFolder=$storage_folder
                                                            :tableName="getTableNameFromImageFieldList(
                                                                $image_field_names,
                                                                $t,
                                                            )" />
                                                    </td>
                                                @else
                                                    <td>{{ getForeignKeyFieldValue($model_relations, $r, $t) }}</td>
                                                @endif
                                            @elseif(isFieldPresentInRelation($model_relations, $t) < 0 &&
                                                    $r->{$t} &&
                                                    (preg_match("/image$/", $t) ||
                                                        preg_match("/_image$/", $t) ||
                                                        preg_match("/_doc$/", $t) ||
                                                        preg_match("/_file$/", $t) ||
                                                        preg_match("/_pdf$/", $t)))
                                                <td>

                                                    <x-singleFile :fileName="$r->{$t}" :modelName="$module" :folderName="$storage_folder"
                                                        :fieldName="$t" :rowid="$r->id" />
                                                </td>
                                            @elseif(isFieldPresentInRelation($model_relations, $t) < 0 &&
                                                    (preg_match("/images$/", $t) ||
                                                        preg_match("/_images$/", $t) ||
                                                        preg_match("/_docs$/", $t) ||
                                                        preg_match("/_files$/", $t) ||
                                                        preg_match("/_pdfs$/", $t)))
                                                <td>
                                                    <!-- here image list is list of table row in object form *****-->

                                                    <x-showImages :row=$r :fieldName=$t :storageFolder=$storage_folder
                                                        :tableName="getTableNameFromImageFieldList(
                                                            $image_field_names,
                                                            $t,
                                                        )" />
                                                </td>
                                            @else
                                                <td class="text-start">

                                                    @php
                                                        if (!is_numeric($r->{$t})) {
                                                            $tr = json_decode($r->{$t}, true);

                                                            $by_json_key = isset($table_columns[$l - 1]['by_json_key'])
                                                                ? $table_columns[$l - 1]['by_json_key']
                                                                : 'id';
                                                            if ($tr !== null) {
                                                                $hide_columns = isset(
                                                                    $table_columns[$l - 1]['hide_columns_in_json_view'],
                                                                )
                                                                    ? $table_columns[$l - 1][
                                                                        'hide_columns_in_json_view'
                                                                    ]
                                                                    : [];
                                                                if (
                                                                    !empty($repeating_group_inputs) &&
                                                                    in_array(
                                                                        $t,
                                                                        array_column(
                                                                            $repeating_group_inputs,
                                                                            'colname',
                                                                        ),
                                                                    )
                                                                ) {
                                                                    if (count($hide_columns) > 0) {
                                                                        $tr = array_map(function ($v) use (
                                                                            $hide_columns,
                                                                        ) {
                                                                            foreach ($hide_columns as $col) {
                                                                                unset($v[$col]);
                                                                            }
                                                                            return $v;
                                                                        }, $tr);
                                                                    }
                                                                    if (
                                                                        isset(
                                                                            $table_columns[$l - 1][
                                                                                'show_json_button_click'
                                                                            ],
                                                                        )
                                                                    ) {
                                                                        if (
                                                                            $table_columns[$l - 1][
                                                                                'show_json_button_click'
                                                                            ]
                                                                        ) {
                                                                            echo showArrayInColumn(
                                                                                $tr,
                                                                                $l,
                                                                                $by_json_key,
                                                                            );
                                                                        } else {
                                                                            echo showArrayInColumnNotButtonForm(
                                                                                $tr,
                                                                                $l,
                                                                                $by_json_key,
                                                                            );
                                                                        }
                                                                    } else {
                                                                        echo showArrayInColumn($tr, $l, $by_json_key);
                                                                    }
                                                                } else {
                                                                    if (!isPlainArray($tr)) {
                                                                        echo showArrayWithNamesOnly($tr);
                                                                    } else {
                                                                        echo $r->{$t};
                                                                    }
                                                                }
                                                            } else {
                                                                echo $r->{$t};
                                                            }
                                                        } else {
                                                            echo $r->{$t};
                                                        }

                                                    @endphp
                                                </td>
                                            @endif
                                        @endforeach
                                        <td>
                                            <div class="d-flex flex-column gap-2">
                                                @if (is_null($r->shiprocket_shipment_id))
                                                    @if (!auth()->id())
                                                        <!-- @if ($r->is_approved_by_vendor == 'No')
    -->
                                                        <button type="button"
                                                            onClick="approveStatusForVendorOrder({!! $r->id !!},'Yes')"
                                                            class="btn btn-success btn-sm btn-outline">Approve </button>
                                                        <!--
@else
    -->
                                                        <button type="button"
                                                            onClick="approveStatusForVendorOrder({!! $r->id !!},'No')"
                                                            class=" open-ajax-modal btn btn-danger btn-sm btn-outline">Reject
                                                        </button>
                                                        <!--
    @endif -->
                                                    @else
                                                        {{ $r->is_approved_by_vendor == 'Yes' ? 'Vendor Accepted' : '' }}
                                                    @endif
                                                @else
                                                    @if ($r->awb) 
                                                    @if(str_contains($r->courier_name,'AMAZON'))
                                                        <button class="btn btn-sm btn-success generate-doc"
                                                            data-id="{{ $r->shiprocket_order_id }}"
                                                            data-type="invoice">Invoice</button>
                                                    @else
                                                       @if($r->invoice_pdf)
                                                      <a href="{{asset('storage/invoices/'.$r->invoice_pdf)}}" class="btn btn-sm btn-success"
                                                            >Invoice</a>
                                                       @endif
                                                    @endif

                                                        <a href="/label/{{ $r->id }}"
                                                            class="btn btn-sm btn-primary">Label</a>

                                                        <!-- <button class="btn btn-sm btn-info generate-doc"
                            data-id="{{ $r->shiprocket_shipment_id }}"
                            data-type="manifest">Manifest</button> -->
                                                    @endif
                                                @endif



                                        </td>


                                    </tr>
                                @endforeach
                                <td colspan='7'>{!! $list->appends(request()->except('page'))->links() !!}
</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="{{ count($table_columns) + 1 }}" align="center">No records</td>
                                </tr>
                            @endif
                            <div id="{{ strtolower($module) }}_modal" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">

                                            <h4 class="modal-title">View {{ $module }}</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <div class="spinner-border text-muted"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

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

    <!-- Order Items Detail Modal -->
    <div class="modal fade" id="dynamicModal" tabindex="-1" aria-labelledby="dynamicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dynamicModalLabel">Order Items Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="dynamicModalBody">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- AWB Modal -->
    <div class="modal fade" id="awbModal" tabindex="-1" aria-labelledby="awbModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="awbModalLabel">Add Awb Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="awb">AWB Code</label>
                            <input type="text" id="awb" class="form-control" placeholder="Enter Awb Code">
                            <input type="hidden" id="inp_vendor_order_id" class="form-control">
                        </div>
                        <button type="button" class="btn btn-primary mt-3" onClick="submitAwb()">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipping Charge Update Modal -->
    <div class="modal fade" id="shippingModal" tabindex="-1" aria-labelledby="shippingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shippingModalLabel">Update Shipping Charge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="shippingForm">

                        <div class="form-group mb-3">
                            <label for="new_shipping" class="form-label">New Shipping Charge </label>
                            <input type="number" id="new_shipping" class="form-control"
                                placeholder="Enter new shipping charge" step="0.01" min="0" >
                            <div class="invalid-feedback" id="shipping-error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="refund" class="form-label">Refund </label>
                            <input type="number" id="refund" class="form-control"
                                placeholder="Enter refund" >
                            <div class="invalid-feedback" id="refund-error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="new_shipping" class="form-label">Invoice File <span
                                    class="text-danger">*</span></label>
                            <input type="file" id="invoice_file" class="form-control"
                                placeholder="Enter new shipping charge" >
                            <div class="invalid-feedback" id="shipping-error"></div>
                        </div>

                        <input type="hidden" id="vendor_order_id">
                    </form> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitShipping">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Update Shipping
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Existing code for order items modal
            $(document).on('click', '.open-ajax-modal', function() {
                const vendor_id = $(this).data('vendor_id');
                const order_id = $(this).data('order_id');

                Swal.fire({
                    title: 'Loading...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{!! domain_route('vendor_order_detail') !!}',
                    type: 'POST',
                    data: {
                        order_id,
                        vendor_id
                    },
                    success: function(response) {
                        Swal.close();
                        $('#dynamicModalBody').html(response);
                        const modal = new bootstrap.Modal(document.getElementById(
                            'dynamicModal'));
                        modal.show();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Could not load row details.'
                        });
                        console.error(xhr.responseText);
                    }
                });
            });

            // Existing code for AWB modal
            $(document).on('click', '.open-awb-modal', function() {
                const vendor_order_id = $(this).data('vendor_order_id');
                $('#inp_vendor_order_id').val(vendor_order_id);
                $('#awb').val(''); // Clear previous value
            });

            // NEW: Shipping charge modal functionality
            $(document).on('click', '.open-shipping-modal', function() {
                const vendor_order_id = $(this).data('vendor_order_id');
                const current_shipping = $(this).data('current_shipping') || 0;

                $('#vendor_order_id').val(vendor_order_id);

                $('#new_shipping').val('');

                $('#shipping-error').text('');
                $('#new_shipping').removeClass('is-invalid');
            });

            // NEW: Submit shipping charge update
           $(document).on('click', '#submitShipping', function() {
    const btn = $(this);
    const spinner = btn.find('.spinner-border');

    const vendor_order_id   = $('#vendor_order_id').val();
    const new_shipping      = $('#new_shipping').val();
    const new_refund        = $('#refund').val(); // assuming you have a refund input
    const invoice_file      = $('#invoice_file')[0].files[0]; // assuming input type="file" with id="invoice"

    // Validation
    if (!new_shipping || parseFloat(new_shipping) < 0) {
        $('#new_shipping').addClass('is-invalid');
        $('#shipping-error').text('Please enter a valid shipping charge');
        return;
    }

    $('#new_shipping').removeClass('is-invalid');
    $('#shipping-error').text('');

    // Show loading state
    btn.prop('disabled', true);
    spinner.removeClass('d-none');

    // Prepare FormData for file upload
    let formData = new FormData();
    formData.append('vendor_order_id', vendor_order_id);
    formData.append('shipping_charge', parseFloat(new_shipping).toFixed(2));
    if (new_refund) formData.append('refund', parseFloat(new_refund).toFixed(2));
    if (invoice_file) formData.append('invoice', invoice_file);
    formData.append('_token', '{{ csrf_token() }}');

    $.ajax({
        url: '/vendor_order_update', // updated endpoint
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            btn.prop('disabled', false);
            spinner.addClass('d-none');

            if (response.data) {
                // Update the UI
                const currency = '{{ getCurrency() }}';
                $('#shipping-cost-' + vendor_order_id).text('-' + currency + parseFloat(response.data.shipping_charge).toFixed(2));
                if (response.data.refund !== null) {
                    $('#refund-' + vendor_order_id).text(currency + parseFloat(response.data.refund).toFixed(2));
                }

                // Close modal
                $('#shippingModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Vendor order updated successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update vendor order'
                });
            }
        },
        error: function(xhr) {
            btn.prop('disabled', false);
            spinner.addClass('d-none');

            let errorMessage = 'Failed to update vendor order';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                errorMessage = xhr.responseText;
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        }
    });
});




            // Existing generate document functionality
            $(document).on('click', '.generate-doc', function() {
                const btn = $(this);
                const id = btn.data('id');
                const type = btn.data('type');

                btn.prop('disabled', true).text('Generating...');

                $.post('{!! domain_route('generate_vendor_doc') !!}', {
                    id,
                    type: type,
                    _token: '{{ csrf_token() }}'
                }).done(function(response) {
                    console.log(response);
                    try {
                        window.open(response.url, '_blank');
                    } catch (e) {
                        console.error("Window open failed:", e);
                    }
                }).fail(function(res) {

                    alert(res.responseJSON.error);
                }).always(function() {
                    btn.prop('disabled', false).text(type.charAt(0).toUpperCase() + type.slice(1));
                });
            });

        });

        // Renamed function to be more specific
        function submitAwb() {
            const awb = $('#awb').val()
            const id = $('#inp_vendor_order_id').val()

            if (awb.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please enter AWB code'
                });
                return;
            }

            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/singleFieldUpdateFromTable',
                type: 'POST',
                data: {
                    table: 'vendor_orders',
                    val: awb,
                    field: 'awb',
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.close()
                    if (response.success) {
                        $('#awbModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'AWB updated successfully!'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close()
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: xhr.responseText
                    });
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
@endpush
