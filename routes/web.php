<?php
use App\Http\Controllers\CommonController;
use App\Http\Controllers\CrudGeneratorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppVersionController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\Api\ReturnController;
use App\Http\Controllers\ReturnItemsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\ShiprocketWebhookController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\VendorController;

Route::get("/android/{version}", function ($version) {
    \DB::table("app_version")->update(["version" => "1.0." . $version]);
    return "done";
});
Route::get("/webhook_json_value", function () {
    $rows = DB::table("webhook_response")
        ->select("id", "message")
        ->get();

    // Group rows by extracted order_id from JSON
    $groupedByOrderId = [];

    foreach ($rows as $row) {
        $json = json_decode($row->message, true);
        if (!is_array($json)) {
            continue;
        }

        $orderId = $json["order_id"] ?? "Unknown";

        if (!isset($groupedByOrderId[$orderId])) {
            $groupedByOrderId[$orderId] = [];
        }

        if (!empty($json["scans"]) && is_array($json["scans"])) {
            foreach ($json["scans"] as $scan) {
                if (isset($scan["sr-status-label"], $scan["date"])) {
                    $groupedByOrderId[$orderId][] = [
                        "date" => $scan["date"],
                        "location" => $scan["location"] ?? "-",
                        "label" => $scan["sr-status-label"],
                    ];
                }
            }
        }
    }

    // Start building HTML
    $html = "";

    foreach ($groupedByOrderId as $orderId => $scans) {
        // Sort by date
        usort($scans, function ($a, $b) {
            return strtotime($a["date"]) <=> strtotime($b["date"]);
        });

        // Order ID header
        $html .= "<h3 style='background:#eee;padding:10px;margin-top:20px;'>Order ID: {$orderId}</h3>";

        // Table of scan history
        $html .=
            "<table border='1' cellpadding='5' cellspacing='0' width='100%'>";
        $html .=
            "<thead><tr><th>Date</th><th>Location</th><th>Status</th></tr></thead><tbody>";

        foreach ($scans as $scan) {
            $html .= "<tr>
                        <td>{$scan["date"]}</td>
                        <td>{$scan["location"]}</td>
                        <td>{$scan["label"]}</td>
                      </tr>";
        }

        $html .= "</tbody></table>";
    }

    return response($html);
});
Route::get("/upload", [ImageUploadController::class, "showForm"]);
Route::post("/upload", [ImageUploadController::class, "doUpload"])->name(
    "upload.image"
);
Route::controller(ReturnController::class)->group(function () {
    // Route::get('categories', 'index');
    Route::post("return_upload", "upload1");
  //  Route::post("update_return_status", "update_return_status");
    Route::post("addUpdateCustomerPayment", "add_update_bank_detail");
    Route::get("getCustomerBank/{id}", "get_bank_detail");
});
Route::controller(ReturnItemsController::class)->group(function () {
    Route::post("update_return_status", "update_return_status");
});
Route::controller(FrontendController::class)->group(function () {
    // Route::get('categories', 'index');
    Route::get("send", "sendMail");
     Route::post("/check_pincode", "checkPincode");
     Route::get("/need_android_update", "needAndroidUpdate");
});

Route::get("/product_image/{id}/{name}", [
    App\Http\Controllers\ImageResizeController::class,
    "product_image_resize",
]);
Route::get("/category_image/{name}", [
    App\Http\Controllers\ImageResizeController::class,
    "category_image_resize",
]);
Route::get("/collection_image/{name}", [
    App\Http\Controllers\ImageResizeController::class,
    "collection_image_resize",
]);
Route::get("/slider_image/{name}", [
    App\Http\Controllers\ImageResizeController::class,
    "slider_image_resize",
]);
Route::get("/banner_image/{name}", [
    App\Http\Controllers\ImageResizeController::class,
    "banner_image_resize",
]);
Route::get("/home", function () {
    /*home is redirect route defined in fortservice provider after logi auth  from here divert route based on role,dont use separate admin rout files now  */ if (
        auth()
            ->user()
            ->hasRole(["Admin"])
    ) {
        return redirect(route("admin.dashboard"));
    } else {
        return redirect(route("user.dashboard"));
    }
});
/** ==============Email verification customisation =========== */
Route::get("/email/verify", function () {
    return view("auth.verify-email");
})
    ->middleware("auth")
    ->name("verification.notice");

Route::get("/email/verify/{id}/{hash}", function (
    EmailVerificationRequest $request
) {
    $request->fulfill();

    return redirect("/home");
})
    ->middleware(["auth", "signed"])
    ->name("verification.verify");

//resend mail
Route::post("/email/verification-notification", function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with("message", "Verification link sent!");
})
    ->middleware(["auth", "throttle:6,1"])
    ->name("verification.send");
/**===================================End custom verification============================== */

Route::controller(CommonController::class)->group(function () {
    Route::post("/fieldExist", "field_exist");
    Route::post("/getDependentSelectData", "getDependentSelectData");
    Route::post("/getCities", "getCity");
    Route::post(
        "/getDependentSelectDataMultipleVal",
        "getDependentSelectDataMultipleVal"
    );
    Route::match(["get", "post"], "/search_table", "search_table");
    Route::post("/search_products", "search_products");
    Route::post("/fetchRowFromTable", "fetchRowFromTable");
    Route::post("/deleteRecordFromTable", "deleteRecordFromTable");

    Route::post("delete_file_from_table", "deleteFileFromTable")->name(
        "deleteTableFile"
    );
    Route::post("delete_file_from_path", "deleteFileFromPath")->name(
        "deleteFileFromPath"
    );

    Route::post("deleteInJsonColumnData", "deleteInJsonColumnData")->name(
        "deleteInJsonColumnData"
    );
    Route::post("assignUser", "assignUser")->name("assignUser");
    Route::post("delete_file_self", "deleteFileFromSelf")->name(
        "deleteFileSelf"
    );
    Route::post("table_field_update", "table_field_update")->name(
        "table_filed_update"
    );
    Route::post(
        "singleFieldUpdateFromTable",
        "singleFieldUpdateFromTable"
    )->name("singleFieldUpdateFromTable");
    Route::post("bulk_delete", "bulkDelete")->name("bulkDelete");
    Route::post("getTableColumn", "getColumnsFromTable");
    Route::post("getTableColumnCheckboxForm", "getColumnsFromTableCheckbox");
    Route::post("getValidationHtml", "getValidationHtml");
    Route::post("getRepeatableHtml", "getRepeatableHtml");
    Route::post("getCreateInputOptionHtml", "getCreateInputOptionHtml");
    Route::post("getSideColumnInputOptionHtml", "getSideColumnInputOptionHtml");
    Route::post("getToggableGroupHtml", "getToggableGroupHtml");
});
Route::controller(ShiprocketWebhookController::class)->group(function () {
    Route::get("/track/{id}", "track");
    Route::post("/webhook", "handleWebhook");
    Route::post("/delhivery-webhook", "handleDelhiveryWebhook");
});
$sharedDomains = ["admin.colourindigo.com", "vendor.colourindigo.com"];
Route::domain("admin.colourindigo.com")->group(function () {
    Route::middleware(["admin"])->group(function () {
        Route::post("/vendor_order_update", [VendorController::class, "updateVendorOrder"]);
        Route::resource("app-versions", AppVersionController::class)->only([
            "index",
            "edit",
            "update",
        ]);
        Route::get("push_notification", [
            PushNotificationController::class,
            "showNotificationForm",
        ])->name("pushnotification_form");
        Route::post("send_notification", [
            PushNotificationController::class,
            "handleNotification",
        ])->name("send_pushnotification");
        Route::delete("push_notifications/{id}", [
            PushNotificationController::class,
            "destroy",
        ])->name("push_notifications.destroy");

        Route::controller(FrontendController::class)->group(function () {
            Route::get("/", "index");
            Route::get("/clear_cache", "clear_cache")->name("clear_cache");
            Route::get("/cache", "cache")->name("cache");
            Route::get("/redirect", "redirect");
            Route::get("/track", "redirect");
           
        });
        Route::controller(DashboardController::class)->group(function () {
            Route::get("/dashboard", "index")->name("admin.dashboard");
            Route::get("/dashboard_data", "dashboard_data")->name(
                "admin.dashboard_data"
            );
        });

        Route::controller(CrudGeneratorController::class)->group(function () {
            Route::get("/crud", "index")->name("admin.crud");
            Route::match(
                ["get", "post"],
                "/generateModule",
                "generateModule"
            )->name("admin.generateModule");
            Route::match(
                ["get", "post"],
                "/generateTable",
                "generateTable"
            )->name("admin.generateTable");
            Route::match(
                ["get", "post"],
                "/addTableRelationship",
                "addTableRelationship"
            )->name("admin.addTableRelationship");
        });
        // RoleController
        registerCrudRoutes(
            "roles",
            \App\Http\Controllers\RoleController::class,
            [
                "export" => "exportRole",
            ],
            "role",
            "admin"
        );

        registerCrudRoutes(
            "permissions",
            \App\Http\Controllers\PermissionController::class,
            [
                "custom" => [
                    [
                        "method" => "post",
                        "uri" => "permission/load_form",
                        "action" => "loadAjaxForm",
                        "name" => "permission.loadAjaxForm",
                    ],
                ],
            ],
            "permission",
            "admin"
        );

        registerCrudRoutes(
            "users",
            \App\Http\Controllers\UserController::class,
            [
                "export" => "exportUser",
                "custom" => [
                    [
                        "method" => "post",
                        "uri" => "user/load_form",
                        "action" => "loadAjaxForm",
                        "name" => "user.loadAjaxForm",
                    ],
                    [
                        "method" => "get",
                        "uri" => "users/{role}",
                        "action" => "index1",
                    ],
                ],
            ],
            "user",
            "admin"
        );

       

        registerCrudRoutes(
            "content_sections",
            \App\Http\Controllers\ContentSectionController::class,
            [
                "export" => "exportContentSection",
                "custom" => [
                    [
                        "method" => "post",
                        "uri" => "update_order_sequence",
                        "action" => "updateSequence",
                        "name" => "updateSequence",
                    ],
                ],
            ],
            "content_section",
            "admin"
        );

        registerCrudRoutes(
            "collections",
            \App\Http\Controllers\CollectionController::class,
            [
                "export" => "exportCollection",
            ],
            "collection",
            "admin"
        );
        registerCrudRoutes(
            "colors",
            \App\Http\Controllers\ColorController::class,
            [],
            "color",
            "admin"
        );

        registerCrudRoutes(
            "videos",
            \App\Http\Controllers\VideoController::class,
            [
                "export" => "exportVideo",
            ],
            "video",
            "admin"
        );

        registerCrudRoutes(
            "website_banners",
            \App\Http\Controllers\WebsiteBannerController::class,
            [
                "export" => "exportWebsiteBanner",
            ],
            "website_banner",
            "admin"
        );
        registerCrudRoutes(
            "facet_attributes",
            \App\Http\Controllers\FacetAttributeController::class,
            [],
            "facet_attributes",
            "admin"
        );
        registerCrudRoutes(
            "facet_attributes_values",
            \App\Http\Controllers\FacetAttributesValueController::class,
            [],
            "facet_attributes_values",
            "admin"
        );

        registerCrudRoutes(
            "website_sliders",
            \App\Http\Controllers\WebsiteSliderController::class,
            [
                "export" => "exportWebsiteSlider",
            ],
            "website_slider",
            "admin"
        );

        registerCrudRoutes(
            "website_content_sections",
            \App\Http\Controllers\WebsiteContentSectionController::class,
            [
                "export" => "exportWebsiteContentSection",
            ],
            "website_content_section",
            "admin"
        );

        registerCrudRoutes(
            "attribute_values_templates",
            \App\Http\Controllers\AttributeValuesTemplateController::class,
            [],
            "attribute_values_template",
            "admin"
        );
        registerCrudRoutes(
            "training_videos",
            \App\Http\Controllers\TrainingVideoController::class,
            [],
            "training_videos",
            "admin"
        );
        registerCrudRoutes(
            "settings",
            \App\Http\Controllers\SettingController::class,
            [],
            "settings",
            "admin"
        );
        registerCrudRoutes(
            "vendor_banks",
            \App\Http\Controllers\VendorBankController::class,
            [],
            "vendor_banks",
            "admin"
        );
        registerCrudRoutes(
            "customer_banks",
            \App\Http\Controllers\CustomerBankController::class,
            [],
            "customer_banks",
            "admin"
        );

        Route::get("/vendor_settlements", [
    App\Http\Controllers\DashboardController::class,
    "vendor_settlements",
]);
        Route::get("/completed_orders", [
    App\Http\Controllers\DashboardController::class,
    "completed_orders",
])->name('admin.completed_orders');
        Route::post("/update_order_shipping_charge", [
    App\Http\Controllers\VendorController::class,
    "updateOrderShippingCharge",
]);
       

    });
});
Route::get("/logout", [
    App\Http\Controllers\Auth\LoginController::class,
    "logout",
]);

Route::domain("vendor.colourindigo.com")->group(function () {
    
    Route::get("forget-password", [
        App\Http\Controllers\Auth\ForgotPasswordController::class,
        "ForgetPassword",
    ])->name("ForgetPasswordGet");
    Route::post("forget-password", [
        App\Http\Controllers\Auth\ForgotPasswordController::class,
        "ForgetPasswordStore",
    ])->name("vendor.ForgetPasswordPost");
    Route::get("reset-password/{token}", [
        App\Http\Controllers\Auth\ForgotPasswordController::class,
        "ResetPassword",
    ])->name("vendor.ResetPasswordGet");
    Route::post("post-reset-password", [
        App\Http\Controllers\Auth\ForgotPasswordController::class,
        "ResetPasswordStore",
    ])->name("vendor.ResetPasswordPost");

    Route::middleware(["vendor"])->group(function () {
               Route::get('/training_videos', [VendorController::class, 'training_videos'])
               ->name('vendor.training_videos');
      Route::get('/earning_settlement', [VendorController::class, 'earning_settlement'])
               ->name('vendor.earning_settlement');
 
        Route::controller(DashboardController::class)->group(function () {
            Route::get("/dashboard", "index")->name("vendor.dashboard");
            Route::get("/dashboard_data", "dashboard_data")->name(
                "vendor.dashboard_data"
            );
            Route::get("/profile", "profile");
            Route::get("/support", "support");
        });
        Route::get('/vendor/banks', [VendorController::class, 'vendor_bank_list'])->name('vendor.bank.list');
      Route::match(['get', 'post'], '/vendor/banks/add', [VendorController::class, 'vendor_bank_form'])->name('vendor.bank.add');
      Route::match(['get', 'post'], '/vendor/banks/{id}/edit', [VendorController::class, 'vendor_bank_form'])->name('vendor.bank.edit');
    });

    Route::middleware("guest")->group(function () {
        Route::get("/register", [
            App\Http\Controllers\Auth\RegisterController::class,
            "show",
        ])->name("register");
        Route::post("/seller/complete-registration", [
            App\Http\Controllers\Auth\RegisterController::class,
            "register",
        ])->name("register.perform");
        Route::post("/pre_register", [
            App\Http\Controllers\Auth\RegisterController::class,
            "registerValidationAndOtp",
        ])->name("register.validate");
        Route::post("/seller/verify-otp", [
            App\Http\Controllers\Auth\RegisterController::class,
            "verifyOtp",
        ]);
        Route::post("/seller/resend-otp", [
            App\Http\Controllers\Auth\RegisterController::class,
            "resendOtp",
        ]);
        
    });
});
foreach ($sharedDomains as $domain) {
    $p = explode(".", $domain); // e.g., admin_example_com
    $prefix = $p[0];
    Route::domain($domain)->group(function () use ($prefix) {
        Route::post("/upload-profile-picture/{id?}", [
            App\Http\Controllers\Auth\RegisterController::class,
            "updateProfilePicture",
        ]);
        Route::post("/update-profile/{id?}", [
            App\Http\Controllers\Auth\RegisterController::class,
            "updateProfile",
        ]);
        Route::post("/update-documents/{id?}", [
            App\Http\Controllers\Auth\RegisterController::class,
            "uploadDocuments",
        ]);
        Route::post("/change-password/{id?}", [
            App\Http\Controllers\Auth\RegisterController::class,
            "updatePassword",
        ]);
        Route::get("/save_location/{id?}", [
            App\Http\Controllers\Auth\RegisterController::class,
            "saveVendorLocation",
        ]);
        registerCrudRoutes(
            "vendors",
            \App\Http\Controllers\VendorController::class,
            [
                "export" => "exportVendor",
            ],
            "vendor",
            $prefix
        );
        registerCrudRoutes(
            "tickets",
            \App\Http\Controllers\TicketController::class,
            [
              
            ],
            "tickets",
            $prefix
        );
       Route::get("/ticket/{id}/replies",[\App\Http\Controllers\TicketController::class,"ticket_replies"]);  
       Route::post("/ticket/reply/{id}",[\App\Http\Controllers\TicketController::class,"reply_ticket"])->name($prefix.'.ticket_reply');  
        Route::controller(\App\Http\Controllers\VendorController::class)->group(
            function () use ($prefix) {
                Route::get("/vendor_orders", "orders")->name(
                    $prefix . ".vendor_orders"
                );
                Route::get(
                    "/vendor_return_shipments",
                    "return_shipments"
                )->name($prefix . ".vendor_return_shipments");
                Route::get(
                    "/transfer_return_shipments/{id}",
                    "transferReturnShipments"
                )->name($prefix . ".vendor_transfer_return_shipments");
                Route::get("/sales", "sales")->name($prefix . ".vendor_sales");
                Route::get("/barcode/{d}/{invoice}", "barcode");
                Route::get("/extract", "extract");
                Route::get("/label/{vendorOrderId}", "showLabel");
                Route::post(
                    "/vendor_order_detail",
                    "getVendorOrderDetail"
                )->name($prefix . ".vendor_order_detail");
                Route::post("/generate_doc", "generateDocumentPost")->name(
                    $prefix . ".generate_vendor_doc"
                );
                Route::match(["get", "post"], "/bank", "vendor_bank")->name(
                    $prefix . ".vendor_bank"
                );
            }
        );
        Route::middleware("guest")->group(function () use ($prefix) {
            Route::get("/login", [
                App\Http\Controllers\Auth\LoginController::class,
                "show",
            ])->name($prefix . ".login");
            Route::post("/login", [
                App\Http\Controllers\Auth\LoginController::class,
                "login",
            ])->name($prefix . ".login.perform");
        });
        Route::middleware($prefix)->group(function () use ($prefix) {
            Route::controller(FrontendController::class)->group(function () {
                Route::get("/", "index");
            });

            registerCrudRoutes(
                "products",
                \App\Http\Controllers\ProductController::class,
                [
                    "export" => "exportProduct",
                    "custom" => [
                        [
                            "method" => "get",
                            "uri" => "product/excel_template",
                            "action" => "exportProductExcelTempalte",
                            "name" => "products.export_template",
                        ],
                        [
                            "method" => "get",
                            "uri" => "product/excel_variant_template",
                            "action" => "exportProductVariantExcelTempalte",
                            "name" => "products.export_variant_template",
                        ],
                        [
                            "method" => "post",
                            "uri" => "products/import",
                            "action" => "importProduct",
                            "name" => "products.import",
                        ],
                        [
                            "method" => "post",
                            "uri" => "products/import_variant",
                            "action" => "importProductVariant",
                            "name" => "products.import_variant",
                        ],
                        [
                            "method" => "post",
                            "uri" => "products-import-discounts",
                            "action" => "importDiscounts",
                            "name" => "products.import-discounts",
                        ],
                        [
                            "method" => "get",
                            "uri" => "products-export-basic",
                            "action" => "exportProductBasic",
                            "name" => "products.export-basic",
                        ],
                        [
                            "method" => "get",
                            "uri" => "category-export",
                            "action" => "exportCategory",
                            "name" => "products.export-category",
                        ],
                        [
                            "method" => "post",
                            "uri" => "generateAccordian",
                            "action" => "generateAccordian",
                        ],
                        [
                            "method" => "post",
                            "uri" => "delete_prod_image",
                            "action" => "deleteImage",
                            "name" => "delete_product_image",
                        ],
                    ],
                ],
                "product",
                $prefix
            );

            registerCrudRoutes(
                "attributes",
                \App\Http\Controllers\AttributeController::class,
                [
                    "export" => "exportAttribute",
                ],
                "attribute",
                $prefix
            );

            registerCrudRoutes(
                "categories",
                \App\Http\Controllers\CategoryController::class,
                [
                    "export" => "exportCategory",
                    "custom" => [
                        [
                            "method" => "post",
                            "uri" => "get_category_based_product_features",
                            "action" => "getCategoryProductFeature",
                        ],
                    ],
                ],
                "category",
                $prefix
            );

            registerCrudRoutes(
                "brands",
                \App\Http\Controllers\BrandController::class,
                [
                    "export" => "exportBrand",
                ],
                "brand",
                $prefix
            );

            Route::controller(
                \App\Http\Controllers\OrderController::class
            )->group(function () use ($prefix) {
                Route::get("driver_orders/{driver_id}", "driver_orders")->name(
                    $prefix . ".driver.orders"
                );
                Route::resource(
                    "orders",
                    \App\Http\Controllers\OrderController::class
                )->names("$prefix.orders");
                Route::post("orders/view", "view")->name(
                    $prefix . ".orders.view"
                );
                Route::get(
                    "order_item/{id}",
                    "show_order_related_to_item_id"
                )->name($prefix . ".orders.view_item_id");
                Route::get("export_orders/{type}", "exportOrders")->name(
                    $prefix . ".orders.export"
                );
            });

            Route::resource(
                "return_items",
                \App\Http\Controllers\ReturnItemsController::class
            )->names("$prefix.return_items");
            Route::resource(
                "refunds",
                \App\Http\Controllers\RefundController::class
            )->names("$prefix.refunds");

            Route::resource(
                "payments",
                \App\Http\Controllers\PaymentController::class
            )->names("$prefix.payments");

            Route::controller(
                \App\Http\Controllers\FileManagerController::class
            )->group(function () {
                Route::get("/file-manager", "index");
                Route::post("/file-manager/upload", "upload1");
                Route::post("/file-manager/create-folder", "createFolder");
                Route::post("/file-manager/delete", "delete");
            });

            registerCrudRoutes(
                "new_coupons",
                \App\Http\Controllers\NewCouponController::class,
                [
                    "export" => "exportNewCoupon",
                ],
                "new_coupon",
                $prefix
            );
            Route::post("category_attributes", [
                \App\Http\Controllers\CategoryController::class,
                "getCategoryAttributes",
            ]);
        });
    });
}
