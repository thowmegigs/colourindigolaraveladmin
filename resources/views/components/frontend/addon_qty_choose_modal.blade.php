@props(['addon'])
<div class="mfp-hide" style="max-width:200px;padding:20px;" id="addonQtyModal{{ $addon->id }}"
    tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $addon->name }} Qty</h5>
            </div>
            <div class="modal-body" x-data>
                <div class="">
                    <input class="form-control bg-white"
                                    style="border-radius:0px;min-height:20px;height:30px!important"
                                    type="number" value="1"
                     @input.stop="addProductAddon('{!! $addon->id !!}', '{!! $addon->name !!}', '{!! $addon->sale_price !!}',true)">
                       
                </div>

            </div>
        </div>
    </div>
</div>
