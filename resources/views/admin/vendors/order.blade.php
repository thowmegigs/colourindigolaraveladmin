@extends('layouts.admin.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

     
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
                  
                </div>
                <br>
                <div class="d-flex justify-content-between flex-wrap mt-3">
                    @if(auth()->id())
                       <div class="d-flex flex-wrap justify-content-between " style="align-items: start;max-width:660px; ">
                      @php 
                      $bulk_update_columns = json_decode($bulk_update, true);
                      @endphp
                        @if (!empty($bulk_update_columns))
                                <div class="" id="bulk_update">
                                    <button type="button" class="rounded-0 btn btn-outline-success me-1" data-bs-toggle="modal"
                                        data-bs-target="#bulk_update_modal">
                                        <i class="bx bx-edit"></i>&nbsp;&nbsp;Bullk Update
                                    </button>
                                    <div id="bulk_update_modal" class="modal fade" role="dialog">
                                        <div class="modal-dialog">

                                            <!-- Modal content-->
                                            <form id="bulk_form">
                                                <div class="modal-content">
                                                    <div class="modal-header">

                                                        <b>Bulk Update </b>

                                                    </div>
                                                    <div class="modal-body">

                                                        <div style="">
                                                            @foreach ($bulk_update_columns as $k => $val)
                                                                <b
                                                                    style="font-weight: 600;font-size: 13px;">{{ properSingularName($val['label']) }}</b>
                                                                @if (!isset($val['input_type']))
                                                                    <div class="form-group mb-2">
                                                                        <select class="form-control" name="{{ $k }}">
                                                                            <option value="">Select {{ $k }}</option>
                                                                            @foreach ($val['data'] as $p)
                                                                                <option value="{{ $p['id'] }}">

                                                                                    {{ $p['name'] }}
                                                                                </option>
                                                                            @endforeach

                                                                        </select>

                                                                    </div>
                                                                @else
                                                                    @if ($val['input_type'] == 'select')
                                                                        <div class="form-group mb-2">
                                                                            <select class="form-control" name="{{ $k }}">
                                                                                <option value="">Select {{ $k }}</option>
                                                                                @foreach ($val['data'] as $p)
                                                                                    <option value="{{ $p['id'] }}">

                                                                                        {{ $p['name'] }}
                                                                                    </option>
                                                                                @endforeach

                                                                            </select>

                                                                        </div>
                                                                    @elseif ($val['input_type'] == 'textarea')
                                                                        <div class="form-group mb-2">
                                                                            <textarea class="form-control" name="{{ $k }}">{{ $val['data'] }}</textarea>

                                                                        </div>
                                                                    @elseif ($val['input_type'] == 'text')
                                                                        <div class="form-group mb-2">
                                                                            <input class="form-control" name="{{ $k }}"
                                                                                value="{{ $val['data'] }}" />

                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <div class="form-group mt-2">

                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary rounded-0"
                                                            onClick="multiSelectCheckBoxAction('vendor_orders')">Submit</button>
                                                        <button type="button" class="btn btn-danger rounded-0"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                        @endif
                        <div id="bulk_delete" style="margin-right:5px;">
                            <button type="button" class="rounded-0 btn btn-outline-danger"
                                onclick="bulkDelete('vendor_orders')">
                                <i class="bx bx-trash"></i>&nbsp;&nbsp;Trash
                            </button>
                        </div>
                    @endif
                  <x-filter :data="$filterable_fields" />
                  

                    <x-search :searchableFields="$searchable_fields" />

                </div>




            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <x-alert/>
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
                <input name="ids[]" class="form-check-input" type="checkbox" value="{{ $r->id }}" />
               <br/><a style="text-decoration:underline!important" 
               
              data-vendor_id="{{$r->vendor_id }}"
              data-order_id="{{ $r->order_id }}"
              data-bs-toggle="modal" data-bs-target="#exampleModal" 
            
              class=" open-ajax-modal">View Items</a>

            </td>
            @foreach ($table_columns1 as $t)
                @php   ++$l;@endphp
            @if(str_contains($t, 'delivery_status'))
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
                            <th>Commission</th>
                            <td>-{{ getCurrency() }}{{ $r->commission_total }}</td>
                            </tr>
                            <tr>
                            <th>Net Profit</th>
                            <td><strong>{{ getCurrency() }}{{ $r->vendor_total - ($r->refunded_amount + $r->commission_total) }}</strong></td>
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

                            <x-singleFile :fileName="$r->{$t}" :modelName="$module" :folderName="$storage_folder" :fieldName="$t"
                                :rowid="$r->id" />
                        </td>
                    @elseif(preg_match("/images$/", $t) ||
                            preg_match("/_images$/", $t) ||
                            preg_match("/_docs$/", $t) ||
                            preg_match("/_files$/", $t) ||
                            preg_match("/_pdfs$/", $t))
                        <td>
                            <!-- here image list is list of table row in object form *****-->

                            <x-showImages :row=$r :fieldName=$t :storageFolder=$storage_folder :tableName="getTableNameFromImageFieldList($image_field_names, $t)" />
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

                        <x-singleFile :fileName="$r->{$t}" :modelName="$module" :folderName="$storage_folder" :fieldName="$t"
                            :rowid="$r->id" />
                    </td>
                @elseif(isFieldPresentInRelation($model_relations, $t) < 0 &&
                        (preg_match("/images$/", $t) ||
                            preg_match("/_images$/", $t) ||
                            preg_match("/_docs$/", $t) ||
                            preg_match("/_files$/", $t) ||
                            preg_match("/_pdfs$/", $t)))
                    <td>
                        <!-- here image list is list of table row in object form *****-->

                        <x-showImages :row=$r :fieldName=$t :storageFolder=$storage_folder :tableName="getTableNameFromImageFieldList($image_field_names, $t)" />
                    </td>
                @else
                    <td class="text-start">

                        @php
                            if (!is_numeric($r->{$t})) {
                                $tr = json_decode($r->{$t}, true);

                                $by_json_key = isset($table_columns[$l - 1]['by_json_key']) ? $table_columns[$l - 1]['by_json_key'] : 'id';
                                if ($tr !== null) {
                                    $hide_columns = isset($table_columns[$l - 1]['hide_columns_in_json_view']) ? $table_columns[$l - 1]['hide_columns_in_json_view'] : [];
                                    if (!empty($repeating_group_inputs) && in_array($t, array_column($repeating_group_inputs, 'colname'))) {
                                        if (count($hide_columns) > 0) {
                                            $tr = array_map(function ($v) use ($hide_columns) {
                                                foreach ($hide_columns as $col) {
                                                    unset($v[$col]);
                                                }
                                                return $v;
                                            }, $tr);
                                        }
                                        if (isset($table_columns[$l - 1]['show_json_button_click'])) {
                                            if ($table_columns[$l - 1]['show_json_button_click']) {
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
            <td><div class="d-flex flex-column gap-2">
            @if(is_null($r->shiprocket_shipment_id))
                @if(!auth()->id())
                     @if($r->is_approved_by_vendor=='No')
                        <button type="button" 
                        onClick="approveStatusForVendorOrder({!! $r->id !!},'Yes')"
                        
                        class="btn btn-success btn-sm btn-outline">Approve </button>
                        @else
                        <button type="button"
                        onClick="approveStatusForVendorOrder({!! $r->id !!},'No')" 
                            class=" open-ajax-modal btn btn-danger btn-sm btn-outline">Reject </button>
                        @endif
                @else 
                  {{$r->is_approved_by_vendor=='Yes'?"Vendor Accepted":""}}
                @endif
          
              @else
            <button class="btn btn-sm btn-success generate-doc"
                        data-id="{{ $r->shiprocket_order_id }}"
                        data-type="invoice">Invoice</button>
                        </div>
             @if($r->awb)
              <a href="/label/{{$r->id}}" class="btn btn-sm btn-primary"
                      >Label</a>

                <button class="btn btn-sm btn-info generate-doc"
                        data-id="{{ $r->shiprocket_shipment_id }}"
                        data-type="manifest">Manifest</button>
              @endif
              @endif
               
           
           
            </td>


        </tr>
    @endforeach
    <td colspan='7'>{!! $list->links() !!}</td>
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
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
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
@endsection
@push('scripts')
  <script>
$(document).ready(function () {
  $(document).on('click', '.open-ajax-modal', function () {
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
      url: '{!! domain_route("vendor_order_detail")!!}',
      type: 'POST',
      data:{order_id,vendor_id},
      success: function (response) {
        Swal.close();
        $('#dynamicModalBody').html(response);
        const modal = new bootstrap.Modal(document.getElementById('dynamicModal'));
        modal.show();
      },
      error: function (xhr) {
        Swal.fire({
          icon: 'error',
          title: 'Failed',
          text: 'Could not load row details.'
        });
        console.error(xhr.responseText);
      }
    });
  });
  $(document).on('click', '.generate-doc', function () {
    const btn = $(this);
    const id = btn.data('id');
    const type = btn.data('type');

    btn.prop('disabled', true).text('Generating...');

    $.post('{!! domain_route("generate_vendor_doc")!!}', {
        id,
        type: type,
        _token: '{{ csrf_token() }}'
    }).done(function (response) {
         console.log(response);
            try {
                window.open(response.url, '_blank');
            } catch (e) {
                console.error("Window open failed:", e);
            }
    }).fail(function (res) {
       
        alert(res.responseJSON.error);
    }).always(function () {
        btn.prop('disabled', false).text(type.charAt(0).toUpperCase() + type.slice(1));
    });
});

});

</script>
@endpush
