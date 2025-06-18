

var cart_btn_html = "";


var additional_product_cost_aprt_from_sale_price = 0;
function showLoader(btnid) {
    let cart_btn_html = $('#' + btnid).html();
    $('#' + btnid).html(`<img src='commonjs/loader.gif' width="20" height="20" style="display:inline-block"/>`);

}
function showLoginModal() {

    $('#loginModal').modal('show');

}
function incrementCounter(pid, variant_id = undefined, show_success_alert = true) {
    let url = baseurl + "/addToCart";
    console.log('input#qty-' + pid + variant_id);
    let cur_qty = $('input#qty-' + pid + variant_id).val();

    let new_qty = parseInt(cur_qty) + 1;
    let callback = function (res) {
        // hideLoader('add_'+product_id);
        $('input#qty-' + pid).val(new_qty);
        $('#cart_dropdown').html(res['minicart_html']);
        if ($('#btn_cart_count_' + pid).length > 0) {
            $('#btn_cart_count_' + pid).text(res['current_product_count']);
        }
    };
    obj = { product_id: pid, qty: new_qty, variant_id };
    objectAjaxWithBtnAndLoader((btnid = undefined), obj, url, callback, undefined, show_success_alert);
}
function decrementCounter(pid, variant_id = undefined, show_success_alert = true,) {
    let url = baseurl + "/addToCart";

    let cur_qty = $('input#qty-' + pid + variant_id).val();
    if (cur_qty > 0) {

        let new_qty = parseInt(cur_qty) - 1;
        let callback = function (res) {

            // hideLoader('add_'+product_id);
            $('input#qty-' + pid).val(new_qty);
            $('#cart_dropdown').html(res['minicart_html']);
            if ($('#btn_cart_count_' + pid).length > 0) {
                $('#btn_cart_count_' + pid).text(res['current_product_count']);
            }
        };
        obj = { product_id: pid, qty: new_qty, variant_id };
        objectAjaxWithBtnAndLoader((btnid = undefined), obj, url, callback, undefined, show_success_alert);
    }
}
function hideLoader(btnid) {
    //let h=$('#'+btnid).html();
    $('#' + btnid).html(cart_btn_html);

}
function addToCart(name, product_id, price, sale_price, sgst = 0, cgst = 0, igst = 0, unit, variant_id = undefined, cart_session_id = undefined) {
    let qty = 1;

    let url = baseurl + "/addToCart";

    let callback = function (res) {
        // hideLoader('add_'+product_id);
        $('#cart_count').text(res['cart_count']);
        $('#cart_dropdown').html(res['minicart_html']);
        if (!variant_id) {
            $('#btn-container-' + product_id).html(`<div class="container" style="width:134px;" id="counter-${product_id}" >
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <button class="btn btn-primary btn-sm" id="minus-btn"
                    onclick="decrementCounter('${product_id}')">&minus;</button>
            </div>
            <input type="number" id="qty-${product_id}" class="text-center form-control form-control-sm"
                value="${qty}" min="1">
            <div class="input-group-append">
                <button class="btn btn-primary btn-sm" id="plus-btn"
                    onclick="incrementCounter('${product_id}')">&plus;</button>
            </div>
        </div>
    </div>`);
        }
        else {
            $('#btn-container-variant-' + variant_id).html(`<div class="container" style="width:134px;" id="variant-counter-${variant_id}" >
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <button class="btn btn-primary btn-sm" id="minus-btn"
                    onclick="decrementCounter('${product_id}','${variant_id}')">&minus;</button>
            </div>
            <input type="number" id="qty-${product_id}" class="text-center form-control form-control-sm"
                value="${qty}" min="1">
            <div class="input-group-append">
                <button class="btn btn-primary btn-sm" id="plus-btn"
                    onclick="incrementCounter('${product_id}','${variant_id}')">&plus;</button>
            </div>
        </div>
    </div>`);
        }
        $('#add-' + product_id).css('display', 'none!important');
        if ($('#btn_cart_count_' + product_id).length > 0) {
            $('#btn_cart_count_' + product_id).text(res['current_product_count']);
        }

    };
    let callbackError = function (res) {

        var myModal = new bootstrap.Modal(document.getElementById('onLoadModal'));
        myModal.show();
    };
    obj = { name, product_id, qty, price, sale_price, sgst, cgst, igst, unit, variant_id };
    objectAjaxWithBtnAndLoader((btnid = undefined), obj, url, callback, callbackError);

}

function OTPInput() {

    const inputs = document.querySelectorAll('#otp > *[id]');
    for (let i = 0; i < inputs.length; i++) {
        inputs[i].addEventListener('keydown', function (event) {
            if (event.key === "Backspace") {
                inputs[i].value = '';
                if (i !== 0) inputs[i - 1].focus();
            }
            else {
                if (i === inputs.length - 1 && inputs[i].value !== '') { return true; } else if (event.keyCode > 47 && event.keyCode < 58) {
                    inputs[i].value = event.key; if (i !== inputs.length - 1)
                        inputs[i + 1].focus(); event.preventDefault();
                }
                else if (event.keyCode > 64 && event.keyCode < 91) {
                    inputs[i].value = String.fromCharCode(event.keyCode);
                    if (i !== inputs.length - 1) inputs[i + 1].focus();
                    event.preventDefault();
                }
            }
        });
    }
}

const login_modal = document.getElementById(
    "loginModal"
);
const login_modal_front = document.getElementById(
    "onLoadModal"
);
if (login_modal) {
    login_modal.addEventListener("shown.bs.modal", (event) => {
        $('#otp_step').hide();
        $('#register_step').hide();
        OTPInput();
        $('#success_message').empty();
        validateRegisterForm();
        $('#otp_form').submit(function (e) {
            e.preventDefault();

            validateOtp();


        })

        $("#customer_login_form").validate({
            errorClass: "invalid-feedback",
            // errorElement: "div",
            errorPlacement: function (error, element) {
                if (element.attr("type") == "radio") {
                    error.insertAfter($("#position_around"));
                } else {
                    $(element).closest(".form-group").append(error);
                }
            },
            highlight: function (element) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid");
            },

            rules: {
                phone: {
                    required: true,
                    digits: true,
                    phone: true,
                },
            },
            messages: {
                phone: {
                    phone: 'Enter  valid phone number'
                }
            },
            focusCleanup: true,
            submitHandler: function (form, event) {
                event.preventDefault();
                $('#validation_errors').html();
                $('#success_message').empty();
                formid = $('#customer_login_form').attr("id");
                let btn = $("#customer_login_btn");
                let formData = $("#customer_login_form").serialize();

                $.ajax({
                    url: baseurl + '/customer_login',
                    method: "POST",
                    dataType: "json",
                    data: formData,

                    beforeSend: function () {
                        btn.html("Please wait..");
                        //   $
                    },
                    success: function (res, textStatus, xhr) {
                        btn.html('Login');
                        $('#validation_errors').html('');
                        $('#success_message').empty();
                        if (!res['success']) {
                            $('#validation_errors').html(res['message']);
                        }
                        else {
                            $('#login_step').hide();
                            $('#otp_step').show();
                        }
                    },
                    complete: function () {
                        btn.html('Login');
                    },

                    error: function (xhr, status, errorThrown) {
                        btn.html('Login');
                        console.log(xhr);
                        $('#success_message').empty();
                        let g = xhr['responseJSON'];

                        $('#validation_errors').html(g['message']);

                    },
                });

            },
        });
    });
}
if (login_modal_front) {

    login_modal_front.addEventListener("shown.bs.modal", (event) => {
        $('#otp_step').hide();
        $('#register_step').hide();
        OTPInput();
        $('#success_message').empty();
        validateRegisterForm();
        $('#otp_form').submit(function (e) {
            e.preventDefault();

            validateOtp();


        })

        $("#customer_login_form").validate({
            errorClass: "invalid-feedback",
            // errorElement: "div",
            errorPlacement: function (error, element) {
                if (element.attr("type") == "radio") {
                    error.insertAfter($("#position_around"));
                } else {
                    $(element).closest(".form-group").append(error);
                }
            },
            highlight: function (element) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid");
            },

            rules: {
                phone: {
                    required: true,
                    digits: true,
                    phone: true,
                },
            },
            messages: {
                phone: {
                    phone: 'Enter  valid phone number'
                }
            },
            focusCleanup: true,
            submitHandler: function (form, event) {
                event.preventDefault();
                $('#validation_errors').html();
                $('#success_message').empty();
                formid = $('#customer_login_form').attr("id");
                let btn = $("#customer_login_btn");
                let formData = $("#customer_login_form").serialize();

                $.ajax({
                    url: baseurl + '/customer_login',
                    method: "POST",
                    dataType: "json",
                    data: formData,

                    beforeSend: function () {
                        btn.html("Please wait..");
                        //   $
                    },
                    success: function (res, textStatus, xhr) {
                        btn.html('Login');
                        $('#validation_errors').html('');
                        $('#success_message').empty();
                        if (!res['success']) {
                            $('#validation_errors').html(res['message']);
                        }
                        else {
                            $('#login_step').hide();
                            $('#otp_step').show();
                        }
                    },
                    complete: function () {
                        btn.html('Login');
                    },

                    error: function (xhr, status, errorThrown) {
                        btn.html('Login');
                        console.log(xhr);
                        $('#success_message').empty();
                        let g = xhr['responseJSON'];

                        $('#validation_errors').html(g['message']);

                    },
                });

            },
        });
    });

    var myModal = new bootstrap.Modal(document.getElementById('onLoadModal'));
    myModal.show();

}
billing_form_init();
function billing_form_init() {
    let rules = {
        billing_fname: {
            required: true,

        },
        billing_address1: {
            required: true,

        },

        billing_state: {
            required: true,

        },
        billing_state: {
            required: true,

        },
        billing_city: {
            required: true,

        },
        billing_pincode: {
            required: true,
            digits: true,
            minlength: 4, maxlength: 6

        },
        billing_phone: {
            required: true,
            phone: true,


        },

    };
    // if ($('#same_as_billing').is(':checked')) {
    //     rules['shipping_fname'] = {
    //         required: $('#same_as_billing').is(":checked") ? true : false,

    //     };
    //     rules['shipping_address1'] = {
    //         required: $('#same_as_billing').is(":checked") ? true : false,

    //     };

    //     rules['shipping_state'] = {
    //         required: $('#same_as_billing').is(":checked") ? true : false,

    //     };
    //     rules['shipping_state'] = {
    //         required: $('#same_as_billing').is(":checked") ? true : false,

    //     };
    //     rules['shipping_city'] = {
    //         required: $('#same_as_billing').is(":checked") ? true : false,

    //     };
    //     rules['shipping_pincode'] = {
    //         required: $('#same_as_billing').is(":checked") ? true : false,
    //         digits: true,
    //         minlength: 4,
    //         maxlength: 6

    //     };
    //     rules['shipping_phone'] = {
    //         required: $('#same_as_billing').is(":checked") ? true : false,
    //         phone: $('#same_as_billing').is(":checked") ? true : false,


    //     };
    // }

    $("#billing_form").validate({
        errorClass: "invalid-feedback",
        // errorElement: "div",
        errorPlacement: function (error, element) {
            if (element.attr("type") == "radio") {
                error.insertAfter($("#position_around"));
            } else {
                $(element).closest(".form-group").append(error);
            }
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },

        rules: rules,
        messages: {
            billing_phone: {
                phone: 'Enter  valid billing phone number'
            },
            shipping_phone: {
                phone: 'Enter  valid shipping phone number'
            }
        },
        // focusCleanup: true,
        submitHandler: function (form, event) {
            event.preventDefault();
            let payment_methd = $('input[name=payment_method]:checked').val();
            console.log(payment_methd);
            if (payment_methd == 'Online') {

                let formData = $("#billing_form").serialize();
                let btn = $('#billing_btn');
                $.ajax({
                    url: baseurl + '/create_order',
                    method: "POST",
                    dataType: "json",
                    data: formData,

                    beforeSend: function () {
                        btn.html("Please wait..");
                        //   $
                    },
                    success: function (res, textStatus, xhr) {
                        btn.html('Place Order');
                        console.log(res);
                        openRazorPayDialog(res['amount'], res['razorpay_orderid']);

                    },


                    error: function (xhr, status, errorThrown) {
                        btn.html('Place Order');
                        let g = xhr['responseJSON'];
                        $('#validation_errors').html(g['message']);

                    },
                });

            }
            else {
                let btn = $('#billing_btn')
                $.ajax({
                    url: $('#billing_form').attr('action'),
                    type: 'POST',
                    data: $('#billing_form').serialize(),

                    beforeSend: function () {
                        btn.html("Please wait..");
                        //   $
                    },
                    success: function (res, textStatus, xhr) {
                        btn.html('Place Order');
                        if (res['success']) {
                            // let myModal = new bootstrap.Modal(document.getElementById('order_success_modal'));
                            // myModal.show();
                            // setTimeout()
                            location.href="/order_success"
                        }
                        else {
                            // let myModal = new bootstrap.Modal(document.getElementById('order_failed_modal'));
                            // myModal.show();
                            location.href="/order_failed"
                        }
                    },

                    error: function (xhr, status, errorThrown) {
                        btn.html('Place Order');
                        // let myModal = new bootstrap.Modal(document.getElementById('order_failed_modal'));
                        // myModal.show();
                        location.href="/order_failed"

                    },
                });
            }

        }
    });
}

function openRazorPayDialog(amount, orderid) {
    var options = {
        "key": razor_key,
        "amount": 1000, // Example: 2000 paise = INR 20
        "name": "MERCHANT",
        "order_id": orderid,
        "description": "description",
        "image": "img/logo.png",// COMPANY LOGO
        // "handler": function (response) {
        //     console.log(response);
        //     // AFTER TRANSACTION IS COMPLETE YOU WILL GET THE RESPONSE HERE.
        // },
        "callback_url": payment_callback_url,
        "prefill": {
            "name": "ABC", // pass customer name
            "email": 'A@A.COM',// customer email
            "contact": '+919123456780' //customer phone no.
        },
        "notes": {
            "address": "address" //customer address 
        },
        "theme": {
            "color": "#15b8f3" // screen color
        }
    };
    console.log(options);
    var propay = new Razorpay(options);
    propay.open();
}
function validateOtp() {
    let btn = $('#otp_btn');
    let otp = '';
    const inputs = document.querySelectorAll('#otp > *[id]');
    for (let i = 0; i < inputs.length; i++) {
        otp += inputs[i].value;

    }
    $('#success_message').empty();
    $.ajax({
        url: baseurl + '/verify_otp',
        method: "POST",
        dataType: "json",
        data: { otp: otp },

        beforeSend: function () {
            btn.html("Please wait..");
            //   $
        },
        success: function (res, textStatus, xhr) {
            btn.html('Verify');
            $('#validation_errors').html('');
            $('#success_message').empty();
            if (!res['success']) {
                $('#validation_errors').html(res['message']);
            }
            else {
                $('#otp_form').trigger('reset');
                $('#success_message').html(`<div class="alert alert-success">${res['message']}</div>`)
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        },
        complete: function () {
            btn.html('Verify');
        },

        error: function (xhr, status, errorThrown) {
            btn.html('Verify');
            let g = xhr['responseJSON'];

            $('#validation_errors').html(g['message']);

        },
    });
}
function showRegisterForm() {
    $('#otp_step').hide();
    $('#login_step').hide();
    $('#register_step').show();
}
function showLogin() {
    $('#otp_step').hide();
    $('#register_step').hide();
    $('#login_step').show();

}
function validateRegisterForm() {
    $('#success_message').empty();
    $("#customer_register_form").validate({
        errorClass: "invalid-feedback",
        // errorElement: "div",
        errorPlacement: function (error, element) {
            if (element.attr("type") == "radio") {
                error.insertAfter($("#position_around"));
            } else {
                $(element).closest(".form-group").append(error);
            }
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },

        rules: {
            name: {
                required: true,

            },
            phone: {
                required: true,
                digits: true,
                phone: true,
            },
        },
        messages: {
            phone: {
                phone: 'Enter  valid phone number'
            }
        },
        focusCleanup: true,
        submitHandler: function (form, event) {
            event.preventDefault();
            $('#validation_errors').html('');
            formid = $('#customer_register_form').attr("id");
            let btn = $("#customer_register_btn");
            let formData = $("#customer_register_form").serialize();

            $.ajax({
                url: baseurl + '/customer_register',
                method: "POST",
                dataType: "json",
                data: formData,

                beforeSend: function () {
                    btn.html("Please wait..");
                    //   $
                },
                success: function (res, textStatus, xhr) {
                    btn.html('Submit');
                    $('#validation_errors').html('');
                    $('#success_message').empty();
                    if (!res['success']) {
                        $('#validation_errors').html(res['message']);
                    }
                    else {
                        $('#success_message').html(`<div class="alert alert-success">${res['message']}</div>`)
                        $('#otp_step').hide();
                        $('#register_step').hide();
                        $('#login_step').show();

                    }
                },
                complete: function () {
                    btn.html('Submit');
                },

                error: function (xhr, status, errorThrown) {
                    btn.html('Submit');

                    let g = xhr['responseJSON'];

                    $('#validation_errors').html(g['message']);

                },
            });

        },
    });
}
function resendOtp() {
    let btn = $('#otp_btn');
    $.ajax({
        url: baseurl + '/resend_otp',
        method: "POST",
        dataType: "json",
        data: formData,

        beforeSend: function () {
            btn.html("Please wait..");
            //   $
        },
        success: function (res, textStatus, xhr) {
            btn.html('Verify');

            if (!res['success']) {
                $('#validation_errors').html(res['message']);
            }
            else {
                alert('verified');
            }
        },
        complete: function () {
            enableBtn(btn);
        },

        error: function (xhr, status, errorThrown) {
            enableBtn(btn);
            console.log(xhr);
            let g = xhr['responseJSON'];

            $('#validation_errors').html(g['message']);

        },
    });
}
function deleteCartItem(product_id, is_cart_page = true, variant_id = undefined) {

    let url = baseurl + "/deleteCart";


    let callback = function (res) {
        if (!is_cart_page) {
            $('#mini_cart_count').text(res['cart_count']);
            $('#' + product_id + variant_id).hide();
        }
        else {
            $('#cart_dropdown').html(res['minicart_html']);
            $('#cart_content').html(res['view']);
        }
        // location.reload();
    };
    obj = { product_id, variant_id, is_cart_page };
    objectAjaxWithBtnAndLoader((btnid = undefined), obj, url, callback);
}



/**************************************SIngle Product Page all js ************************************************** */
let current_single_product_price = $('#sale_price').text();
let total_payable = current_single_product_price;
$('#total_payable').text('(₹' + total_payable + ')');
let choosen_addon_product = [];
let choosen_addon_items = [];
let variants = {};

let g = $('.attributes');
if (g.length > 0) {
    g.each(function (el) {
        let name = $(this).attr('name');
        variants[name] = $(this).val();
    });
}
function addToCartFromDetailPage(name, product_id, price, sale_price, sgst = 0, cgst = 0, igst = 0, unit, variant_id = undefined, cart_session_id = undefined) {
    let qty = $('#qty').val();
    showLoade
    +('addToCart');
    let url = baseurl + "/addToCart";

    let callback = function (res) {
        hideLoader('addToCart');
        $('#mini_cart_count').text(res['cart_count']);
        $('#cart_dropdown').html(res['minicart_html']);
        $('#btn-container-' + product_id).html(`<div class="container mt-5" id="counter-${product_id}" >
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <button class="btn btn-dark btn-sm" id="minus-btn"
                    onclick="decrementCounter('${product_id}')">&minus;</button>
            </div>
            <input type="number" id="qty-${product_id}" class="text-center form-control form-control-sm"
                value="${qty}" min="1">
            <div class="input-group-append">
                <button class="btn btn-dark btn-sm" id="plus-btn"
                    onclick="incrementCounter('${product_id}')">&plus;</button>
            </div>
        </div>
    </div>`);
        $('#add-' + product_id).css('display', 'none!important');

    };
    obj = { name, product_id, qty, price, sale_price, sgst, cgst, igst, unit, variant_id, addon_products: choosen_addon_product, addon_items: choosen_addon_items, variant_attributes: variants };
    objectAjaxWithBtnAndLoader((btnid = undefined), obj, url, callback);

}
function addProductAddon(addon_id, addon_name, amount, qty, is_qty_changed = false) {
    let target = event.currentTarget;

    $(target).toggleClass('active_addon');
    let item_to_add = {
        'addon_name': addon_name,
        'addon_id': addon_id,
        'addon_variant_name': '',
        'addon_variant_id': '',
        'amount': amount,
        'qty': qty

    };


    let f = choosen_addon_product.filter(function (arr_item) {
        if (arr_item['addon_id'] == addon_id) {
            return false;
        }
        else return true;
    });

    if ($(target).hasClass('active_addon') || is_qty_changed) {
        f.push(item_to_add);
    }
    choosen_addon_product = f;

    console.log(choosen_addon_product);
    calculateTotalPayable();

}
function addProductAddonWithVariant(addon_id, addon_name, amount, qty, addon_variant_id, addon_variant_name, is_qty_changed = false) {
    let target = event.currentTarget;
    let item_to_add = {
        'addon_name': addon_name,
        'addon_id': addon_id,
        'addon_variant_name': addon_variant_name,
        'addon_variant_id': addon_variant_id,
        'amount': amount,
        'qty': qty

    };
    let f = choosen_addon_product.filter(function (arr_item) {
        if (arr_item['addon_id'] == addon_id && arr_item['addon_variant_id'] == addon_variant_id) {
            return false;
        }
        else return true;
    });

    console.log(f);

    if ($(target).is(':checked') || is_qty_changed) {
        f.push(item_to_add);
    }
    choosen_addon_product = f;
    console.log(choosen_addon_product);
    calculateTotalPayable();
}
function addAddonItem(name, amount, qty = 1, is_qty_changed = false) {
    let target = event.currentTarget;


    let item_to_add = {
        'name': name,
        'price': amount,
        'qty': qty,


    };
    // let f = [];

    let f = choosen_addon_items.filter(function (arr_item) {
        if (arr_item['name'] == name) {
            return false;
        }
        else return true;
    });
    console.log(f);

    if ($(target).is(':checked') || is_qty_changed) {
        f.push(item_to_add);
    }
    choosen_addon_items = f;

    console.log(choosen_addon_items);

    calculateTotalPayable()



}
function showAddonVariantModal(addon_id) {
    var myModal = new bootstrap.Modal(document.getElementById('addonModal' + addon_id));
    myModal.show();

}


function variantOptionSelect(pid) {
    let variants = {};

    let g = $('.attributes');
    g.each(function (el) {
        let name = $(this).attr('name');
        variants[name] = $(this).val();
    });


    let callback = function (res) {
        // hideLoader('add_'+product_id);
        $('#price').text(res['price']);
        $('#sale_price').text(res['sale_price']);
        current_single_product_price = res['sale_price'];
        let price = parseFloat(res['price']);
        let sale_price = parseFloat(res['sale_price']);
        let discount = ((price - sale_price) / price) * 100;
        $('#discount').text(parseInt(discount));
        calculateTotalPayable();
    };
    // console.log(variants_ar);
    obj = { product_id: pid, attributes: JSON.stringify(variants) };
    objectAjaxNoLoaderNoAlert(obj, '/get_variant_price', callback);
}
function singleProductPageQtyChanged(op) {
    let cur=parseInt($('#qty').val());
    let new_val=op=='inc'?cur+1:cur-1;
    $('#qty').val(new_val).change();
   
   
    calculateTotalPayable();
}

function calculateTotalPayable() {
    let product_qty = $('#qty').val();
    console.log(product_qty)
    let amt = parseFloat(current_single_product_price);
    total_payable = amt;
    if (choosen_addon_product.length > 0) {
        choosen_addon_product.forEach(function (v) {
            amt += parseFloat(v['qty'] * v['amount']);
        });
    }
    if (choosen_addon_items.length > 0) {
        choosen_addon_items.forEach(function (v) {

            amt += parseFloat(v['qty'] * v['price']);
        });
    }
    total_payable = product_qty * amt;
    console.log(total_payable);
    $('#total_payable').text('(₹' + total_payable + ')');

}
function stopPropogation() {

    $(event.currentTarget).parent().parent().addClass('active_addon');
}
/***************Checkout***************** */
let delivery_date = null;
let time_slot = null;

function setTimeSlot(value) {

    let g = $('.slot_time_tabs');
    g.each(function () {
        $(this).toggleClass('slot_time_tabs');
    });
    $(event.currentTarget).addClass('slot_time_tabs');
    time_slot = value;
    $('input#slot_time').val(value)

}
/*********************FIlter list ************************** */
let selected_brands = [];
let selected_category = null;
let sort_by = 'ASC';

var page = 1;
var pr = document.getElementById("priceRange");

let minPriceSelected = pr ? parseInt(pr.getAttribute('data-min')) : 0;
let maxPriceSelected = pr ? parseInt(pr.getAttribute('data-max')) : 0;

function setSortBy(type) {
    sort_by = type;
    filter();
}

function setFilterBrands() {
    let target = $(event.currentTarget);
    selected_brands = [];
    $('input[name=brands]').each(function () {

        if ($(this).is(':checked')) {
            let br = $(this).val();
            if (!selected_brands.includes(br))
                selected_brands.push(br);
        }
    })
    console.log(selected_brands)

}
function filter(page = 1) {
    page = page;
    let callback = function (res) {
        // hideLoader('add_'+product_id);
        $('#prod_list').empty();
        $('#prod_list').html(res['view']);
        $('#product_count').text(res['product_count']);

    };
    obj = { min_price: $('#min_price_input').val(), max_price: $('#max_price_input').val(), brands: JSON.stringify(selected_brands), page, sort_by };
    objectAjaxNoLoaderNoAlert(obj, '/filter', callback)
}
function applyCouponCode(cart_session_id) {
    let code = $('#coupon_code').val();
    let callback = function (res) {
        //   if(res['success'])
        $('#cart_content').html(res['view']);

     
    };
    let callbackError = function (res) {
        console.log(res);
    }
    obj = { coupon_code: code, cart_session_id };
    objectAjaxNoLoaderNoAlert(obj, '/applyCouponCode', callback, callbackError)
}

/***************COupon Code***************** */
