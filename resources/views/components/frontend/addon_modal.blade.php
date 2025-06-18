@props(['addon', 'showQtyOption'])
<div class="product product-single fade mfp-hide" style="max-width:300px;padding:20px;" id="addonModal{{ $addon->id }}"
    tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $addon->name }} Variants</h5>
            </div>
            <div class="modal-body" x-data>
                <div class="">
                    @foreach ($addon->variants as $item)
                        <div class="d-flex justify-content-start">
                            <div class="d-flex form-check m-1"
                                style="justify-content:space-around;align-items:baseline;min-width:150px;">
                                <input type="checkbox" class="form-check-input"
                                    @input="addProductAddonWithVariant('{!! $addon->id !!}', '{!! $addon->name !!}', '{!! $item->sale_price !!}','{!! $item->id !!}','{!! $item->name !!}')"
                                    id="{{ $item['name'] }}" name="addon_items" value="{{ $item['price'] }}" />
                                <p><strong>{{ $item['name'] }}</strong></p>
                                <p><span
                                        style="color:red;font-size:10px;font-weight:bold">{{ getCurrency() }}{{ $item['sale_price'] }}</span>
                                </p>


                            </div>
                            <div style="width:20px">&nbsp;</div>
                            @if ($showQtyOption != 'Yes')
                                <input class="form-control bg-white"
                                    style="border-radius:0px;width:85px;min-height:20px;height:30px!important"
                                    type="number" value="1"
                                    @input.stop="addProductAddonWithVariant('{!! $addon->id !!}', '{!! $addon->name !!}', '{!! $item->sale_price !!}','{!! $item->id !!}','{!! $item->name !!}',true)">
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>
