<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use App\Models\Order;
use DB;
class CommonController extends Controller
{
    public function field_exist(Request $r)
    {
        $exist = fieldExist($r->model, $r->field, $r->value);
        if ($exist) {
            return response()->json("Email already exist", 200);
        } else {
            return response()->json("true", 200);
        }
    }
    public function getCities(Request $r)
    {
        $state = $r->state;
        $dependent_key = $r->dependent_key;
        $table = $r->table;
        $table_id = $r->table_id;
        $value = $r->value;
        $where = $r->where;

        $t = \DB::table("cities")
            ->whereStateId($state)
            ->get();
        $str = "<option value=''>Select city</option>";
        $i = 0;
        foreach ($t as $r) {
            $selected = "";
            ++$i;
            $str .=
                "<option value='" .
                $r->id .
                "'" .
                $selected .
                ">" .
                $r->name .
                "</option>";
        }
        return response()->json(["success" => true, "message" => $str]);
    }
    public function fetchRowFromTable(Request $r)
    {
        $table = $r->table;
        $id = $r->id;
        $row = \DB::table($table)
            ->whereId($id)
            ->first();
        if ($row) {
            return createResponse(true, $row);
        } else {
            return createResponse(false, "Not foud");
        }
    }
    public function search_table(Request $r)
    {
        $table = $r->search_table;
        $search_in_column = $r->search_by_column;
        $search_value = $r->value;
        $id_column = $r->search_id_column;
        $name_column = $r->search_name_column;
        $where = $r->has("where") ? json_decode($r->where, true) : null;
        $whereIn = $r->has("whereIn") ? json_decode($r->whereIn, true) : null;

        $query = \DB::table($table)->where(
            $search_in_column,
            "LIKE",
            $search_value . "%"
        );
        if ($where) {
            $query = $query->where($where);
        }
        if ($whereIn) {
            $col = array_keys($whereIn)[0];
            $query = $query->whereIn($col, $whereIn[$col]);
        }

        $list = $query->get([$id_column, $name_column])->toArray();

        if (count($list) > 0) {
            $ar = [];
            foreach ($list as $item) {
                array_push($ar, [
                    "id" => $item->{$id_column},
                    "text" => $item->{$name_column},
                ]);
            }
            return createResponse(true, $ar);
        } else {
            return createResponse(false, "");
        }
    }
    public function search_products(Request $r)
    {
        $cat_ids = json_decode($r->category_ids, true);
        $search_term = $r->q;

        $query = null;
        if ($cat_ids) {
            $query = \DB::table("products")
                ->whereIn("category_id", $cat_ids)
                ->where("name", "LIKE", "%" . $search_term . "%");
        } else {
            $query = \DB::table("products")
                ->where("category_id", $cat_ids)
                ->where("name", "LIKE", "%" . $search_term . "%");
        }

        $list = $query->get(["id", "name"])->toArray();

        if (count($list) > 0) {
            $ar = [];
            foreach ($list as $item) {
                array_push($ar, ["id" => $item->id, "text" => $item->name]);
            }
            return createResponse(true, $ar);
        } else {
            return createResponse(false, "");
        }
    }
    public function getColumnsFromTable(Request $r)
    {
        $table = $r->table;
        \Session::put("table", $table);
        $field_name = $r->field_name;
        $type = $r->type;
        $multiple = $type == "checkbox" ? "multiple" : "";
        $event_call = $r->has("event") ? $r->event : "";
        $cols = \Schema::getColumnListing($table);
        $resp = "";
        $resp =
            '<div class="form-group"><label  class="form-label">Select Fields</label>
        <select id="' .
            $field_name .
            '" name="' .
            $field_name .
            '[]" ' .
            $multiple .
            ' class="form-control"  tabindex="-1" aria-hidden="true" onchange="' .
            $event_call .
            '">';
        $options = ' <option value="" selected="" ></option>';

        foreach ($cols as $col) {
            $options .= ' <option value="' . $col . '" >' . $col . "</option>";
        }
        $resp .= $options . "</select> </div>";

        return createResponse(true, $resp);
    }
    public function getColumnsFromTableCheckbox(Request $r)
    {
        $table = $r->table;
        $field_name = $r->field_name;
        $cols = \Schema::getColumnListing($table);
        $resp = "";
        $resp = "";
        foreach ($cols as $col) {
            $resp .=
                '<div class="form-check form-check-inline">
        <input type="checkbox" name="' .
                $field_name .
                '[]" value="' .
                $col .
                '"
           class="form-check-input" aria-invalid="false">
        <label class="form-check-label">' .
                $col .
                '</label>
            </div>';
        }

        return createResponse(true, $resp);
    }

    public function getValidationHtml(Request $r)
    {
        $field_name = $r->field_name;

        $cols = getValidation();

        $resp = "";
        foreach ($cols as $col) {
            $resp .=
                '<div class="form-check form-check-inline">
                  <input  type="checkbox" name="' .
                $field_name .
                '_rules[]"  value="' .
                $col->value .
                '" class="form-check-input" aria-invalid="false">
                  <label  class="form-check-label">' .
                $col->label .
                '</label>
                  </div>';
        }

        return createResponse(true, $resp);
    }
    public function getToggableGroupHtml(Request $r)
    {
        $field_name = $r->field_name;
        $table = \Session::get("table");
        $cols = \Schema::getColumnListing($table);
        $toggable_val = "";
        $select_inputs =
            '<div class="form-group"><label  class="form-label">Select Inputs</label>
        <select name="' .
            $field_name .
            '_inputtype[]"  class="form-control"  tabindex="-1" aria-hidden="true" >';
        $options = ' <option value="" selected="" >Select input type</option>';
        foreach (getInputs() as $inp) {
            $options .=
                ' <option value="' .
                $inp->value .
                '" >' .
                $inp->label .
                "</option>";
        }

        $select_inputs .= $options . "</select> </div>";
        $select_fields =
            '<div class="form-group"><label  class="form-label">Select Fields</label>
        <select name="' .
            $field_name .
            '_fields[]"  class="form-control"  tabindex="-1" aria-hidden="true" >';
        $options = ' <option value="" selected="" >Select fields</option>';
        foreach ($cols as $col) {
            $options .= ' <option value="' . $col . '" >' . $col . "</option>";
        }

        $select_fields .= $options . "</select> </div>";

        $resp =
            '<fieldset class="form-group border p-3 fieldset">
       <legend class="w-auto px-2 legend">Inputs Generation </legend>
       <div id="nested_togggle" class="toggable_group" style="margin-bottom:5px">
           <div class="row">

               <div class="col-md-12">
                   <div class="d-flex justify-content-end">

                       <button type="button" class="btn btn-success btn-xs mr-5"
                           onclick="addPlusToggleNested()">+</button>


                       <button type="button" class="btn btn-danger btn-xs"
                           onclick="removeMinusToggleNested()">-</button>

                   </div>
               </div>
           </div>
           <div class="row copy_row1 border-1">
           <div class="col-md-2 mb-3">' .
            $select_fields .
            '
           </div>
               <div class="col-md-2 mb-3">' .
            $select_inputs .
            '
               </div>




               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Options(Comma seprated) </label>
                       <input type="text" id="module" name="' .
            $field_name .
            '_options[]" value=""
                           class="form-control valid is-valid" placeholder="toption1,option2,option3..">

                   </div>
               </div>

               <div class="col-md-2 mb-3">
                   <p>Is Multiple?
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' .
            $field_name .
            '_multiple[]" value="Yes"
                           class="form-check-input valid is-valid" aria-invalid="false">
                       <label class="form-check-label">Yes</label>
                   </div>
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' .
            $field_name .
            '_multiple[]" value="No"
                           class="form-check-input valid is-valid" aria-invalid="false">
                       <label class="form-check-label">No</label>
                   </div>
               </div>
               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Atrr.(Options(Comma seprated)) </label>
                       <input type="text" id="module" name="' .
            $field_name .
            '_attributes[]" value=""
                           class="form-control valid is-valid" placeholder="multiple=>true,onChange=>somfunciton">

                   </div>
               </div>

           </div>
           <hr>
     </div>

   </fieldset>';

        $toggable_val =
            '<label for="name" class="form-label">Value</label>
           <input type="text"  name="' .
            $field_name .
            '_toggable_val[]" class="form-control valid is-valid" placeholder="Enter conditional value" />';
        return createResponse(true, [
            "label" => $toggable_val,
            "html" => $resp,
        ]);
    }
    public function getRepeatableHtml(Request $r)
    {
        $field_name = $r->field_name;

        $label = "";
        $select =
            '<div class="form-group"><label  class="form-label">Select Inputs</label>
        <select name="' .
            $field_name .
            '_inputtype[]"  id="repeatable_' .
            $field_name .
            '" class="form-control"  tabindex="-1" aria-hidden="true" >';
        $options = ' <option value="" selected="" >Select input type</option>';
        foreach (getInputs() as $inp) {
            $options .=
                ' <option value="' .
                $inp->value .
                '" >' .
                $inp->label .
                "</option>";
        }

        $select .= $options . "</select> </div>";

        $resp =
            '<fieldset class="form-group border p-3 fieldset">
       <legend class="w-auto px-2 legend">Inputs Generation </legend>
       <div  class="repeatable3" style="margin-bottom:5px">
           <div class="row">

               <div class="col-md-12">
                   <div class="d-flex justify-content-end">

                       <button type="button" class="btn btn-success btn-xs mr-5"
                           onclick="addPlusRepeatable()">+</button>


                       <button type="button" class="btn btn-danger btn-xs"
                           onclick="removeMinusRepeatable()">-</button>

                   </div>
               </div>
           </div>
           <div class="row copy_row1 border-1">
               <div class="col-md-2 mb-3">' .
            $select .
            '
               </div>
               <div class="col-md-2 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Key Name</label>
                       <input type="text" id="module" name="' .
            $field_name .
            '_keys[]" value=""
                           class="form-control valid is-valid" placeholder="Enter keyy name for json">

                   </div>
               </div>

               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Options(Comma seprated) </label>
                       <input type="text" id="module" name="' .
            $field_name .
            '_options[]" value=""
                           class="form-control valid is-valid" placeholder="toption1,option2,option3..">

                   </div>
               </div>

               <div class="col-md-2 mb-3">
                   <p>Is Multiple?
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' .
            $field_name .
            '_multiple[]" value="Yes"
                           class="form-check-input valid is-valid" aria-invalid="false">
                       <label class="form-check-label">Yes</label>
                   </div>
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' .
            $field_name .
            '_multiple[]" value="No"
                           class="form-check-input valid is-valid" aria-invalid="false">
                       <label class="form-check-label">No</label>
                   </div>
               </div>
               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Atrr.(Options(Comma seprated)) </label>
                       <input type="text" id="module" name="' .
            $field_name .
            '_attributes[]" value=""
                           class="form-control valid is-valid" placeholder="multiple=>true,onChange=>somfunciton">

                   </div>
               </div>

           </div>
           <hr>
     </div>

   </fieldset>';

        $label =
            '<label for="name" class="form-label">Label</label>
           <input type="text"  name="' .
            $field_name .
            '_label[]" class="form-control valid is-valid" placeholder="Enter  label" />';
        $priority =
            '<label for="name" class="form-label">Priority</label>
           <input type="number"  name="' .
            $field_name .
            '_priority[]" class="form-control valid is-valid" placeholder="Enter  priorit" />';
        return createResponse(true, [
            "label" => $label,
            "html" => $resp,
            "priority" => $priority,
        ]);
    }
    public function getCreateInputOptionHtml(Request $r)
    {
        $field_name = $r->field_name;
        $index = $r->cur_index;

        $label = "";
        $select =
            '<div class="form-group"><label  class="form-label">Select Inputs</label>
        <select id="repeatable_' .
            $field_name .
            '" name="' .
            $field_name .
            "_inputtype_create_" .
            $index .
            '[]"  class="form-control"  tabindex="-1" aria-hidden="true" >';
        $options = ' <option value="" selected="" >Select input type</option>';
        foreach (getInputs() as $inp) {
            $options .=
                ' <option value="' .
                $inp->value .
                '" >' .
                $inp->label .
                "</option>";
        }

        $select .= $options . "</select> </div>";

        $resp =
            ' <div class="row copy_row1 border-1">
               <div class="col-md-2 mb-3">' .
            $select .
            '
               </div>


               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Options(Comma seprated) </label>
                       <input type="text" name="' .
            $field_name .
            "_options_create_" .
            $index .
            '[]" value=""
                           class="form-control valid is-valid" placeholder="toption1,option2,option3..">

                   </div>
               </div>

               <div class="col-md-3 mb-3">
                   <p>Is Multiple?
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' .
            $field_name .
            "_multiple_create_" .
            $index .
            '[]" value="Yes"
                           class="form-check-input >
                       <label class="form-check-label">Yes</label>
                   </div>
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' .
            $field_name .
            "_multiple_create_" .
            $index .
            '[]" value="No"
                           class="form-check-input  aria-invalid="false">
                       <label class="form-check-label">No</label>
                   </div>
               </div>
               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Atrr.(Options(Comma seprated)) </label>
                       <input type="text" name="' .
            $field_name .
            "_attributes_create_" .
            $index .
            '[]" value=""
                           class="form-control valid is-valid" placeholder="multiple=>true,onChange=>somfunciton">

                   </div>
               </div>

           </div>';

        $label =
            '<label for="name" class="form-label">Label</label>
           <input type="text"  name="' .
            $field_name .
            "_label_create_" .
            $index .
            '[]" class="form-control valid is-valid" placeholder="Enter fiedset label" />';
        return createResponse(true, ["label" => $label, "html" => $resp]);
    }
    public function getSideColumnInputOptionHtml(Request $r)
    {
        $field_name = $r->field_name;
        $index = $r->cur_index;

        $label = "";
        $select =
            '<div class="form-group"><label  class="form-label">Select Inputs</label>
        <select id="repeatable_' .
            $field_name .
            '" name="' .
            $field_name .
            "_inputtype_sidecolumn_" .
            $index .
            '[]"  class="form-control"  tabindex="-1" aria-hidden="true" >';
        $options = ' <option value="" selected="" >Select input type</option>';
        foreach (getInputs() as $inp) {
            $options .=
                ' <option value="' .
                $inp->value .
                '" >' .
                $inp->label .
                "</option>";
        }

        $select .= $options . "</select> </div>";

        $resp =
            ' <div class="row copy_row1 border-1">
               <div class="col-md-2 mb-3">' .
            $select .
            '
               </div>


               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Options(Comma seprated) </label>
                       <input type="text" name="' .
            $field_name .
            "_options_sidecolumn_" .
            $index .
            '[]" value=""
                           class="form-control valid is-valid" placeholder="toption1,option2,option3..">

                   </div>
               </div>

               <div class="col-md-3 mb-3">
                   <p>Is Multiple?
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' .
            $field_name .
            "_multiple_sidecolumn_" .
            $index .
            '[]" value="Yes"
                           class="form-check-input >
                       <label class="form-check-label">Yes</label>
                   </div>
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' .
            $field_name .
            "_multiple_sidecolumn_" .
            $index .
            '[]" value="No"
                           class="form-check-input  aria-invalid="false">
                       <label class="form-check-label">No</label>
                   </div>
               </div>
               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Atrr.(Options(Comma seprated)) </label>
                       <input type="text" name="' .
            $field_name .
            "_attributes_sidecolumn_" .
            $index .
            '[]" value=""
                           class="form-control valid is-valid" placeholder="multiple=>true,onChange=>somfunciton">

                   </div>
               </div>

           </div>';

        $label =
            '<label for="name" class="form-label">Label</label>
           <input type="text"  name="' .
            $field_name .
            "_label_sidecolumn_" .
            $index .
            '[]" class="form-control valid is-valid" placeholder="Enter fiedset label" />';
        return createResponse(true, ["label" => $label, "html" => $resp]);
    }

   public function table_field_update(Request $r)
{
    DB::beginTransaction(); // Start transaction

    try {
        $table = $r->table;
        parse_str($r->form_val, $values);

        // Remove empty values
        $values = array_filter($values);

        $keys = array_keys($values);

        // Add timestamps or OTP if applicable
        if ($table === "orders") {
            if (in_array("delivery_status", $keys)) {
                $values["status_update_date"] = now();
            }

            if (in_array("driver_id", $keys)) {
                $values["otp"] = rand(100000, 999999);
            }
        }

        $ids = json_decode($r->ids, true);
        $order_status_message = $values["order_status_message"] ?? "";
        unset($values["order_status_message"]);
       
         if ($table === "vendors" && in_array("is_verified", $keys) && $values['is_verified'] === 'Yes') {
            $values['verified_at'] =date('Y-m-d H:i:s');
        }
        // Skip update if transfer status is in request
      

        // Vendor Orders Specific Logic
        if ($table === "vendor_orders") {
            // Transfer logic
             if (
                in_array("is_transferred", $keys) &&
                $values["is_transferred"] === "Yes"
            ) {
                $orders = \App\Models\VendorOrder::with([
                    "vendor:id,name,state_id,city_id,pickup_location_name,delhivery_pickup_name",
                    "vendor.state",
                    "vendor.city",
                    "order:id,user_id,uuid,payment_method,shipping_address_id,billing_address_id,shipping_cost,subtotal,net_payable,total_discount",
                    "order.user:id,email,phone",
                    "order.billing_address",
                    "order.shipping_address",
                ])
                    ->whereIn("id", $ids)
                    ->get();
                
                $orders->each(function ($vendorOrder) {
                    if($vendorOrder->is_transferred=='Yes'){
                        throw new \Exception("Order  already transferred");
                        return;
                    }
                    $orderItems = \App\Models\OrderItem::with([
                        "product:id,name,sale_price,sku,package_dimension",
                        "variant:id,sku,sale_price,name,atributes_json",
                    ])
                        ->where("order_id", $vendorOrder->order_id)
                        ->where("vendor_id", $vendorOrder->vendor_id)
                        ->get();

                    $vendorOrder->setRelation("order_items", $orderItems);
                });
                $current_courier=\DB::table('settings')->first()->courier;
            
                $service =$current_courier=='Delhivery'? app(\App\Services\DelhiveryService::class): app(\App\Services\ShiprocketService::class);
                $service->createForwardShipments($orders);
               
                 
            }

            // Settlement logic
            if (
                in_array("is_settled", $keys) &&
                $values["is_settled"] === "Yes"
            ) {
                $vendor_orders = \App\Models\VendorOrder::whereIn("id", $ids)->get();

                foreach ($vendor_orders as $vendor_order) {
                    $commission_percentage = \App\Models\Setting::first()->commission;
                    $total_sell = $vendor_order->vendor_total;
                    $platform_charge = $total_sell * $commission_percentage * 0.01;
                    $shipping_cost = $vendor_order->shipping_cost;
                    $vendor_earnings = $total_sell - ($shipping_cost + $platform_charge + $vendor_order->refunded_amount);

                    \App\Models\VendorOrder::where("id", $vendor_order->id)
                        ->update(["vendor_earnings" => $vendor_earnings]);

                    $settlement = \App\Models\VendorSettlement::where('vendor_id', $vendor_order->vendor_id)
                        ->where('paid_status', 'No')
                        ->lockForUpdate() // prevents race conditions
                        ->first();

                    if ($settlement) {
                        $orderIds = json_decode($settlement->vendor_order_ids, true) ?? [];
                        $orderIds[] = $vendor_order->id;

                        $settlement->update([
                            'amount' => $settlement->amount + $vendor_earnings,
                            'vendor_order_ids' => json_encode($orderIds),
                        ]);
                    } else {
                        \App\Models\VendorSettlement::create([
                            'vendor_id' => $vendor_order->vendor_id,
                            'created_at' => Carbon::today()->toDateString(),
                            'amount' => $vendor_earnings,
                            'vendor_order_ids' => json_encode([$vendor_order->id]),
                            'paid_status' => 'No'
                        ]);
                    }
                }
            }

            // Paid status update logic
            if (
                in_array("paid_status", $keys) &&
                $values["paid_status"] === "Paid"
            ) {
                $settlements = \App\Models\VendorSettlement::whereIn("id", $ids)->get();

                foreach ($settlements as $settlement) {
                    $settlement->update(['paid_status' => 'Paid']);
                    $vendor_order_ids = json_decode($settlement->vendor_order_ids, true);

                    if ($vendor_order_ids) {
                        \App\Models\VendorOrder::whereIn('id', $vendor_order_ids)
                            ->update([
                                'is_settled' => 'Yes',
                                'settled_at' => now()
                            ]);
                    }
                }
            }
        }
          if (!in_array("order_transfer_status", $keys)) {
            DB::table($table)
                ->whereIn("id", $ids)
                ->update($values);
        }

        DB::commit(); // Commit transaction
        return createResponse(true, "Updated successfully");
    } catch (\Throwable $e) {
        DB::rollBack(); // Rollback on error
        \Sentry\captureException($e); // Optional: log error
        return createResponse(false, $e->getMessage());
    }
}

    public function singleFieldUpdateFromTable(Request $r)
    {
        $table = $r->table;
        $id = $r->id;
        $column = $r->field;
        $val = $r->val;
        if (!$id) {
            return createResponse(false, "failed to update successfuly");
        }
        \DB::table($table)
            ->where("id", $id)
            ->update([$column => $val]);
        

        return createResponse(true, "Updated successfuly");
    }
    public function bulkDelete(Request $r)
    {
        $table = $r->table;
        $ids = json_decode($r->ids, true);
        if (Schema::hasColumn($table, "deleted_at")) {
            // Soft delete: set deleted_at timestamp
            if (count($ids) > 0) {
                \DB::table($table)
                    ->whereIn("id", $ids)
                    ->update(["deleted_at" => date("Y-m-d H:i:s")]);
            }
        } else {
            // Hard delete
            if (count($ids) > 0) {
                \DB::table($table)
                    ->whereIn("id", $ids)
                    ->delete();
            }
        }

        return createResponse(true, "Deleted successfuly");
    }
    public function deleteRecordFromTable(Request $r)
    {
        $table = $r->table;
        $id = $r->id;

        if (Schema::hasColumn($table, "deleted_at")) {
            // Soft dele
            if ($id) {
                \DB::table($table)
                    ->where("id", $id)
                    ->update(["deleted_at" => date("Y-m-d H:i:s")]);
            }
        } else {
            // Hard delete
            if ($id) {
                \DB::table($table)
                    ->where("id", $id)
                    ->delete();
            }
        }

        return createResponse(true, "Deleted successfuly");
    }
    public function deleteFileFromTable(Request $r)
    {
        $table = $r->table;
        $id = $r->id;
        $folder = $r->folder;
        $file_name = \DB::table($table)
            ->whereId($id)
            ->first()->name;
        if ($file_name) {
            $path = storage_path("app/public/" . $folder . "/" . $file_name);
            if (\File::exists($path)) {
                unlink($path);
            }
            $thumbnail_path = storage_path(
                "app/public/" . $folder . "/thumbnail"
            );
            $or_thumbnail_path = storage_path(
                "app/public/" . $folder . "/thumbnails"
            );
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            if (\File::exists($thumbnail_path)) {
                foreach (array_keys(getThumbnailDimensions()) as $size) {
                    $img = basename($path) . "_" . $size . "." . $extension;
                    $file_path = "storage/" . $folder . "/thumbnail/" . $img;

                    if (\File::exists(public_path($file_path))) {
                        \File::delete(public_path($file_path));
                    }
                }
            }
            if (\File::exists($or_thumbnail_path)) {
                foreach (array_keys(getThumbnailDimensions()) as $size) {
                    $img = basename($path) . "_" . $size . "." . $extension;
                    $file_path = "storage/" . $folder . "/thumbnails/" . $img;

                    if (\File::exists(public_path($file_path))) {
                        \File::delete(public_path($file_path));
                    }
                }
            }
            \DB::table($table)
                ->whereId($id)
                ->delete();
            return createResponse(true, "File deleted successfully");
        } else {
            return createResponse(false, "Error in deleteting File");
        }
    }
    public function deleteFileFromSelf(Request $r)
    {
        $field_name = $r->field_name;
        $folder = $r->folder_name;
        $id = $r->row_id;

        $model_name = $r->modelName;
        $model_instance = app("App\\Models\\" . $model_name);
        $file_name = $model_instance->whereId($id)->first()->{$field_name};

        $model_instance->whereId($id)->update([$field_name => null]);

        if ($file_name) {
            $path = storage_path("app/public/" . $folder . "/" . $file_name);
            if (\File::exists($path)) {
                unlink($path);
            }

            $thumbnail_path = storage_path(
                "app/public/" . $folder . "/thumbnail"
            );
            $or_thumbnail_path = storage_path(
                "app/public/" . $folder . "/thumbnails"
            );
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            dlog("yes ex", \File::exists($thumbnail_path));
            dlog("folder", $folder);
            if (\File::exists($thumbnail_path)) {
                chmod($thumbnail_path, 0777);
                $thumbs = getThumbnailsFromImage($file_name);

                foreach ($thumbs as $p) {
                    $path = storage_path(
                        "app/public/" . $folder . "/thumbnail/" . $p
                    );
                    if (\File::exists($path)) {
                        unlink($path);
                    }
                }
            }

            if (\File::exists($or_thumbnail_path)) {
                $thumbs = getThumbnailsFromImage($file_name);

                foreach ($thumbs as $p) {
                    $path = storage_path(
                        "app/public/" . $folder . "/thumbnails/" . $p
                    );
                    if (\File::exists($path)) {
                        unlink($path);
                    }
                }
            }

            return createResponse(true, "File deleted successfully");
        } else {
            return createResponse(false, "Error in deleteting File");
        }
    }
    public function deleteFileFromPath(Request $r)
    {
        $path = $r->path;

        if ($path) {
            if (\File::exists($path)) {
                unlink($path);
                \App\Models\ContentSection::whereRaw(
                    "INSTR(?, `header_image`) > 0",
                    [$path]
                )->update(["header_image" => null]);
                \App\Models\WebsiteContentSection::whereRaw(
                    "INSTR(?, `header_image`) > 0",
                    [$path]
                )->update(["header_image" => null]);
            }
            return createResponse(true, "File deleted successfully");
        } else {
            return createResponse(false, "Error in deleteting File");
        }
    }
    public function getCity(Request $r)
    {
        $state = $r->state_id;
        $t = \DB::table("city")
            ->where("state_id", $state)
            ->get();
        $str = "";
        foreach ($t as $r) {
            $str .= "<option value='" . $r->id . "'>" . $r->name . "</option>";
        }
        return response()->json(["success" => true, "message" => $str]);
    }
    public function getDependentSelectData(Request $r)
    {
        $dependee_key = $r->dependee_key;
        $dependent_key = $r->dependent_key;
        $table = $r->table;
        $table_id = $r->table_id;
        $value = $r->value;
        $where = $r->where;

        $t = \DB::table($table)
            ->where(function ($query) use ($dependee_key, $value) {
                if (is_numeric($value)) {
                    $query->where($dependee_key, "=", $value);
                } else {
                    $query->where($dependee_key, "LIKE", "%" . $value . "%");
                }
            })
            ->whereNull("deleted_at")
            ->get();
        $str = "";
        $i = 0;
        foreach ($t as $r) {
            $selected = $i == 0 ? "selected" : "";
            ++$i;
            $str .=
                "<option value='" .
                $r->{$table_id} .
                "'" .
                $selected .
                ">" .
                $r->{$dependent_key} .
                "</option>";
        }
        return response()->json(["success" => true, "message" => $str]);
    }
    public function getDependentSelectDataMultipleVal(Request $r)
    {
        $dependee_key = $r->dependee_key;
        $dependent_key = $r->dependent_key;

        $table = $r->table;
        $table_id = $r->table_id;
        $value = [];
        if ($r->value) {
            $value = json_decode($r->value, true);
        }
        //this is multiple values from parent select box so json decode it

        $t = \DB::table($table)
            ->whereIn($dependee_key, $value)
            ->get();
        $str = "";
        foreach ($t as $r) {
            $str .=
                "<option value='" .
                $r->{$table_id} .
                "'>" .
                $r->{$dependent_key} .
                "</option>";
        }
        return response()->json(["success" => true, "message" => $str]);
    }
    public function load_Category(Request $request)
    {
        /*****load category for fast select  */ $query = $request->input(
            "query"
        );
        $ar = [];
        if (!empty($query)) {
            $cat_options = \App\Models\Category::where(
                "name",
                "like",
                "%" . $query . "%"
            )
                ->orWhere("slug", "like", "%" . $query . "%")
                ->get(["id", "name"]);

            foreach ($cat_options as $t) {
                array_push($ar, ["text" => $t->name, "value" => $t->id]);
            }
        }
        return response()->json($ar);
    }
    public function assignUser(Request $r)
    {
        try {
            $rowids = json_decode($r->ids, true);

            $selected_user = $r->selected_users;
            $set_in_table = $r->set_in_table;
            $field_to_set = $r->field_to_set;
            \DB::table($set_in_table)
                ->whereIn("id", $rowids)
                ->update([$field_to_set => $selected_user]);
            return response()->json([
                "success" => true,
                "message" => "Assigned successfully",
            ]);
        } catch (\Exception $ex) {
            \Sentry\captureException($ex);
            return response()->json([
                "success" => false,
                "message" => $ex->getMessage(),
            ]);
        }
    }
    public function getModelFieldValueById(Request $r)
    {
        if ($r->ajax()) {
            $model = $r->model;
            $id = $r->id;
            $field = $r->field;
            $response = getFieldById($model, $id, $field);
            return createResponse(true, $response);
        }
    }
    public function getUnitByMeterialId(Request $r)
    {
        if ($r->ajax()) {
            $id = $r->material_id;
            $row = \DB::table("input_material AS A")
                ->select("B.name")
                ->join("unit AS B", "B.id", "=", "A.unit_id")
                ->where("A.id", $id)
                ->first();

            $response = $row ? $row->name : "";
            return createResponse(true, $response);
        }
    }
    public function deleteInJsonColumnData(Request $r)
    {
        if ($r->ajax()) {
            \DB::beginTransaction();
            try {
                $rowid = $r->row_id;
                $json_column_name = $r->json_column_name;
                $key = $r->by_json_key;
                $json_key_val = $r->json_key_val;
                $table = $r->table;
                $t = \DB::table($table)
                    ->whereId($rowid)
                    ->first();
                if (is_null($t)) {
                    return createResponse(
                        false,
                        "Please refresh the page and try again"
                    );
                }
                $existing_json_data = $t->{$json_column_name}
                    ? json_decode($t->{$json_column_name}, true)
                    : [];
                if (!empty($existing_json_data)) {
                    $i = 0;
                    foreach ($existing_json_data as $item) {
                        if ($item[$key] == $json_key_val) {
                            break;
                        }
                        $i++;
                    }
                    unset($existing_json_data[$i]);

                    $updated_data = json_encode(
                        array_values($existing_json_data)
                    );
                    \DB::table($table)
                        ->whereId($rowid)
                        ->update([$json_column_name => $updated_data]);
                }
                \DB::commit();
                return createResponse(true, "Deleted  successfullly");
            } catch (\Exception $ex) {
                \Sentry\captureException($ex);
                \DB::rollback();
                return createResponse(false, $ex->getMessage());
            }
        } else {
            return createResponse(false, "Invalid Request");
        }
    }
}
