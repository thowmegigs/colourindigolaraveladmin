function deleteAccordian(target, id) {
    let parent_item = $(target).closest('.accordion-item')

    if (id !== undefined) {
        const success = () => {
            parent_item.remove()
           
        }
        objectAjaxNoLoaderNoAlert(
            { id, table: 'product_variants' },
            '/deleteRecordFromTable',
            callbackSuccess = success,
            callbackError = undefined,
            method = "POST",
            show_error_in_alert = true
        )
    }
    else {
        parent_item.remove()
    }
}
function approveStatusForVendorOrder(id,val) {
   const success=(res)=>{
location.reload()
   }
        objectAjaxNoLoaderNoAlert(
            { id, table: 'vendor_orders',field:'is_approved_by_vendor',val:val },
            '/singleFieldUpdateFromTable',
            callbackSuccess = success,
            callbackError = undefined,
            method = "POST",
            show_error_in_alert = true
        )
   
}
function updateRefundStatus(id,status) {
   const success=(res)=>{
location.reload()
   }
        objectAjaxNoLoaderNoAlert(
            { id, table: 'return_items',field:'refund_status',val:status },
            '/singleFieldUpdateFromTable',
            callbackSuccess = success,
            callbackError = undefined,
            method = "POST",
            show_error_in_alert = true
        )
   
}



function show(v, event) {
    let sel = event.target
    let option_name_seleted = sel.options[sel.selectedIndex].text
    let t = $(event.target).closest('.row');

    if (option_name_seleted !== 'Color') {
        $(t).find('.p').first().html(`<div class="form-group">
     <label class="form-label">Value</label>
     <div>
         <input  class="form-control attribute_values" name="value-${v}" data-role="tagsinput" />
     </div>
 </div>`)
    }
    else {
        const color_val = $(sel).data('select')
        let c = color_val.split(',');
       

        let str = '';
        c.forEach(function (v) {
              let split = v.split('==');
                let col_name=split[0]
                let col_hex=split[1]

            str += `<option value='${col_name}' style="background:${col_hex}">${col_name}</option>`;

        })

        $(t).find('.p').first().html(`<div class="form-group">
     <label class="form-label">Value</label>
     <div>
        <select class="form-control attribute_values select_tag" multiple name="value-${v}[]" >
          ${str}
        </select>
     </div>
 </div>`)
        $('.select_tag').select2({
            
            placeholder: "Search colors to add ",
            allowClear: true,
            minimumResultsForSearch: Infinity
        }).on('change', function () {
            // Get the selected values

            generateVariant();
        });
    }

    initTaggedInput();
}
function getCategoryProductFeature(cat_id) {


    let callback = function (res) {

        $('#category_based_product_features').html(res['features']);

    };
    obj = { id: cat_id };
    objectAjaxNoLoaderNoAlert(obj, '/admin/get_category_based_product_features', callback);

}
function onLoadEditCoupon() {
    var discount_method = $('input[name="discount_method"]:checked').val();
    toggleForDiscountMethod(discount_method);
    var coupon_type = $('input[name="type"]:checked').val();
    toggleDiscountRuleDiv(coupon_type);
}
function onLoadContentSection() {
    var discount_method = $('input[name="content_type"]:checked').val();
    toggleContentSections(discount_method);

}
function toggleContentSections(value) {

    $('#section_header_imge').closest('.col-md-6').hide();
    $('#inp-slider_id').closest('.col-md-6').hide();
    $('#inp-website_slider_id').closest('.col-md-6').hide();
    $('#inp-website_banner_id').closest('.col-md-6').hide();
    $('#category_id').closest('.col-md-6').hide();
    $('#inp-product_ids').closest('.col-md-6').hide();
    $('#inp-coupon_ids').closest('.col-md-6').hide();
   // $('#inp-vidoe_id').closest('.col-md-6').hide();
    $('#inp-collection_ids').closest('.col-md-6').hide();
    $('#background_color').closest('.col-md-6').hide();
    $('#inp-banner_id').closest('.col-md-6').hide();
    $('#inp-website_banner_id').closest('.col-md-6').hide();

    $('#section_header_imge').closest('.col-md-6').hide();
    $('#inp-section_subtitle').closest('.col-md-6').hide();
    $('#inp-display-Horizontal').closest('.col-md-6').hide();
    $('#inp-no_of_items').closest('.col-md-6').hide();
   // $('#inp-header_image').closest('.col-md-6').hide();
    $('#inp-slider_id').closest('.col-md-6').hide();
    $('#inp-website_slider_id').closest('.col-md-6').hide();
    $('#section_background_color').closest('.col-md-6').hide();
    $('#inp-vidoe_id').closest('.col-md-6').hide();
    if (value == 'Banner') {

        $('#inp-banner_id').closest('.col-md-6').show()
        $('#inp-website_banner_id').closest('.col-md-6').show()


    }
    else if (value == 'Slider') {
        $('#inp-slider_id').closest('.col-md-6').show();
        $('#inp-website_slider_id').closest('.col-md-6').show();
    }
    else if (value == 'Categories') {

        $('#category_id').closest('.col-md-6').show();
        $('#inp-no_of_items').closest('.col-md-6').show();

        $('#background_color').closest('.col-md-6').show();
        $('#inp-section_subtitle').closest('.col-md-6').show();
        $('#inp-display-Horizontal').closest('.col-md-6').show();

        $('#inp-header_image').closest('.col-md-6').show();

    }
    else if (value == 'Collections') {

    $('#section_header_imge').closest('.col-md-6').show();
        $('#inp-collection_ids').closest('.col-md-6').show();
        $('#inp-no_of_items').closest('.col-md-6').show();
        $('#background_color').closest('.col-md-6').show();
        $('#inp-section_subtitle').closest('.col-md-6').show();
        $('#inp-display-Horizontal').closest('.col-md-6').show();
        $('#inp-header_image').closest('.col-md-6').show();

    }
    else if (value == 'Products') {
        
    $('#section_header_imge').closest('.col-md-6').show();
        $('#category_id').closest('.col-md-6').show();
        $('#inp-coupon_ids').closest('.col-md-6').hide();
        $('#inp-product_ids').closest('.col-md-6').show();
        $('#background_color').closest('.col-md-6').show();
        $('#inp-section_subtitle').closest('.col-md-6').show();
        $('#inp-display-Horizontal').closest('.col-md-6').show();
        $('#inp-header_image').closest('.col-md-6').show();
        $('#inp-no_of_items').closest('.col-md-6').show();
    }
    else if (value == 'Coupons') {

        $('#inp-coupon_ids').closest('.col-md-6').show();

    $('#section_header_imge').closest('.col-md-6').show();
        $('#inp-section_subtitle').closest('.col-md-6').show();
        $('#inp-header_image').closest('.col-md-6').show();
        $('#inp-no_of_items').closest('.col-md-6').show();
    }
    else if (value == 'Video') {
  
    $('#inp-vidoe_id').closest('.col-md-6').show();
    $('#inp-section_subtitle').closest('.col-md-6').show();
    $('#inp-header_image').closest('.col-md-6').show();

    }
}
function toggleForDiscountMethod(value) {
    if (value == 'Coupon Code') {
        $('#inp-coupon_code').closest('.form-group').show();
        $('#inp-details').closest('.form-group').show();
        $('#inp-customer_usage_limit').closest('.form-group').show();
        $('#inp-total_usage_limit').closest('.form-group').show();

    }
    else {
        $('#inp-coupon_code').closest('.form-group').hide();

        $('#inp-details').closest('.form-group').hide();
        $('#inp-customer_usage_limit').closest('.form-group').hide();
        $('#inp-total_usage_limit').closest('.form-group').hide();
    }
}
function toggleDiscountRuleDiv(value) {

    if (value == 'Individual Quantity') {
        $('#quantity_rule').show();


        $('#buy_products').hide();

        $('#get_products').hide();

        $('#inp-maximum_discount_limit').closest('.form-group').hide();
        $('#inp-discount').closest('.form-group').hide();
        $('#inp-discount_type').closest('.form-group').hide();
        $('#category_id').closest('.col-md-12').show();
        $('#inp-product_id').closest('.col-md-6').show();
    }
    else if (value == 'Cart') {
        $('#quantity_rule').hide();
        $('#buy_products').hide();
        $('#get_products').hide();

        $('#inp-maximum_discount_limit').closest('.form-group').show();
        $('#inp-discount').closest('.form-group').show();
        $('#inp-discount_type').closest('.form-group').show();
        $('#category_id').closest('.col-md-12').show();
        $('#inp-product_id').closest('.col-md-6').show();

    }
    else if (value == 'BOGO') {


        $('#buy_products').show();
        $('#get_products').show();


        $('#quantity_rule').hide();
        $('#category_id').closest('.col-md-12').hide();
        $('#inp-product_id').closest('.col-md-6').hide();


        $('#inp-maximum_discount_limit').closest('.form-group').hide();
        $('#inp-discount').closest('.form-group').hide();
        $('#inp-discount_type').closest('.form-group').hide();
    }
    else if (value == 'Bulk' || value == 'Shipping') {
        $('#quantity_rule').hide();
        $('#buy_products').closest('.form-group').hide();
        $('#get_products').closest('.form-group').hide();

        $('#inp-maximum_discount_limit').closest('.form-group').hide();
        $('#inp-discount').closest('.form-group').show();
        $('#inp-discount_type').closest('.form-group').show();
        $('#category_id').closest('.col-md-12').show();
        $('#inp-product_id').closest('.col-md-6').show();
    }

}
function toggleCollectionTypeDisplay(value) {

    if (value == 'Manual') {
        $('#conditions').hide();

        $('#category_id').closest('.col-md-12').show();
        $('#inp-product_id').closest('.col-md-6').show();
    }
    else {
        $('#conditions').show();

        $('#category_id').closest('.col-md-12').hide();
        $('#inp-product_id').closest('.col-md-6').hide();
    }

}
function fetchFeeStructureRow(id) {
    obj = {
        id,
    };
    $("#here").empty();
    fetchHtmlContent(obj, "here", "/admin/getFeeStructureRow");
}
function showProductsonMultiCategorySelect() {

    let values = $('#category_id').val();
    $("#inp-product_ids").select2("destroy");
    $("#inp-product_ids").select2({
        dropdownParent: $("#crud_modal"), placeholder: "Search prducts2",
        minimumInputLength: 3,
        ajax: {
            delay: 250,

            url: "/search_products",
            dataType: 'json',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // important!
            },
            data: function (params) {
                return {
                    q: params.term,                // search term
                    category_ids: JSON.stringify(values) // dynamically pulled from another field
                };
            },
            processResults: function (data) {
                console.log("data", data);
                return {
                    results: data.message,
                };
            },
        },
    });
}

function someInitOnAnyPopupOpen() {
    const myModalEl = document.getElementById("myModal");
    const bulkUpdateModal = document.getElementById("bulk_update_modal");

    const filterModal = document.getElementById("filter_modal");
    const jsonModal = document.getElementById("json_modal");
    const invoiceModal = document.getElementById("invoice_modal");
    const accept_payment_modal = document.getElementById(
        "accept_payment_modal"
    );

    if (myModalEl) {
        myModalEl.addEventListener("shown.bs.modal", (event) => {
            applySelect2("select", true, "myModal");
        });
    }
    if (bulkUpdateModal) {
        bulkUpdateModal.addEventListener("shown.bs.modal", (event) => {
            applySelect2("select", true, "bulk_update_modal");
        });
    }

    if (jsonModal) {
        // jsonModal.addEventListener("shown.bs.modal", (event) => {
        //     applySelect2("select", true, "json_modal");
        //     flatpickr("input[type='date']");
        // });
    }
    if (accept_payment_modal) {
        accept_payment_modal.addEventListener("shown.bs.modal", (event) => {
            applySelect2("select", true, "accept_payment_modal");
            initializeFormAjaxSubmitAndValidation();
        });
    }
    if (invoiceModal) {
        invoiceModal.addEventListener("shown.bs.modal", (event) => {
            applySelect2("select", true, "invoice_modal");
            flatpickr("input[type='date']");
        });
    }
    if (filterModal) {
        filterModal.addEventListener("shown.bs.modal", (event) => {
            applySelect2("select", true, "filter_modal");
            flatpickr("input[type='date']"); /**tfor date inpit */
        });
    }
    var myDropdown = document.getElementById("filter");

    if (myDropdown) {
        myDropdown.addEventListener("shown.bs.dropdown", function () {

            applySelect2("select", true, "filter");
        });
    }

}

function inilizeEvents() {
    if ($("#filter").length > 0) {
        $("#filter").on("hide.bs.dropdown", function (e) {
            if (e.clickEvent) {
                e.preventDefault();
            }
        });
    }

    //applySelect2("select", false);
    initiateSelect2ChangeEvents(false);
    someInitOnAnyPopupOpen();

    if ($("#image").length > 0) {
        $("#image").on("change", function () {
            multiImagePreview(this, "gallery1");
        });
    }
    if ($("#inp-image").length > 0) {
        $("#inp-image").on("change", function () {
            /***always take for single image filed name image ,here inp is aapended automatically to image id */
            singleImagePreview(this, "gallery1");
        });
    }
    if ($("#inp-password").length > 0) {
        $("#inp-password").keyup(function (event) {
            var password = $("#password").val();
            checkPasswordStrength(password);
        });
    }

    $("input[name=has_variant]").on("change", function (v) {
        $("#add_variant").toggle();
    });
}



$(document).ready(function () {

    if ($("form").length > 0) initializeFormAjaxSubmitAndValidation();
    onLoadEditCoupon();
    onlyPageLoadInit();



});
function initialiseSortingOnTable() {
    let g = $('#sortable-table');
    if (g.length > 0) {

        // Initialize sortable only once

        if (typeof $.ui === 'undefined') {
            // If jQuery UI is not loaded, dynamically load it
            var script = document.createElement('script');
            script.src = "https://code.jquery.com/ui/1.13.2/jquery-ui.min.js";
            script.onload = function () {

                g.sortable({
                    items: "tr",
                    cursor: 'move',
                    axis: 'y',
                    update: function () {
                        $('#sortable-table tr').each(function (index) {
                            if ($(this).data('sequence') !== undefined) {
                                $(this).data('sequence', (index + 1));
                            }
                        });
                        var newOrder = [];
                        $('#sortable-table tr').each(function () {
                            if ($(this).data('sequence') !== undefined) {
                                var rowId = $(this).data('id');  // assuming each row has a unique data-id attribute
                                var sequence = $(this).data('sequence');
                                newOrder.push({ id: rowId, sequence: sequence });
                            }

                        });
                        const table = $('#sortable-table').data('table');
                       console.log('table',table)
                        $.ajax({
                            url: '/update_order_sequence',  // Laravel route to handle the update
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),  // CSRF token
                                order: newOrder, table
                            },
                            success: function (response) {
                                // Handle success response
                                console.log(response);
                            },
                            error: function (xhr, status, error) {
                                // Handle error response
                                console.log(error);
                            }
                        });
                    }
                });


            };
            document.head.appendChild(script);

            // Load the jQuery UI CSS as well
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css';
            document.head.appendChild(link);
        }



    }
}


function onlyCrudPopupRelatedInit(module, modal, modal_id) {
    initialiseSummernote();
    applySelect2("select", (in_popup = true), modal_id);
    initiateSelect2ChangeEvents(true, modal_id);
    flatpickr("input[type='date']");

    initFilePreviewEvent();
    initTaggedInput()
    showToggableDivOnLoadIfPresent();
    //initializeModalFormValidation(module, bsOffcanvas);
    initializeModalFormValidation(module, modal);

    initMultiSelectMoveTo();
    onLoadContentSection();
    if ($('#repeater-container').length > 0)
        initialiseFormRepeater();

    if ($('input[name=collection_type]').length > 0) {

        let checked_val = $('input[name=collection_type]:checked').val();

        if (checked_val == 'Manual') {
            $('#conditions').hide();
            $('#category_id').closest('.col-md-12').show();
            $('#inp-product_id').closest('.col-md-6').show();
        }
        else
            $('#conditions').show();
        $('#category_id').closest('.col-md-12').hide();
        $('#inp-product_id').closest('.col-md-6').hide();
    }

}
function onlyPageLoadInit() {
    applySelect2("select", false);
    flatpickr("input[type=date]");
    initTaggedInput()
    hideShowToggleClassHavingFormControl()
    initialiseSummernote();
    inilizeEvents(); /***isi mein opup or modal or dropdown related innit hai */
    initialiseSortingOnTable();
    showToggableDivOnLoadIfPresent();
    initFilePreviewEvent();
    initMultiSelectMoveTo()
    $('.select_tag').select2({
        tags: true,
        placeholder: "Select colors",
        allowClear: true
    }).on('change', function () {


        generateVariant();
    });


    if ($('input[name=collection_type]').length > 0) {
        let checked_val = $('input[name=collection_type]:checked').val();

        if (checked_val == 'Manual') {
            $('#conditions').hide();
            $('#category_id').closest('.col-md-12').show();
            $('#inp-product_id').closest('.col-md-6').show();
        }
        else
            $('#conditions').show();
        $('#category_id').closest('.col-md-12').hide();
        $('#inp-product_id').closest('.col-md-6').hide();
    }

}

