<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use File;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ShiprocketService;
use App\Services\DelhiveryService;
use Milon\Barcode\DNS1D;
use Barryvdh\DomPDF\Facade\Pdf;
class VendorController extends Controller
{
    public function __construct()
    {
        $this->dashboard_url = \URL::to("/admin");
        $this->index_url = domain_route("vendors.index");
        $this->module = "Vendor";
        $this->view_folder = "vendors";
        $this->storage_folder = "vendor_documents";
        $this->has_upload = 0;
        $this->is_multiple_upload = 0;
        $this->has_export = 0;
        $this->pagination_count = 100;
        $this->crud_title = "Seller";
        $this->show_crud_in_modal = 0;
        $this->has_popup = 0;
        $this->has_detail_view = 0;
        $this->has_side_column_input_group = 0;
        $this->form_image_field_name = [
            [
                "field_name" => "gst_image",
                "single" => true,
                "has_thumbnail" => false,
            ],
            [
                "field_name" => "pan_image",
                "single" => true,
                "has_thumbnail" => false,
            ],
            [
                "field_name" => "business_license_image",
                "single" => true,
                "has_thumbnail" => false,
            ],
            [
                "field_name" => "trademark_image",
                "single" => true,
                "has_thumbnail" => false,
            ],
        ];

        $this->model_relations = [
            [
                "name" => "city",
                "type" => "BelongsTo",
                "save_by_key" => "",
                "column_to_show_in_view" => "name",
            ],
            [
                "name" => "state",
                "type" => "BelongsTo",
                "save_by_key" => "",
                "column_to_show_in_view" => "name",
            ],
            [
                "name" => "vendor_bank",
                "type" => "BelongsTo",
                "save_by_key" => "",
                "column_to_show_in_view" => "name",
            ],
        ];
    }
    public function sideColumnInputs($model = null)
    {
        $data = [
            "side_title" => "Any Title",
            "side_inputs" => [],
        ];

        return $data;
    }
    public function createInputsData()
    {
        $data = [
            [
                "label" => null,
                "inputs" => [
                    [
                        "placeholder" => "Enter name",
                        "name" => "name",
                        "label" => "Business Name",
                        "tag" => "input",
                        "type" => "text",
                        "default" => isset($model) ? $model->name : "",
                        "attr" => [],
                    ],
                    [
                        "placeholder" => "Enter email",
                        "name" => "email",
                        "label" => "Email",
                        "tag" => "input",
                        "type" => "email",
                        "default" => isset($model) ? $model->email : "",
                        "attr" => [],
                    ],
                    [
                        "placeholder" => "Enter phone",
                        "name" => "phone",
                        "label" => "Phone",
                        "tag" => "input",
                        "type" => "number",
                        "default" => isset($model) ? $model->phone : "",
                        "attr" => [],
                    ],
                ],
            ],
        ];
        if (count($this->form_image_field_name) > 0) {
            foreach ($this->form_image_field_name as $g) {
                $y = [
                    "placeholder" => "",
                    "name" => $g["single"]
                        ? $g["field_name"]
                        : $g["field_name"] . "[]",
                    "label" => $g["single"]
                        ? properSingularName($g["field_name"])
                        : properPluralName($g["field_name"]),
                    "tag" => "input",
                    "type" => "file",
                    "default" => "",
                    "attr" => $g["single"] ? [] : ["multiple" => "multiple"],
                ];
                array_push($data[0]["inputs"], $y);
            }
        }
        return $data;
    }
    public function editInputsData($model)
    {
        $data = [
            [
                "label" => null,
                "inputs" => [
                    [
                        "placeholder" => "Enter name",
                        "name" => "name",
                        "label" => "Business Name",
                        "tag" => "input",
                        "type" => "text",
                        "default" => isset($model) ? $model->name : "",
                        "attr" => [],
                    ],
                    [
                        "placeholder" => "Enter email",
                        "name" => "email",
                        "label" => "Email",
                        "tag" => "input",
                        "type" => "email",
                        "default" => isset($model) ? $model->email : "",
                        "attr" => [],
                    ],
                    [
                        "placeholder" => "Enter phone",
                        "name" => "phone",
                        "label" => "Phone",
                        "tag" => "input",
                        "type" => "number",
                        "default" => isset($model) ? $model->phone : "",
                        "attr" => [],
                    ],
                ],
            ],
        ];
        if (count($this->form_image_field_name) > 0) {
            foreach ($this->form_image_field_name as $g) {
                $y = [
                    "placeholder" => "",
                    "name" => $g["single"]
                        ? $g["field_name"]
                        : $g["field_name"] . "[]",
                    "label" => $g["single"]
                        ? properSingularName($g["field_name"])
                        : properPluralName($g["field_name"]),
                    "tag" => "input",
                    "type" => "file",
                    "default" => $g["single"]
                        ? $this->storage_folder .
                            "/" .
                            $model->{$g["field_name"]}
                        : json_encode(
                            $this->getImageList(
                                $model->id,
                                $g["table_name"],
                                $g["parent_table_field"],
                                $this->storage_folder
                            )
                        ),
                    "attr" => $g["single"] ? [] : ["multiple" => "multiple"],
                ];
                array_push($data[0]["inputs"], $y);
            }
        }
        return $data;
    }
    public function commonVars($model = null)
    {
        $repeating_group_inputs = [];
        $toggable_group = [];

        $table_columns = [
            [
                "column" => "name",
                "label" => "Busines Name",
                "sortable" => "Yes",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "email",
                "label" => "Email",
                "sortable" => "Yes",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "phone",
                "label" => "Phone",
                "sortable" => "Yes",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "pincode",
                "label" => "Pincode",
                "sortable" => "Yes",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "is_verified",
                "label" => "Is Verified?",
                "sortable" => "Yes",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
             [
                'column' => 'bank_name',
                'label' => 'Bank Name',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'account_number',
                'label' => 'A/C Number',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'account_holder',
                'label' => 'A/C Holder',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'ifsc_code',
                'label' => 'IFSC code',
                'sortable' => 'Yes',
            ],
        ];
        $view_columns = [
            [
                "column" => "name",
                "label" => "Business Name",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],

            [
                "column" => "gst",
                "label" => "GST No",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "gst_image",
                "label" => "GST Image",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "pan",
                "label" => "PAN No",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "pan_image",
                "label" => "PAN Image",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "business_license_image",
                "label" => "Business License  Certificate",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "trademark_image",
                "label" => "Trademark",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],

            [
                "column" => "address",
                "label" => "Address",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "address2",
                "label" => "Address2",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "city_id",
                "label" => "City Id",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
            [
                "column" => "state_id",
                "label" => "State Id",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],

            [
                "column" => "created_at",
                "label" => "Joined Date",
                "show_json_button_click" => false,
                "by_json_key" => "id",
                "inline_images" => true,
            ],
        ];

        $searchable_fields = [
            [
                "name" => "email",
                "label" => "Email",
            ],
            [
                "name" => "phone",
                "label" => "Phone",
            ],
            [
                "name" => "name",
                "label" => "Name",
            ],
            [
                "name" => "pincode",
                "label" => "Pincode",
            ],
            [
                "name" => "gst",
                "label" => "Gst",
            ],
            [
                "name" => "pan",
                "label" => "Pan",
            ],
        ];
        $filterable_fields = [
            [
                "name" => "created_at",
                "label" => "Created At",
                "type" => "date",
            ],
            [
                "name" => "city_id",
                "label" => "City ",
                "type" => "select",
                "options" => getList("City "),
            ],
            [
                "name" => "state_id",
                "label" => "State ",
                "type" => "select",
                "options" => getList("State "),
            ],
        ];

        $data["data"] = [
            "dashboard_url" => $this->dashboard_url,
            "index_url" => $this->index_url,
            "title" => "All " . $this->crud_title . "s",
            "module" => $this->module,
            "model_relations" => $this->model_relations,
            "searchable_fields" => $searchable_fields,
            "filterable_fields" => $filterable_fields,
            "storage_folder" => $this->storage_folder,
            "plural_lowercase" => "vendors",
            "has_image" => $this->has_upload,
            "table_columns" => $table_columns,
            "view_columns" => $view_columns,

            "image_field_names" => $this->form_image_field_name,
            "storage_folder" => $this->storage_folder,
            "module_table_name" => "vendors",
            "has_export" => $this->has_export,
            "crud_title" => $this->crud_title,
            "show_crud_in_modal" => $this->show_crud_in_modal,
            "has_popup" => $this->has_popup,
            "has_side_column_input_group" => $this->has_side_column_input_group,
            "has_detail_view" => $this->has_detail_view,
            "repeating_group_inputs" => $repeating_group_inputs,
            "toggable_group" => $toggable_group,
        ];

        return $data;
    }
    public function afterCreateProcess($request, $post, $model)
    {
        $meta_info = $this->commonVars()["data"];

        return $this->afterCreateProcessBase(
            $request,
            $post,
            $model,
            $meta_info
        );
    }
    public function common_view_data($id)
    {
        $data["row"] = null;
        if (count($this->model_relations) > 0) {
            $data["row"] = Vendor::with(
                array_column($this->model_relations, "name")
            )->findOrFail($id);
        } else {
            $data["row"] = Vendor::findOrFail($id);
        }
        $data["view_inputs"] = [];
        /***If you want to show any form iput in view ***
        $data['view_inputs'] = [
            [
                'label' => '',
                'inputs' => [
                    [
                        'placeholder' => 'Enter title',
                        'name' => 'title',
                        'label' => 'Title',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => '',
                        'attr' => [],
                    ],
                    [
                        'placeholder' => 'Enter remark',
                        'name' => 'remark',
                        'label' => 'Remark',
                        'tag' => 'input',
                        'type' => 'file',
                        'default' => '',
                        'attr' => ['class'=>'summernote'],
                    ],
                ],
            ],
        ];
        ***/
        $data = array_merge($this->commonVars()["data"], $data);
        // dd($data);
        return $data;
    }
    public function index(Request $request)
    {
        $tabs = [
            /*[
        'label' => 'Active',
        'value' => 'Active',
        'count' => 1,
        'column' => 'status',
    ],
    [
        'label' => 'In-Active',
        'value' => 'In-Active',
        'count' => 3,
        'column' => 'status',
    ],*/
        ];
        $vendor_id = auth()
            ->guard("vendor")
            ->id();
        $common_data = $this->commonVars()["data"];
        if ($request->ajax()) {
            $sort_by = $request->get("sortby");
            $sort_type = $request->get("sorttype");
            $search_by = $request->get("search_by");
            $query = $request->get("query");

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
                $search_by = "name";
            }

            $tabs_column =
                count($tabs) > 0 ? array_column($tabs, "column") : [];

            $db_query = Vendor::when(!empty($search_val), function (
                $query
            ) use ($search_val, $search_by) {
                return $query->where(
                    $search_by,
                    "like",
                    "%" . $search_val . "%"
                );
            })
                ->when(!empty($sort_by), function ($query) use (
                    $sort_by,
                    $sort_type
                ) {
                    return $query->orderBy($sort_by, $sort_type);
                })
                ->when(!empty($vendor_id), function ($query) use (
                    $vendor_id,
                    $sort_by,
                    $sort_type
                ) {
                    return $query->where("vendor_id", $vendor_id);
                });

            if (count($tabs_column) > 0) {
                foreach ($tabs_column as $col) {
                    if ($request->has($col) && !empty($request->{$col})) {
                        $db_query = $db_query->where($col, $request->{$col});
                    }
                }
            }

            $list = $db_query->latest()->paginate($this->pagination_count);
            $data = array_merge($common_data, [
                "list" => $list,
                "sort_by" => $sort_by,
                "sort_type" => $sort_type,
                "bulk_update" => json_encode([
                    "is_verified" => [
                        "label" => "Set Verified",
                        "data" => getListFromIndexArray([
                            "Yes",
                            "No",
                        ]),
                    ],
                    "status" => [
                        "label" => "Status",
                        "data" => getListFromIndexArray([
                            "Active",
                            "In-Active",
                        ]),
                    ],
                ]),

                /*
            Multi rows select karke koi column mein values update kara ho jaise status update,user assign
            'bulk_update' => json_encode([
            'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Active','In-Active'])],
            'user_id'=>['label'=>'Assign User','data'=>getList('User')]

            ])
             */
            ]);
            return view("admin." . $this->view_folder . ".page", with($data));
        } else {
            if (!can("list_vendors")) {
                return redirect()
                    ->back()
                    ->withError("Dont have permission to list");
            }
            $query = null;
            if (count($this->model_relations) > 0) {
                $query = Vendor::with(
                    array_column($this->model_relations, "name")
                );
            } else {
                $query = Vendor::query();
            }
            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);
            $view_data = array_merge($common_data, [
                "list" => $list,
               "bulk_update" => json_encode([
                    "is_verified" => [
                        "label" => "Set Verified",
                        "data" => getListFromIndexArray([
                            "Yes",
                            "No",
                        ]),
                    ],
                    "status" => [
                        "label" => "Status",
                        "data" => getListFromIndexArray([
                            "Active",
                            "In-Active",
                        ]),
                    ],
                ]),

                /*
            Multi rows select karke koi column mein values update kara ho jaise status update,user assign
            'bulk_update' => json_encode([
            'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Active','In-Active'])],
            'user_id'=>['label'=>'Assign User','data'=>getList('User')]

            ])
             */
            ]);
            $index_view = count($tabs) > 0 ? "index_tabs" : "index";
            return view(
                "admin." . $this->view_folder . "." . $index_view,
                $view_data
            );
        }
    }

    public function create(Request $r)
    {
        $data = $this->createInputsData();
        $view_data = array_merge($this->commonVars()["data"], [
            "data" => $data,
        ]);

        if ($r->ajax()) {
            if (!can("create_vendors")) {
                return createResponse(false, "Dont have permission to create");
            }

            $html = view(
                "admin." . $this->view_folder . ".modal.add",
                with($view_data)
            )->render();
            return createResponse(true, $html);
        } else {
            if (!can("create_vendors")) {
                return redirect()
                    ->back()
                    ->withError("Dont have permission to create");
            }
            return view(
                "admin." . $this->view_folder . ".add",
                with($view_data)
            );
        }
    }
    public function store(VendorRequest $request)
    {
        if (!can("create_vendors")) {
            return createResponse(false, "Dont have permission to create");
        }
        \DB::beginTransaction();

        try {
            $post = $request->all();

            $post = formatPostForJsonColumn($post);
            /* Saving name alongwith id in json column takki join se na retrive karna pade
               copy this code from contrller file and paste and edit here 
              $post=$this->processJsonColumnToAddNameOrAddtionalData($post);
            */

            $vendor = Vendor::create($post);
            $this->afterCreateProcess($request, $post, $vendor);
            \DB::commit();
            return createResponse(
                true,
                $this->crud_title . " created successfully",
                $this->index_url
            );
        } catch (\Exception $ex) {
            \Sentry\captureException($ex);
            \DB::rollback();

            return createResponse(false, $ex->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {
        $model = Vendor::findOrFail($id);
  
        $data = $this->editInputsData($model);
     
        $view_data = array_merge($this->commonVars($model)["data"], [
            "data" => $data,
            "model" => $model,
             'states'=>getList('State'),
             'city_name'=>$model->state_id?\DB::table('city')->where('state_id',$model->state_id)->first()->name:null,
              'cities'=>$model->state_id?\DB::table('city')->where('state_id',$model->state_id)->get():[]
        ]);
    //return view('admin.vendors.edit',with($view_data));
        if ($request->ajax()) {
            if (!can("edit_vendors")) {
                return createResponse(false, "Dont have permission to edit");
            }

            $html = view(
                "admin." . $this->view_folder . ".modal.edit",
                with($view_data)
            )->render();
            return createResponse(true, $html);
        } else {
           
            if (!can("edit_vendors")) {
                return redirect()
                    ->back()
                    ->withError("Dont have permission to edit");
            }

            return view(
                "admin." . $this->view_folder . ".edit",
                with($view_data)
            );
        }
    }

    public function show(Request $request, $id)
    {
        $view = $this->has_detail_view ? "view_modal_detail" : "view_modal";
        $data = $this->common_view_data($id);

        if ($request->ajax()) {
            if (!can("view_vendors")) {
                return createResponse(false, "Dont have permission to view");
            }

            $html = view(
                "admin." . $this->view_folder . ".view." . $view,
                with($data)
            )->render();
            return createResponse(true, $html);
        } else {
            if (!can("view_vendors")) {
                return redirect()
                    ->back()
                    ->withError("Dont have permission to view");
            }

            return view(
                "admin." . $this->view_folder . ".view." . $view,
                with($data)
            );
        }
    }

    public function update(VendorRequest $request, $id)
    {
        if (!can("edit_vendors")) {
            return createResponse(false, "Dont have permission to update");
        }
        \DB::beginTransaction();

        try {
            $post = $request->all();
dd($post);
            $vendor = Vendor::findOrFail($id);

            $post = formatPostForJsonColumn($post);
            /* Saving name alongwith id in json column takki join se na retrive karna pade
               copy this code from contrller file and paste and edit here 
              $post=$this->processJsonColumnToAddNameOrAddtionalData($post);
            */
            $vendor->update($post);
            $this->afterCreateProcess($request, $post, $vendor);
            \DB::commit();
            return createResponse(
                true,
                $this->crud_title . " updated successfully",
                $this->index_url
            );
        } catch (\Exception $ex) {
            \Sentry\captureException($ex);
            \DB::rollback();
            return createResponse(false, $ex->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can("delete_vendors")) {
            return createResponse(false, "Dont have permission to delete");
        }
        \DB::beginTransaction();
        try {
            if (Vendor::where("id", $id)->exists()) {
                Vendor::destroy($id);
            }

            if ($this->has_upload) {
                $this->deleteFile($id);
            }
            \DB::commit();
            return createResponse(
                true,
                $this->module . " Deleted successfully"
            );
        } catch (\Exception $ex) {
            \DB::rollback();
            return createResponse(false, "Failed to  Delete Properly");
        }
    }
    public function deleteFile($id)
    {
        return $this->deleteFileBase($id, $this->storage_folder);
    }

    public function exportVendor(Request $request, $type)
    {
        if (!can("export_vendors")) {
            return redirect()
                ->back()
                ->withError("Not allowed to export");
        }
        $meta_info = $this->commonVars()["data"];
        return $this->exportModel("Vendor", "vendors", $type, $meta_info);
    }
    public function load_toggle(Request $r)
    {
        $value = trim($r->val);
        $rowid = $r->has("row_id") ? $r->row_id : null;
        $row = null;
        if ($rowid) {
            $model = app("App\\Models\\" . $this->module);
            $row = $model::where("id", $rowid)->first();
        }
        $index_of_val = 0;
        $is_value_present = false;
        $i = 0;
        foreach ($this->toggable_group as $val) {
            if ($val["onval"] == $value) {
                $is_value_present = true;
                $index_of_val = $i;
                break;
            }
            $i++;
        }
        if ($is_value_present) {
            if ($row) {
                $this->toggable_group = [];
            }
            $data["inputs"] = $this->toggable_group[$index_of_val]["inputs"];

            $v = view(
                "admin.attribute_families.toggable_snippet",
                with($data)
            )->render();
            return createResponse(true, $v);
        } else {
            return createResponse(true, "");
        }
    }
    public function getImageList($id, $table, $parent_field_name)
    {
        return $this->getImageListBase(
            $id,
            $table,
            $parent_field_name,
            $this->storage_folder
        );
    }
    public function orders(Request $request)
    {
      
       $vendor_order_ids=!empty($request->get('ids'))?explode(',',$request->get('ids')):[];

         
          $is_vendor=current_role()=='Vendor'?true:false;
        $vendor_id = auth()
            ->guard("vendor")
            ->id();
        $view_columns = [
            [
                "column" => "shiprocket_shipment_id",
                "label" => "Shiprocket Shipment Id",
                "sortable" => "Yes",
            ],
            [
                "column" => "shiprocket_order_id",
                "label" => "Shiprocket Order Id",
                "sortable" => "Yes",
            ],
        ];
        $table_columns = [
            [
                "column" => "vendor_id",
                "label" => "Seller Name",
                "sortable" => "Yes",
            ],
            [
                "column" => "uuid",
                "label" => "Order Id",
                "sortable" => "Yes",
            ],

            [
                "column" => "vendor_total",
                "label" => "Amount(Rs.)",
                "sortable" => "Yes",
            ],
            [
                "column" => "net_profit",
                "label" => "Expected Profit(Rs.)",
                "sortable" => "Yes",
            ],

            [
                "column" => "awb",
                "label" => "AWB",
                "sortable" => "Yes",
            ],

            // [
            // 'column' => 'delivered_at',
            // 'label' => 'Delivered Date',
            // 'sortable' => 'Yes',
            // ],

            [
                "column" => "paid_status",
                "label" => "Paid Status",
                "sortable" => "Yes",
            ],
            [
                "column" => "delivery_status",
                "label" => "Order Status",
                "sortable" => "Yes",
            ],
            [
                "column" => "created_at",
                "label" => "Create Date",
                "sortable" => "Yes",
            ],
        ];
        $filterable_fields = [
            [
                "name" => "created_at",
                "label" => "Created At",
                "type" => "date",
            ],
            // [
            //     'name' => 'paid_status',
            //     'label' => 'Payment Status ',
            //     'type' => 'select',
            //     'options' => getListFromIndexArray(['Paid','Unpaid']),
            // ],
        ];
        $searchable_fields = [
            [
                "name" => "uuid",
                "label" => "Order Id",
                "type" => "text",
            ],
        ];
        $model_relations = [
            [
                "name" => "vendor",
                "type" => "BelongsTo",
                "save_by_key" => "",
                "column_to_show_in_view" => "name",
            ],
           
        ];
        if(!$is_vendor){
           $table_columns=array_merge($table_columns, [[
                "column" => "is_transferred",
                "label" => "is transferred?",
                "sortable" => "Yes",
           ],
        [
                "column" => "courier",
                "label" => "Courier",
                "sortable" => "Yes",
            ]],); 
        }
        $this->pagination_count = 100;
        if ($request->ajax()) {
            $sort_by = $request->get("sortby");
            $sort_type = $request->get("sorttype");
            $search_by = $request->get("search_by");

            $query = $request->get("query");

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
                $search_by = "name";
            }
            if ($search_by == "order_id") {
                $order = \DB::table("orders")
                    ->where("uuid", $search_val)
                    ->first();
                $search_val = $order ? $order->id : $search_val;
            }
            $list = \App\Models\VendorOrder::when(
                !empty($search_val),
                function ($query) use ($search_val, $search_by) {
                    return $query->where(
                        $search_by,
                        "like",
                        "%" . $search_val . "%"
                    );
                }
            )
                ->when(!empty($vendor_id), function ($query) use ($vendor_id) {
                    return $query->where("vendor_id", $vendor_id);
                })
                ->when(count($vendor_order_ids)>0, function ($query) use ($vendor_order_ids) {
                    return $query->whereIn("id", $vendor_order_ids);
                })
                ->when(!empty($sort_by), function ($query) use (
                    $sort_by,
                    $sort_type
                ) {
                    return $query->orderBy($sort_by, $sort_type);
                })
                ->where("status", "Success")
                ->latest()
                ->paginate($this->pagination_count);
            $data = [
                "table_columns" => $table_columns,
                "list" => $list,
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "vendor_order",
                "sort_by" => $sort_by,
                "sort_type" => $sort_type,
                "storage_folder" => "",
                "plural_lowercase" => "vendor_order",
                "module" => "VendorOrder",
                "has_image" => 0,
                "model_relations" => [],
                "image_field_names" => [],
                "crud_title" => "Vendor Order",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                "bulk_update" => json_encode([
                    "order_ship_status" => [
                        "label" => "Transfer To Courier Service",
                        "data" => getListFromIndexArray(["Yes", "No"]),
                    ],
                ]),
            ];
            return $is_vendor?view("vendors.orders.page", with($data)):view("admin.vendors.order_page", with($data));
        } else {
            $query = null;

            $query = \App\Models\VendorOrder::when(
                !empty($vendor_id),
                function ($query) use ($vendor_id) {
                    return $query->where("vendor_id", $vendor_id);
                }
            )->when(count($vendor_order_ids)>0, function ($query) use ($vendor_order_ids) {
                    return $query->whereIn("id", $vendor_order_ids);
                })->where("status", "Success");
          
            $query = $this->buildFilter($request, $query,['ids']);
            $list = $query->latest()->paginate($this->pagination_count);

            $view_data = [
                "list" => $list,

                "title" => "Vendor Order",
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "vendor_order",
                "table_columns" => $table_columns,
                "module_table_name" => "vendor_orders",

                "model_relations" => $model_relations,
                "module" => "VendorOrder",
                "crud_title" => "Vendor Order",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                "bulk_update" => json_encode([
                    "is_transferred" => [
                        "label" => "Transfer To Courier Service",
                        "data" => getListFromIndexArray(["Yes", "No"]),
                    ],
                ]),
            ];
           
            return $is_vendor?view("vendor.orders.index", $view_data):view("admin.vendors.order", $view_data);
        }
    }
  
    public function return_shipments(Request $request)
    {
       
         $is_vendor=current_role()=='Vendor'?true:false;
        $vendor_id = auth()
            ->guard("vendor")
            ->id();
        $view_columns = [];
        $table_columns = [
            [
                "column" => "uuid",
                "label" => "Return Order Id",
                "sortable" => "Yes",
            ],
            [
                "column" => "vendor_order_id",
                "label" => "Vendor Order Id",
                "sortable" => "Yes",
            ],

            [
                "column" => "is_transferred",
                "label" => "Is Shipped?",
                "sortable" => "Yes",
            ],

            [
                "column" => "created_at",
                "label" => "Create Date",
                "sortable" => "Yes",
            ],
        ];
        $filterable_fields = [
            [
                "name" => "created_at",
                "label" => "Created At",
                "type" => "date",
            ],
            // [
            //     'name' => 'paid_status',
            //     'label' => 'Payment Status ',
            //     'type' => 'select',
            //     'options' => getListFromIndexArray(['Paid','Unpaid']),
            // ],
        ];
        $searchable_fields = [
            [
                "name" => "uuid",
                "label" => "Return Order Id",
                "type" => "text",
            ],
        ];
        $model_relations = [
            [
                "name" => "vendor_order",
                "type" => "BelongsTo",
                "save_by_key" => "",
                "column_to_show_in_view" => "name",
            ],
        ];
        $this->pagination_count = 100;
        if ($request->ajax()) {
            $sort_by = $request->get("sortby");
            $sort_type = $request->get("sorttype");
            $search_by = $request->get("search_by");

            $query = $request->get("query");

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
                $search_by = "name";
            }
            if ($search_by == "order_id") {
                $order = \DB::table("orders")
                    ->where("uuid", $search_val)
                    ->first();
                $search_val = $order ? $order->id : $search_val;
            }
            $list = \App\Models\ReturnShipment::with("vendor_order:id,uuid")
                ->when(!empty($search_val), function ($query) use (
                    $search_val,
                    $search_by
                ) {
                    return $query->where(
                        $search_by,
                        "like",
                        "%" . $search_val . "%"
                    );
                })
                ->when(!empty($vendor_id), function ($query) use ($vendor_id) {
                    return $query->where("vendor_id", $vendor_id);
                })
                ->when(!empty($sort_by), function ($query) use (
                    $sort_by,
                    $sort_type
                ) {
                    return $query->orderBy($sort_by, $sort_type);
                })
                ->latest()
                ->paginate($this->pagination_count);
            $data = [
                "table_columns" => $table_columns,
                "list" => $list,
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "vendor_order",
                "sort_by" => $sort_by,
                "sort_type" => $sort_type,
                "storage_folder" => "",
                "plural_lowercase" => "vendor_order",
                "module" => "VendorOrder",
                "has_image" => 0,
                "model_relations" => [],
                "image_field_names" => [],
                "crud_title" => "Vendor Order",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                "bulk_update" => "",
            ];
            return $is_vendor?view("vendor.return_orders.index", $view_data):view("admin.vendors.return_shipment_page", with($data));
        } else {
            $query = null;

            $query = \App\Models\ReturnShipment::with(
                "vendor_order:id,uuid"
            )->when(!empty($vendor_id), function ($query) use ($vendor_id) {
                return $query->where("vendor_id", $vendor_id);
            });

            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);

            $view_data = [
                "list" => $list,

                "title" => "Return Shipemnt Order",
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "return_shipments",
                "table_columns" => $table_columns,
                "module_table_name" => "return_shipments",

                "model_relations" => $model_relations,
                "module" => "ReturnShipment",
                "crud_title" => "Return Shipmnet ",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                "bulk_update" => "",
            ];
            return $is_vendor?view("vendor.return_orders.index", $view_data):view("admin.vendors.return_shipments", $view_data);
        }
    }
    public function getVendorOrderDetail(Request $request)
    {
        $vendor_id = $request->vendor_id;
        $order_id = $request->order_id;
        $q = \App\Models\VendorOrder::with([
            "order.items" => function ($query) use ($vendor_id) {
                $query->where("vendor_id", $vendor_id);
            },
        ])
            ->whereVendorId($vendor_id)
            ->whereOrderId($order_id)
            ->first();
        $orderItemIds = optional($q->order)
            ->items->pluck("id")
            ->all();
        // dd($orderItemIds);
        $order_items = \DB::table("order_items as oi")
            ->select(
                "p.name",
                "p.image",
                "oi.product_id",
                "oi.variant_id",
                "oi.sale_price",
                "oi.discount_share",
                "oi.qty",
                "pv.atributes_json"
            )
            ->leftJoin("product_variants as pv", "pv.id", "oi.variant_id")
            ->leftJoin("products as p", "p.id", "oi.product_id")
            ->whereIn("oi.id", $orderItemIds)
            ->get();

        return view(
            "admin.vendors.partial_order_detail",
            compact("order_items")
        )->render();
    }
   public function sales(Request $request)
{
    $vendor_id = auth()->guard("vendor")->id();
    
    $query = \App\Models\VendorOrder::with([
        "order.items.product",
        "order.items.variant",
        ])->whereVendorId($vendor_id)
        ->where('is_completed','Yes');

    // Filter by order UUID + vendor_id
    if ($request->filled('order_uuid')) {
        $input = $request->order_uuid;
        $vendorIdLength = strlen($vendor_id);
        $orderUuid = substr($input, 0, -$vendorIdLength); // Remove vendor ID from end
        $query->whereHas('order', function($q) use ($orderUuid) {
            $q->where('uuid', $orderUuid);
        });
    }

    // Optional: filter by start & end date
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
        
    }
    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    
    }

    $data['orders'] = $query->get();

    return view("vendor.sales", with($data));
}

    public function generateDocumentPost(Request $request)
    {
        $shipservice = app(ShiprocketService::class);
        $type = $request->type; // 'label', 'manifest', 'invoice'

        $id = $request->id;
        $resp = $shipservice->generateDocument($type, $id);

        if ($type == "label") {
            if ($resp["label_created"]) {
                return response()->json(["url" => $resp["label_url"]]);
            } else {
                return response()->json(
                    ["error" => "Failed to generate label"],
                    400
                );
            }
        } elseif ($type == "manifest") {
            if (isset($resp["manifest_url"])) {
                return response()->json(["url" => $resp["manifest_url"]]);
            } else {
                return response()->json(["error" => $resp["message"]], 400);
            }
        } elseif ($type == "invoice") {
            // dd($resp);
            if (isset($resp["invoice_url"])) {
                return response()->json(["url" => $resp["invoice_url"]]);
            } else {
                return response()->json(["error" => $resp["message"]], 400);
            }
        }
        return response()->json(
            ["error" => "Failed to generate document"],
            400
        );
    }
    public function vendor_bank_list()
{
    $user_id = auth()->guard("vendor")->id();
    $banks = \App\Models\VendorBank::where("vendor_id", $user_id)->get();
    return view("vendor.bank_list", compact("banks"));
}

public function vendor_bank_form(Request $request, $id = null)
{
    $user_id = auth()->guard("vendor")->id();
    $bankDetail = $id
        ? \App\Models\VendorBank::where("vendor_id", $user_id)->where("id", $id)->firstOrFail()
        : new \App\Models\VendorBank();

    if ($request->isMethod("post")) {
        $validated = $request->validate([
            "account_holder" => "required|string|max:255",
            "account_number" => "required|string|max:50",
            "ifsc_code"      => "required|string|max:20",
            "bank_name"      => "required|string|max:255",
            "branch_name"    => "nullable|string|max:255",
        ]);

        $validated["vendor_id"] = $user_id;
        $bankDetail->fill($validated)->save();

        return redirect()->route("vendor.bank.list")->with("success", $id ? "Bank updated" : "Bank added");
    }

    return view("vendor.bank", compact("bankDetail"));
}

    public function barcode($d, $invoice)
    {
        //$awb = '19041760409866'; // Example AWB number from Shiprocket
        $barcode = new DNS1D();
        $barcode->setStorPath(__DIR__ . "/cache/");

        // Get barcode HTML
        $data["html"] = $barcode->getBarcodeHTML($d, "C128");
        $data["invoice"] = $barcode->getBarcodeHTML($invoice, "C128");
        return view("admin.vendors.invoice", with($data));
    }

    public function transferReturnShipments(Request $r, $id)
    {
        $return = \App\Models\ReturnShipment::with([
            "vendor:id,name,email,delhivery_pickup_name,pickup_location_name",

            "return_items" => function ($query) {
                $query->select(
                    "id",
                    "order_item_id",
                    "reason",
                    "exchange_variant_id",'return_status_updates','return_status',
                    "return_shipment_id"
                );
            },
            "return_items.order_item" => function ($query) {
                $query->select(
                    "id",
                    "product_id",
                    "variant_id",
                    "discount_share"
                ); // include foreign keys
            },
            "return_items.exchange_variant" => function ($query) {
                $query->select(
                    "id",
                    "name",
                    "atributes_json",
                    "sale_price",
                    "sku",
                    "product_id"
                ); // include foreign keys
            },
            "return_items.exchange_variant.product" => function ($query) {
                $query->select(
                    "id",
                    "name",
                    "sale_price",
                    "sku",
                    "package_dimension"
                ); // include foreign keys
            },
            "return_items.order_item.product" => function ($query) {
                $query->select("id", "name", "sale_price", "sku", "image"); // customize columns you want
            },
            "return_items.order_item.variant" => function ($query) {
                $query->select("id", "name", "sku", "sale_price", "image"); // customize columns you want
            },
            "vendor_order" => function ($query) {
                $query->select("id", "order_id", "vendor_id", "uuid"); // include foreign key for 'order'
            },
            "vendor_order.order" => function ($query) {
                $query->select(
                    "id",
                    "user_id",
                    "uuid",
                    "shipping_address_id",
                    "payment_method"
                ); // include key for 'shipping_address'
            },
            "vendor_order.order.user" => function ($query) {
                $query->select("id", "name", "phone", "email"); // include key for 'shipping_address'
            },
            "vendor_order.order.shipping_address",
            "vendor_order.order.shipping_address.city" => function ($query) {
                $query->select("id", "name"); // city columns
            },
            "vendor_order.order.shipping_address.state" => function ($query) {
                $query->select("id", "name"); // state columns
            },
        ])
            ->where("id", $id)
            ->first();
        //dd($return->toArray());
        $order_items = [];
         $vendor = $return->vendor;
         if ($return->is_transferred == "No") {
            $current_courier=setting()->courier;
             $service = $current_courier=='Shiprocket'? app(ShiprocketService::class):app(DelhiveryService::class);
            $resp= $service->transferReturnExhShipments($return);
            if (!($response['success'] ?? false)) {
                    return back()->withError($response['message'] ?? "Failed to transfer return shipment id ".$return->uuid);
                }
            
                 $newStatus = [
                            "icon" => "Approved",
                            "status" => "APPROVED",
                            "date" => now(),
                            "message" => "",
                    ];
                     DB::transaction(function () use ($return, $response, $current_courier, $vendor) {
                        $return_status_updates=json_decode($return->return_status_updates,true);
                        $return_status_updates[] = $newStatus;
                        $return->return_status = $newStatus["status"];
                        $return->shiprocket_shipment_id = $current_courier=='Shiprocket'?$response["shipment_id"]: null;
                        $return->shiprocket_order_id = $current_courier=='Shiprocket'? $response["order_id"]: null;
                        $return->is_transferred = 'Yes';

                        //Awb in case of shiprocket is generated after mainfest generation means when 
                        // admin assign courier but in delhivery assigned instanlty 
                        //in delhivery for exchange request new waybill is generated not for return request
                        $return->awb =$return->type == "Exchange" &&  $current_courier=='Delhivery'? $response['packages'][0]['waybill']:null;
                    
                        $return->save();
                        foreach ($return->return_items as $item) {
                        

                            $updates = json_decode($item->return_status_updates ?? "[]", true);
                            $updates[] = $newStatus;

                            $item->return_status = $newStatus["status"];
                            $item->return_status_updates = json_encode($updates);
                            $item->save();
                        }
                });
                $str = view("mails.return_order", [
                    "type" => $return->type,
                    "vendor_name" => $vendor->name,
                    "return_id" => $return->uuid,
                    "order_id" =>  $return->vendor_order->order->uuid,
                ])->render();
                $resp = $this->mail(
                    $vendor->email,
                    "New {$return->type} Order received",
                    $str
                );
                return back()->withSuccess("{$return->type} Shipment Transferred Successfully");
           
         }
         else {
            return redirect()
                ->back()
                ->withError("Already transferred");
        }
    }
    public function showLabel($vendorOrderId)
    {
        $current_courier=setting()->courier;
        $vendorId = \Auth::guard("vendor")->id();
          $vendorOrder = \App\Models\VendorOrder::with([
            "order",
            "order.user:id,name,phone,email",
            "order.items.product",
            "order.items.variant",
            "order.billing_address",
            "order.shipping_address",
            "vendor",
        ])->findOrFail($vendorOrderId);
        if($current_courier=='Shiprocket'){
          
        $shipservice = app(ShiprocketService::class);

      

        // Filter items for this vendor only
        $items = $vendorOrder->order->items->where("vendor_id", $vendorId);

        $url = $shipservice->generateDocument(
            "label",
            $vendorOrder->shiprocket_shipment_id
        );
        if (!isset($url["label_url"]) || empty($url["label_url"])) {
            return redirect()
                ->back()
                ->withError("Faliled to generate label");
        }
        $routing_code = "NA";
        $url = $url["label_url"];
        return redirect($url);
    }
    else{
       $delservice = app(DelhiveryService::class);
       $resp=$delservice->packingSlip( $vendorOrder->awb);
       dd($resp);

    }
        // $routing_code = extractTextValueFromPdfUrl($url, "Routing Code:");
        // $dimension = extractTextValueFromPdfUrl($url, "Dimensions:");
        // $weight = extractTextValueFromPdfUrl($url, "Weight:");

        // if (empty($routing_code) || empty($dimension) || empty($weight)) {
        //     return $url;
        //     echo "some value from pdf caould not be extracted ";
        //     die();
        // }

        
        // $pdf = Pdf::loadView("label", [
        //     "routing_code" => $routing_code,
        //     "vendorOrder" => $vendorOrder,
        //     "order" => $vendorOrder->order,
        //     "items" => $items,
        //     "dimension" => $dimension,
        //     "weight" => $weight,
        // ]);
        // return $pdf->download("shipping-label_" . $vendorOrder->id . ".pdf");
    }
    public function updateOrderShippingCharge(Request $request){
        $vendor_order_id=$request->vendor_order_id;
        $shipping_charge=$request->shipping_charge;
        \DB::Table('vendor_orders')->where('id',$vendor_order_id)->update(['shipping_cost'=>$shipping_charge]);
        return createResponse(true,"Changed Successfully");
    }
    public function training_videos(Request $request){
      
       $data['videos'] =\DB::table('training_videos')->where('published','Yes')->latest()->get();
        return view('vendor.training_videos',with($data));
    }
    public function earning_settlement(Request $request)
    {
       $vendor_id=auth()->guard('vendor')->id();
         
        $view_columns =[];
         $table_columns = [
           
            [
                "column" => "amount",
                "label" => "Amount Earned",
                "sortable" => "Yes",
            ],

            [
                "column" => "paid_status",
                "label" => "Paid Status",
                "sortable" => "Yes",
            ],
           

            [
                "column" => "updated_at",
                "label" => "Settled Date",
                "sortable" => "Yes",
            ],
        ];
        $filterable_fields = [
            [
                "name" => "created_at",
                "label" => "Created At",
                "type" => "date",
            ],
            [
                "name" => "paid_status",
                "label" => "Paid Status",
                "type" => "select",
                'options'=>getListFromIndexArray(['Pending','Paid'])
            ],
            [
                "name" => "vendor_id",
                "label" => "Vendor",
                "type" => "select",
                'options'=>getList('Vendor')
            ],
         
        ];
        $searchable_fields = [
            

          
        ];
        $model_relations = [
           
        ];
        $this->pagination_count = 100;
        if ($request->ajax()) {
            $sort_by = $request->get("sortby");
            $sort_type = $request->get("sorttype");
            $search_by = $request->get("search_by");

            $query = $request->get("query");

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
                $search_by = "name";
            }
           
            $list = \App\Models\VendorSettlement::where('vendor_id',$vendor_id)->when(!empty($search_val), function ($query) use (
                    $search_val,
                    $search_by
                ) {
                    return $query->where(
                        $search_by,
                        "like",
                        "%" . $search_val . "%"
                    );
                })
               
                ->when(!empty($sort_by), function ($query) use (
                    $sort_by,
                    $sort_type
                ) {
                    return $query->orderBy($sort_by, $sort_type);
                })
                ->latest()
                ->paginate($this->pagination_count);
               
            $data = [
                "table_columns" => $table_columns,
                "list" => $list,
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "vendor_settlements",
                "sort_by" => $sort_by,
                "sort_type" => $sort_type,
                "storage_folder" => "",
                "plural_lowercase" => "vendor_settlements",
                "module" => "VendorSettlement",
                "has_image" => 0,
                "model_relations" => [],
                "image_field_names" => [],
                "crud_title" => "Vendor Settlement",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                 "bulk_update" => '',
            ];
            return view("vendor.settlements.page",with($data));
        } else {
            $query = null;

            $query = \App\Models\VendorSettlement::where('vendor_id',$vendor_id);

            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);

            $view_data = [
                "list" => $list,

                "title" => "Vendor Settlement",
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "vendor_settlements",
                "table_columns" => $table_columns,
                "module_table_name" => "vendor_settlements",

                "model_relations" => $model_relations,
                "module" => "VendorSettlement",
                "crud_title" => "Vendor Settlement ",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                "bulk_update" => json_encode([
                    "paid_status" => [
                        "label" => "Set Paid",
                        "data" => getListFromIndexArray(["Paid", "Pending"]),
                    ],
                ]),
            ];
            return view("vendor.settlements.index", $view_data);
        }
    }


}
