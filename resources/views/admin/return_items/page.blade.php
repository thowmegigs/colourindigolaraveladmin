@php
    $table_columns1 = array_column($table_columns, 'column');
@endphp

@if ($list->total() > 0)
    @php
        $i = $list->perPage() * ($list->currentPage() - 1) + 1;
        $l = 0;
    @endphp

    @foreach ($list as $r)
        @php
            $deleteurl = domain_route($plural_lowercase . '.destroy', [\Str::singular($plural_lowercase) => $r->id]);
            $viewurl = domain_route($plural_lowercase . '.show', [\Str::singular($plural_lowercase) => $r->id]);
            $base_src = asset('storage/returns/');
        @endphp

        <tr id="row-{{ $r->id }}">
            <td>
                {{ $i++ }}
                <!-- <input name="ids[]" class="form-check-input" type="checkbox" value="{{ $r->id }}" /> -->

                {{-- Modal --}}
                <div class="modal fade approve_modal"  id="exampleModal{{ $r->id }}" tabindex="-1"  aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="dynamicModalLabel">Verify Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body" id="dynamicModalBody" style="height:400px;overflow:auto">
                                <div class="table-responsive">
                                   <table class="table table-bordered table-striped table-hover">
                                    <tbody>
                                        <tr>
                                            <th>Product Name</th>
                                            <td>{{ \Str::limit($r->order_item->product->name,50) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Variant Name</th>
                                            <td>{{$r->order_item->variant->name}}</td>
                                        </tr>
                                        <tr>
                                            <th>Category</th>
                                            <td>{{ $r->order_item->product->category->name }}</td>
                                        </tr>
                                          <tr>
                                            <th>Images</th>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @if($r->first_image)
                                                        <a href="{{ $base_src.'/'.$r->first_image }}" data-lightbox="images">
                                                            <img src="{{ $base_src.'/'.$r->first_image }}" style="width:100px;height:100px" />
                                                        </a>
                                                    @endif
                                                    @if($r->second_image)
                                                        <a href="{{ $base_src.'/'.$r->second_image }}" data-lightbox="images">
                                                            <img src="{{ $base_src.'/'.$r->second_image }}" style="width:100px;height:100px" />
                                                        </a>
                                                    @endif
                                                    @if($r->third_image)
                                                        <a href="{{ $base_src.'/'.$r->third_image }}" data-lightbox="images">
                                                            <img src="{{ $base_src.'/'.$r->third_image }}" style="width:100px;height:100px" />
                                                        </a>
                                                    @endif
                                                    @if($r->fourth_image)
                                                        <a href="{{ $base_src.'/'.$r->fourth_image }}" data-lightbox="images">
                                                            <img src="{{ $base_src.'/'.$r->fourth_image }}" style="width:100px;height:100px" />
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Order Date</th>
                                            <td>{{ formateDate($r->order->created_at) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Return Request Date</th>
                                            <td>{{ formateDate($r->created_at) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Refund Method</th>
                                            <td>{{ $r->refund_method }}</td>
                                        </tr>
                                       
                                        <tr>
                                            <th>Qty</th>
                                            <td>{{ $r->order_item->quantity }}</td>
                                        </tr>
                                         <tr>
                                            <th>Refund Status</th>
                                            <td>{{ $r->refund_status }}</td>
                                        </tr>
                                         <tr>
                                            <th>Refundable Amt</th>
                                            <td>{{ $r->refund_amount }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reject Reason (If any)</th>
                                            <td>{{ $r->reject_reason }}</td>
                                        </tr>
                                     
                                      
                                    </tbody>
                                </table>
                            </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                             @if($r->return_shipment)
                                  @if($r->return_shipment?->is_transferred=='No')
                                        @if($r->status=='Pending' || $r->status=='Rejected')
                                        <button class="btn btn-sm btn-success approve-btn" data-status="Approved" data-id="{{ $r->id }}">Approve</button>
                                        @endif
                                            @if($r->status=='Pending' || $r->status=='Approved')
                                        <button class="btn btn-sm btn-danger approve-btn" data-status="Rejected"  data-id="{{ $r->id }}">Reject</button>
                                       @endif
                                @endif
                            @else 
                                  @if($r->status=='Pending' || $r->status=='Rejected')
                                        <button class="btn btn-sm btn-success approve-btn" data-status="Approved" data-id="{{ $r->id }}">Approve</button>
                                        @endif
                                            @if($r->status=='Pending' || $r->status=='Approved')
                                        <button class="btn btn-sm btn-danger approve-btn" data-status="Rejected"  data-id="{{ $r->id }}">Reject</button>
                                @endif
                             @endif
                            </div>
                        </div>
                    </div>
                </div>
            </td>

            @foreach ($table_columns1 as $t)
                @php ++$l; @endphp

                @if (str_contains($t, 'order_id'))
                    <td>{{ $r->order?->uuid }}/{{ $r->order_item?->vendor_id }}</td>
                @elseif (str_contains($t, 'product'))
                    <td>{{ \Str::limit($r->order_item?->product?->name, 20) }}</td>
                @elseif (str_contains($t, 'exchange_variant'))
                    <td>{{ $r->exchange_variant?->name }}</td>
                @elseif (str_contains($t, 'variant'))
                    <td>{{ $r->order_item?->variant?->name }}</td>
                @elseif (str_contains($t, '_at') || str_contains($t, 'date'))
                    <td>{{ formateDate($r->{$t}) }}</td>
                @elseif (isFieldPresentInRelation($model_relations, $t) >= 0)
                    <td>{{ getForeignKeyFieldValue($model_relations, $r, $t) }}</td>
                @else
                    <td class="text-start">{{ $r->{$t} }}</td>
                @endif
            @endforeach

            <td>
                <a type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal{{ $r->id }}">
                   View More
                </a>
            </td>
        </tr>
    @endforeach

    <tr>
        <td colspan="7">{!! $list->links() !!}</td>
    </tr>
@else
    <tr>
        <td colspan="{{ count($table_columns) + 1 }}" align="center">No records</td>
    </tr>
@endif

{{-- Global Modal --}}
<div id="{{ strtolower($module) }}_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
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
