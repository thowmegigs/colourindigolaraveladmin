@extends('layouts.vendor.app')
@section('content')
    <div class="container-xxl flex-grow-1">

     
        <div class="card">
            <div class="card-header pb-1">
                <div class="d-flex justify-content-between flex-wrap">
                    <h5>All {{ properPluralName($plural_lowercase) }}</h5>
                  
                </div>
             
                <div class="d-flex justify-content-between flex-wrap mt-2">
                  
                    <x-groupButtonIndexPage :whichButtonsToHideArray="['bulk','trash']" :filterableFields="$filterable_fields" :pluralLowercase="$plural_lowercase" :bulkUpdate="$bulk_update" :moduleTableName="$module_table_name" />

                    <x-search :searchableFields="$searchable_fields" />
</div>
                    <div class="d-flex justify-content-start mt-3 mb-2">
   @php
    $currentStatus = request()->get('delivery_status')??'Ordered'; // Get current status from URL
@endphp

<ul class="nav nav-tabs" id="orderStatusTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $currentStatus == 'Ordered' ? 'active text-white' : 'text-black' }}"
           style="{{ $currentStatus == 'Ordered' ? 'background-color:#ba1654; border-color:#dee2e6 #dee2e6 #fff;' : '' }}"
           href="{{ domain_route('vendor_orders', ['delivery_status' => 'Ordered']) }}">
            New Orders
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $currentStatus == 'APPROVED' ? 'active text-white' : 'text-black' }}"
           style="{{ $currentStatus == 'APPROVED' ? 'background-color:#ba1654; border-color:#dee2e6 #dee2e6 #fff;' : '' }}"
           href="{{ domain_route('vendor_orders', ['delivery_status' => 'APPROVED']) }}">
            Confirmed
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $currentStatus == 'DELIVERED' ? 'active text-white' : 'text-black' }}"
           style="{{ $currentStatus == 'DELIVERED' ? 'background-color:#ba1654; border-color:#dee2e6 #dee2e6 #fff;' : '' }}"
           href="{{ domain_route('vendor_orders', ['delivery_status' => 'DELIVERED']) }}">
            Delivered
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $currentStatus == 'CANCELLED' ? 'active text-white' : 'text-black' }}"
           style="{{ $currentStatus == 'CANCELLED' ? 'background-color:#ba1654; border-color:#dee2e6 #dee2e6 #fff;' : '' }}"
           href="{{ domain_route('vendor_orders', ['delivery_status' => 'CANCELLED']) }}">
            Cancelled
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ empty($currentStatus) ? 'active text-white' : 'text-black' }}"
           style="{{ empty($currentStatus) ? 'background-color:#ba1654; border-color:#dee2e6 #dee2e6 #fff;' : '' }}"
           href="{{ domain_route('vendor_orders') }}">
            All
        </a>
    </li>
</ul>

</div>


                </div>




            </div>
            <div class="card-body py-1 my-1">
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
                            @include('vendor.orders.page')
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
     <div class="modal fade" id="dynamicModal" tabindex="-1" aria-labelledby="dynamicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2 border-0">
                    <h5 class="modal-title" id="dynamicModalLabel">Order Items Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-1 border-0" id="dynamicModalBody">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="awbModal" tabindex="-1" aria-labelledby="dynamicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="dynamicModalLabel">Add Awb Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body border-0">
                    <form>
                        <div class="form-group">
                            <label for="exampleInputEmail">AWB Code</label>
                            <input type="text" id="awb" class="form-control" placeholder="Enter Awb Code">
                            <input type="hidden" id="inp_vendor_order_id" class="form-control">
                        </div>
                        <!-- <div class="form-group">
                    <label for="exampleInputEmail">Shipment Id</label>
                    <input type="text" id="inp_shipment_id" class="form-control"  placeholder="Enter Shipmet id ">
                  
                </div> -->
                        <button type="button" class="btn btn-primary mt-3" onClick="submitme()">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
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
            $(document).on('click', '.open-awb-modal', function() {
                const vendor_order_id = $(this).data('vendor_order_id');



                $('#inp_vendor_order_id').val(vendor_order_id);



            });
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

        function submitme() {
            const awb = $('#awb').val()
            const id = $('#inp_vendor_order_id').val()
            // const shipment_id=$('#inp_shipment_id').val()
            if (awb.length === 0)
                return
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
                    id
                },
                success: function(response) {
                    Swal.close()
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Both Updated successfully'
                        });
                    } else
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
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