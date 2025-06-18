<style>
    .coupon_box {
        border: 1px solid #eee;
        border-bottom: 2px solid green;
        border-radius: 2px;
        padding-top: 7px;
    }
    .details{
        font-size: 12px;
    padding: 11px 12px;
    border-top: 1px solid #d4e4d1;
    margin-top: 11px;
    }

    .text-green {
        font-weight: bold;
        color: green;
        text-align: center;
    }

    .coupon_box img {
        width: 25px;
        height: 25px
    }

    .coupon_title {
        color: rgb(3, 109, 19);
        font-size: 12px;
    }

    .valid_date {
        margin: 0;
        font-size: 10px;
        margin-left:13px;
    }

    .coupon_code {
        padding: 1px 2px;
        text-align: center;
        min-width: 80px;
        margin: 0;
        font-size: 12px;
        border: 2px dashed green;
        background: #e3ffe3;
        color: green
    }
</style>
<div class="mfp-hide"
    style="max-width: 540px;
background: white;
    padding: 10px 5px;
    box-shadow: 0px 0px 3px 0px #b4c0be;
    border-radius: 2px;"
    id="applied_offer_modal">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">
            <div class="modal-header" x-data>
                <h4 class="modal-title">Applied Offer(s)</h4>
            </div>
            <div class="modal-body" x-data style="max-height: 400px;
            overflow-y: auto;">

                <template x-for="p in $store.cart.applied_offers">
                    <div class="d-flex flex-column coupon_box" x-data="{
                        show: false,
                        is_copied: false,
                        copy(elem) {
                    
                            let copyText = elem;
                    
                            navigator.clipboard.writeText(copyText.textContent);
                    
                    
                            this.is_copied = true;
                        }
                    }">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex">
                                <img src="/front_assets/images/discount.png" />
                                <div class="d-flex flex-column">
                                    <b class="coupon_title mb-1 ml-3" x-text="p['coupon_name']"></b>
                                    <p class="valid_date">
                                        Valid From <b x-text="p['start_date']"></b> to <b x-text="p['end_date']">
                                        </b>
                                </div>
                            </div>
                            <template x-if="p['coupon_code'].length>0">
                                <div class="d-flex flex-column text-center">
                                    <p class="coupon_code" x-ref="code" x-text="p['coupon_code']">
                                    </p>
                                    <span style="font-size:10px;" @click="copy($refs.code)"
                                        :class="is_copied ? 'text-green' : ''"
                                        x-text="is_copied?'Copied':'Tap To Copy'"></span>


                                </div>
                            </template>
                        </div>
                        <template x-if="show">
                            <div class="details" x-html="p['coupon_details']"></div>
                        </template>
                        <p class="text-center" @click="show=!show"
                            style="margin-bottom:2px;font-size:11px;color:black;font-weight:bold;cursor:pointer"><span
                                x-text="!show?'Show':'Hide'"></span> Details &nbsp;&nbsp;&nbsp;<span
                                class="w-icon-angle-down"></i></p>



                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
