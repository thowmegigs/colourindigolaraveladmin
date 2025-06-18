<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use \App\Models\Order;
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

        $t = \DB::table('cities')->whereStateId($state)->get();
      $str = "<option value=''>Select city</option>";
        $i=0;
        foreach ($t as $r) {
             $selected='';
             ++$i;
            $str .= "<option value='" . $r->id . "'".$selected.">". $r->name . "</option>";
        }
        return response()->json(['success' => true, 'message' => $str]);

    }
    public function fetchRowFromTable(Request $r)
    {
        $table = $r->table;
        $id = $r->id;
        $row = \DB::table($table)->whereId($id)->first();
        if ($row) {
            return createResponse(true, $row);
        } else {
            return createResponse(false, 'Not foud');
        }

    }
    public function search_table(Request $r)
    {

        $table = $r->search_table;
        $search_in_column = $r->search_by_column;
        $search_value = $r->value;
        $id_column = $r->search_id_column;
        $name_column = $r->search_name_column;
        $where = $r->has('where') ? json_decode($r->where, true) : null;
        $whereIn = $r->has('whereIn') ? json_decode($r->whereIn, true) : null;

        $query = \DB::table($table)->where($search_in_column, 'LIKE', $search_value . '%');
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
                array_push($ar, ['id' => $item->{$id_column}, 'text' => $item->{$name_column}]);
            }
            return createResponse(true, $ar);
        } else {
            return createResponse(false, '');
        }

    }
    public function search_products(Request $r)
    {

        $cat_ids = json_decode($r->category_ids,true);
        $search_term = $r->q;
      
        $query =null;
        if($cat_ids)
        $query=\DB::table('products')->whereIn('category_id',$cat_ids)->where('name', 'LIKE', '%'.$search_term . '%');
         else
        $query=\DB::table('products')->where('category_id',$cat_ids)->where('name', 'LIKE', '%'.$search_term . '%');
        

        $list = $query->get(['id','name'])->toArray();

        if (count($list) > 0) {
            $ar = [];
            foreach ($list as $item) {
                array_push($ar, ['id' => $item->id, 'text' => $item->name]);
            }
            return createResponse(true, $ar);
        } else {
            return createResponse(false, '');
        }

    }
    public function getColumnsFromTable(Request $r)
    {
        $table = $r->table;
        \Session::put('table', $table);
        $field_name = $r->field_name;
        $type = $r->type;
        $multiple = $type == 'checkbox' ? 'multiple' : '';
        $event_call = $r->has('event') ? $r->event : '';
        $cols = \Schema::getColumnListing($table);
        $resp = '';
        $resp = '<div class="form-group"><label  class="form-label">Select Fields</label>
        <select id="' . $field_name . '" name="' . $field_name . '[]" ' . $multiple . ' class="form-control"  tabindex="-1" aria-hidden="true" onchange="' . $event_call . '">';
        $options = ' <option value="" selected="" ></option>';

        foreach ($cols as $col) {
            $options .= ' <option value="' . $col . '" >' . $col . '</option>';
        }
        $resp .= $options . '</select> </div>';

        return createResponse(true, $resp);
    }
    public function getColumnsFromTableCheckbox(Request $r)
    {
        $table = $r->table;
        $field_name = $r->field_name;
        $cols = \Schema::getColumnListing($table);
        $resp = '';
        $resp = '';
        foreach ($cols as $col) {
            $resp .= '<div class="form-check form-check-inline">
        <input type="checkbox" name="' . $field_name . '[]" value="' . $col . '"
           class="form-check-input" aria-invalid="false">
        <label class="form-check-label">' . $col . '</label>
            </div>';
        }

        return createResponse(true, $resp);
    }

    public function getValidationHtml(Request $r)
    {

        $field_name = $r->field_name;

        $cols = getValidation();

        $resp = '';
        foreach ($cols as $col) {
            $resp .= '<div class="form-check form-check-inline">
                  <input  type="checkbox" name="' . $field_name . '_rules[]"  value="' . $col->value . '" class="form-check-input" aria-invalid="false">
                  <label  class="form-check-label">' . $col->label . '</label>
                  </div>';

        }

        return createResponse(true, $resp);
    }
    public function getToggableGroupHtml(Request $r)
    {

        $field_name = $r->field_name;
        $table = \Session::get('table');
        $cols = \Schema::getColumnListing($table);
        $toggable_val = '';
        $select_inputs = '<div class="form-group"><label  class="form-label">Select Inputs</label>
        <select name="' . $field_name . '_inputtype[]"  class="form-control"  tabindex="-1" aria-hidden="true" >';
        $options = ' <option value="" selected="" >Select input type</option>';
        foreach (getInputs() as $inp) {
            $options .= ' <option value="' . $inp->value . '" >' . $inp->label . '</option>';
        }

        $select_inputs .= $options . '</select> </div>';
        $select_fields = '<div class="form-group"><label  class="form-label">Select Fields</label>
        <select name="' . $field_name . '_fields[]"  class="form-control"  tabindex="-1" aria-hidden="true" >';
        $options = ' <option value="" selected="" >Select fields</option>';
        foreach ($cols as $col) {
            $options .= ' <option value="' . $col . '" >' . $col . '</option>';
        }

        $select_fields .= $options . '</select> </div>';

        $resp = '<fieldset class="form-group border p-3 fieldset">
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
           <div class="col-md-2 mb-3">' . $select_fields . '
           </div>
               <div class="col-md-2 mb-3">' . $select_inputs . '
               </div>




               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Options(Comma seprated) </label>
                       <input type="text" id="module" name="' . $field_name . '_options[]" value=""
                           class="form-control valid is-valid" placeholder="toption1,option2,option3..">

                   </div>
               </div>

               <div class="col-md-2 mb-3">
                   <p>Is Multiple?
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' . $field_name . '_multiple[]" value="Yes"
                           class="form-check-input valid is-valid" aria-invalid="false">
                       <label class="form-check-label">Yes</label>
                   </div>
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' . $field_name . '_multiple[]" value="No"
                           class="form-check-input valid is-valid" aria-invalid="false">
                       <label class="form-check-label">No</label>
                   </div>
               </div>
               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Atrr.(Options(Comma seprated)) </label>
                       <input type="text" id="module" name="' . $field_name . '_attributes[]" value=""
                           class="form-control valid is-valid" placeholder="multiple=>true,onChange=>somfunciton">

                   </div>
               </div>

           </div>
           <hr>
     </div>

   </fieldset>';

        $toggable_val = '<label for="name" class="form-label">Value</label>
           <input type="text"  name="' . $field_name . '_toggable_val[]" class="form-control valid is-valid" placeholder="Enter conditional value" />';
        return createResponse(true, ['label' => $toggable_val, 'html' => $resp]);
    }
    public function getRepeatableHtml(Request $r)
    {

        $field_name = $r->field_name;

        $label = '';
        $select = '<div class="form-group"><label  class="form-label">Select Inputs</label>
        <select name="' . $field_name . '_inputtype[]"  id="repeatable_' . $field_name . '" class="form-control"  tabindex="-1" aria-hidden="true" >';
        $options = ' <option value="" selected="" >Select input type</option>';
        foreach (getInputs() as $inp) {
            $options .= ' <option value="' . $inp->value . '" >' . $inp->label . '</option>';
        }

        $select .= $options . '</select> </div>';

        $resp = '<fieldset class="form-group border p-3 fieldset">
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
               <div class="col-md-2 mb-3">' . $select . '
               </div>
               <div class="col-md-2 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Key Name</label>
                       <input type="text" id="module" name="' . $field_name . '_keys[]" value=""
                           class="form-control valid is-valid" placeholder="Enter keyy name for json">

                   </div>
               </div>

               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Options(Comma seprated) </label>
                       <input type="text" id="module" name="' . $field_name . '_options[]" value=""
                           class="form-control valid is-valid" placeholder="toption1,option2,option3..">

                   </div>
               </div>

               <div class="col-md-2 mb-3">
                   <p>Is Multiple?
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' . $field_name . '_multiple[]" value="Yes"
                           class="form-check-input valid is-valid" aria-invalid="false">
                       <label class="form-check-label">Yes</label>
                   </div>
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' . $field_name . '_multiple[]" value="No"
                           class="form-check-input valid is-valid" aria-invalid="false">
                       <label class="form-check-label">No</label>
                   </div>
               </div>
               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Atrr.(Options(Comma seprated)) </label>
                       <input type="text" id="module" name="' . $field_name . '_attributes[]" value=""
                           class="form-control valid is-valid" placeholder="multiple=>true,onChange=>somfunciton">

                   </div>
               </div>

           </div>
           <hr>
     </div>

   </fieldset>';

        $label = '<label for="name" class="form-label">Label</label>
           <input type="text"  name="' . $field_name . '_label[]" class="form-control valid is-valid" placeholder="Enter  label" />';
        $priority = '<label for="name" class="form-label">Priority</label>
           <input type="number"  name="' . $field_name . '_priority[]" class="form-control valid is-valid" placeholder="Enter  priorit" />';
        return createResponse(true, ['label' => $label, 'html' => $resp, 'priority' => $priority]);
    }
    public function getCreateInputOptionHtml(Request $r)
    {

        $field_name = $r->field_name;
        $index = $r->cur_index;

        $label = '';
        $select = '<div class="form-group"><label  class="form-label">Select Inputs</label>
        <select id="repeatable_' . $field_name . '" name="' . $field_name . '_inputtype_create_' . $index . '[]"  class="form-control"  tabindex="-1" aria-hidden="true" >';
        $options = ' <option value="" selected="" >Select input type</option>';
        foreach (getInputs() as $inp) {
            $options .= ' <option value="' . $inp->value . '" >' . $inp->label . '</option>';
        }

        $select .= $options . '</select> </div>';

        $resp = ' <div class="row copy_row1 border-1">
               <div class="col-md-2 mb-3">' . $select . '
               </div>


               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Options(Comma seprated) </label>
                       <input type="text" name="' . $field_name . '_options_create_' . $index . '[]" value=""
                           class="form-control valid is-valid" placeholder="toption1,option2,option3..">

                   </div>
               </div>

               <div class="col-md-3 mb-3">
                   <p>Is Multiple?
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' . $field_name . '_multiple_create_' . $index . '[]" value="Yes"
                           class="form-check-input >
                       <label class="form-check-label">Yes</label>
                   </div>
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' . $field_name . '_multiple_create_' . $index . '[]" value="No"
                           class="form-check-input  aria-invalid="false">
                       <label class="form-check-label">No</label>
                   </div>
               </div>
               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Atrr.(Options(Comma seprated)) </label>
                       <input type="text" name="' . $field_name . '_attributes_create_' . $index . '[]" value=""
                           class="form-control valid is-valid" placeholder="multiple=>true,onChange=>somfunciton">

                   </div>
               </div>

           </div>';

        $label = '<label for="name" class="form-label">Label</label>
           <input type="text"  name="' . $field_name . '_label_create_' . $index . '[]" class="form-control valid is-valid" placeholder="Enter fiedset label" />';
        return createResponse(true, ['label' => $label, 'html' => $resp]);
    }
    public function getSideColumnInputOptionHtml(Request $r)
    {

        $field_name = $r->field_name;
        $index = $r->cur_index;

        $label = '';
        $select = '<div class="form-group"><label  class="form-label">Select Inputs</label>
        <select id="repeatable_' . $field_name . '" name="' . $field_name . '_inputtype_sidecolumn_' . $index . '[]"  class="form-control"  tabindex="-1" aria-hidden="true" >';
        $options = ' <option value="" selected="" >Select input type</option>';
        foreach (getInputs() as $inp) {
            $options .= ' <option value="' . $inp->value . '" >' . $inp->label . '</option>';
        }

        $select .= $options . '</select> </div>';

        $resp = ' <div class="row copy_row1 border-1">
               <div class="col-md-2 mb-3">' . $select . '
               </div>


               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Options(Comma seprated) </label>
                       <input type="text" name="' . $field_name . '_options_sidecolumn_' . $index . '[]" value=""
                           class="form-control valid is-valid" placeholder="toption1,option2,option3..">

                   </div>
               </div>

               <div class="col-md-3 mb-3">
                   <p>Is Multiple?
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' . $field_name . '_multiple_sidecolumn_' . $index . '[]" value="Yes"
                           class="form-check-input >
                       <label class="form-check-label">Yes</label>
                   </div>
                   <div class="form-check form-check-inline">
                       <input type="checkbox" name="' . $field_name . '_multiple_sidecolumn_' . $index . '[]" value="No"
                           class="form-check-input  aria-invalid="false">
                       <label class="form-check-label">No</label>
                   </div>
               </div>
               <div class="col-md-3 mb-3">

                   <div class="form-group">
                       <label for="name" class="form-label">Atrr.(Options(Comma seprated)) </label>
                       <input type="text" name="' . $field_name . '_attributes_sidecolumn_' . $index . '[]" value=""
                           class="form-control valid is-valid" placeholder="multiple=>true,onChange=>somfunciton">

                   </div>
               </div>

           </div>';

        $label = '<label for="name" class="form-label">Label</label>
           <input type="text"  name="' . $field_name . '_label_sidecolumn_' . $index . '[]" class="form-control valid is-valid" placeholder="Enter fiedset label" />';
        return createResponse(true, ['label' => $label, 'html' => $resp]);
    }

    public function table_field_update(Request $r)
{
    $table = $r->table;
    parse_str($r->form_val, $values);

    // Remove empty values
    $values = array_filter($values);

    $keys = array_keys($values);

    // Add timestamps or OTP if applicable
    if ($table === 'orders') {
        if (in_array('delivery_status', $keys)) {
            $values['status_update_date'] = now();
        }

        if (in_array('driver_id', $keys)) {
            $values['otp'] = rand(100000, 999999);
        }
    }

    $ids = json_decode($r->ids, true);
    $order_status_message = $values['order_status_message'] ?? '';
    unset($values['order_status_message']);

    // Skip update if transfer status is in request
    if (!in_array('order_transfer_status', $keys)) {
        \DB::table($table)->whereIn('id', $ids)->update($values);
    }

    // Orders-specific update logic
    if ($table === 'vendor_orders') {

       if (in_array('is_transferred', $keys) && $values['is_transferred'] === 'Yes') {
           $orders = \App\Models\VendorOrder::with(['vendor:id,name,state_id,city_id,pickup_location_name',
                            'vendor.state',
                            'vendor.city',
                            'order:id,user_id,uuid,shipping_address_id,billing_address_id,shipping_cost,total_amount,net_payable,total_discount',
                            'order.user:id,email,phone',
                            'order.billing_address',
                            'order.shipping_address',
                        ])->whereIn('id',$ids)->get();
                
            $orders->each(function ($vendorOrder) {
                $orderItems = \App\Models\OrderItem::with(['product:id,name,sale_price,sku,package_dimension', 'variant:id,sku,sale_price,name,atributes_json'])
                    ->where('order_id', $vendorOrder->order_id)
                    ->where('vendor_id', $vendorOrder->vendor_id)
                    ->get();

                $vendorOrder->setRelation('order_items', $orderItems);
            });
            foreach ($orders as $order) {
              //  dd($order->toArray());
              $mainOrder=$order->order;
                $groupedProducts = $order->order_items->groupBy(fn($item) => $item->product->vendor->id ?? null);
//dd($groupedProducts->toArray());
                foreach ($groupedProducts as $vendorId => $items) {
                    $vendor = $items->first()->product->vendor;
                    if (!$vendor || !$vendor->name) continue;

                    $orderItems = [];
                    $subtotal = 0;

                    foreach ($items as $item) {
                        $orderItems[] = [
                            "name" => $item->name,
                            "sku" => $item->variant_id ? $item->variant->sku : $item->product->sku,
                            "units" => $item->qty,
                            "selling_price" => $item->sale_price,
                        ];
                        $subtotal += $item->sale_price * $item->qty;
                    }

                    $vendor_share_ratio = $subtotal / $mainOrder->total_amount;
                    $vendor_final_amount = $mainOrder->net_payable * $vendor_share_ratio;
                    $dimensions = getFinalShipmentDimensionsAndWeight($items);

                    $pickup_location_name = $vendor->pickup_location_name ?: "{$vendor->name}_pickup_location_1";

                    $orderData = [
                        "order_id" => $order->uuid,
                        "order_date" => now()->format('Y-m-d'),
                        'pickup_location' => $pickup_location_name,
                        "billing_customer_name" => $mainOrder->billing_address->name ?? $mainOrder->user->name,
                        "billing_last_name" => "",
                        "shipping_last_name" => "",
                        "billing_address" => $mainOrder->billing_address?->address1 . ' ' . $mainOrder->billing_address?->address2,
                        "billing_city" => $mainOrder->billing_address?->city->name,
                        "billing_pincode" => $mainOrder->billing_address?->pincode,
                        "billing_state" => $mainOrder->billing_address?->state->name,
                        "billing_country" => "India",
                        "billing_email" => $mainOrder->billing_address?->email ?? $mainOrder->user->email,
                        "billing_phone" => $mainOrder->billing_address?->phone ?? $mainOrder->user->phone,
                        "shipping_is_billing" => $mainOrder->shipping_address->id == $mainOrder->billing_address->id,
                        "shipping_customer_name" => $mainOrder->shipping_address?->name ?? $mainOrder->user->name,
                        "shipping_address" => $mainOrder->shipping_address?->address1 . ' ' . $mainOrder->shipping_address?->address2,
                        "shipping_city" => $mainOrder->shipping_address?->city->name,
                        "shipping_pincode" => $mainOrder->shipping_address?->pincode,
                        "shipping_state" => $mainOrder->shipping_address?->state->name,
                        "shipping_country" => "India",
                        "shipping_email" => $mainOrder->shipping_address?->email ?? $mainOrder->user->email,
                        "shipping_phone" => $mainOrder->shipping_address?->phone ?? $mainOrder->user->phone,
                        "order_items" => $orderItems,
                        "payment_method" => $mainOrder->payment_method === 'COD' ? 'COD' : 'Prepaid',
                        "sub_total" => $vendor_final_amount,
                        "length" => $dimensions['length'],
                        "breadth" => $dimensions['breadth'],
                        "height" => $dimensions['height'],
                        "weight" => $dimensions['weight'],
                    ];
                 // dd($orderData);
                    try {
                        $shiprocketService = app(\App\Services\ShiprocketService::class);
                        $response = $shiprocketService->createOrder($orderData);
                       // dd($response);
                        if (isset($response['status_code'])) {
                            if ($response['status_code']==1) {
                                \App\Models\VendorOrder::where([
                                    'id' => $order->id,
                                   
                                ])->update([
                                    'package_dimension'=>json_encode($dimensions),
                                    'shiprocket_shipment_id' => $response['shipment_id'] ?? null,
                                   
                                    'shiprocket_order_id' => $response['order_id'],
                                ]);

                                return createResponse(true, 'Transferred successfully');
                            } else {
                                   $message = $response['message'] ?? $response['status'];
                                 if (str_contains($message, 'INVOICED')) 
                                return createResponse(false,"Already transfered");
                                else
                                return createResponse(false, $response['message'] ?? $response['status']);
                            }
                        } else {
                            
                            $message = $response['message'] ?? $response['status'];
                            if (str_contains($message, 'Wrong Pickup location')) {
                                return createResponse(false, "Please add vendor address in Shiprocket with name '{$vendor->name} Pickup Location', then transfer.");
                            }
                            return createResponse(false, $message);
                        }

                    } catch (\Exception $e) { \Sentry\captureException($e);
                        return createResponse(false, $e->getMessage());
                        \DB::table('system_errors')->insert([
                            'error' => $e->getMessage(),
                            'which_function' => 'CommonController order ship status update at line ' . $e->getLine()
                        ]);
                    }
                }
            }
        }
    }

    return createResponse(true, 'Updated successfully');
}

    public function singleFieldUpdateFromTable(Request $r)
    {
        $table = $r->table;
        $id = $r->id;
        $column = $r->field;
        $val = $r->val;

        \DB::table($table)->whereId($id)->update([$column => $val]);
        if ($table == 'fees_payments') {
            if ($column == 'payment_status') {
                $amount = \DB::table($table)->whereId($id)->first()->paid_amount;
                \DB::table('school_ledger')->insert(['name' => 'Fee Payment', 'amount' => $amount, 'mode' => 'Income', 'handle_by_id' => auth()->id()]);
            }

        }

        return createResponse(true, 'Updated successfuly');
    }
    public function bulkDelete(Request $r)
    {
        $table = $r->table;
        $ids = json_decode($r->ids, true);
        if (Schema::hasColumn($table, 'deleted_at')) {
    // Soft delete: set deleted_at timestamp
                if (count($ids) > 0) {
                        \DB::table($table)->whereIn('id', $ids)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
                    }
            } else {
                // Hard delete
               if (count($ids) > 0) {
                        \DB::table($table)->whereIn('id', $ids)->delete();
                    }
            }
      

        return createResponse(true, 'Deleted successfuly');
    }
    public function deleteRecordFromTable(Request $r)
    {
        $table = $r->table;
        $id = $r->id;
       
        if (Schema::hasColumn($table, 'deleted_at')) {
    // Soft dele
                if ($id) {
                        \DB::table($table)->where('id',$id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
                    }
                } else {
                    // Hard delete
                   if ($id) {
                   \DB::table($table)->where('id',$id)->delete();
                     }
                }

        return createResponse(true, 'Deleted successfuly');
    }
    public function deleteFileFromTable(Request $r)
    {
        $table = $r->table;
        $id = $r->id;
        $folder = $r->folder;
        $file_name = \DB::table($table)->whereId($id)->first()->name;
        if ($file_name) {
            $path = storage_path('app/public/' . $folder . '/' . $file_name);
            if (\File::exists($path)) {
                unlink($path);
            }
            $thumbnail_path = storage_path('app/public/' . $folder . '/thumbnail');
            $or_thumbnail_path = storage_path('app/public/' . $folder . '/thumbnails');
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            if (\File::exists($thumbnail_path)) {

                foreach (array_keys(getThumbnailDimensions()) as $size) {
                    {
                        $img = basename($path) . '_' . $size . '.' . $extension;
                        $file_path = 'storage/' . $folder . '/thumbnail/' . $img;

                        if (\File::exists(public_path($file_path))) {
                            \File::delete(public_path($file_path));
                        }
                    }
                }
            }
            if (\File::exists($or_thumbnail_path)) {

                foreach (array_keys(getThumbnailDimensions()) as $size) {
                    {
                        $img = basename($path) . '_' . $size . '.' . $extension;
                        $file_path = 'storage/' . $folder . '/thumbnails/' . $img;

                        if (\File::exists(public_path($file_path))) {
                            \File::delete(public_path($file_path));
                        }
                    }
                }
            }
            \DB::table($table)->whereId($id)->delete();
            return createResponse(true, 'File deleted successfully');
        } else {
            return createResponse(false, 'Error in deleteting File');
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
            $path = storage_path('app/public/' . $folder . '/' . $file_name);
            if (\File::exists($path)) {
              
                unlink($path);
            }
           
            $thumbnail_path = storage_path('app/public/' . $folder . '/thumbnail');
            $or_thumbnail_path = storage_path('app/public/' . $folder . '/thumbnails');
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            dlog('yes ex',\File::exists($thumbnail_path));
            dlog('folder',$folder);
            if (\File::exists($thumbnail_path)) {
               
           
                    chmod($thumbnail_path,0777);
                    $thumbs = getThumbnailsFromImage($file_name);
      
                    foreach ($thumbs as $p) {
                        $path = storage_path('app/public/' . $folder . '/thumbnail/' . $p);
                        if (\File::exists($path)) {
                            unlink($path);
                        }
                    }
                    
                }
          
            if (\File::exists($or_thumbnail_path)) {

                $thumbs = getThumbnailsFromImage($file_name);
      
                    foreach ($thumbs as $p) {
                        $path = storage_path('app/public/' . $folder . '/thumbnails/' . $p);
                        if (\File::exists($path)) {
                            unlink($path);
                        }
                    }
                
            }

            return createResponse(true, 'File deleted successfully');
        } else {
            return createResponse(false, 'Error in deleteting File');
        }

    }
    public function getCity(Request $r)
    {
        $state = $r->state_id;
        $t = \DB::table('city')->where('state_id',$state)->get();
        $str = '';
        foreach ($t as $r) {
            $str .= "<option value='" . $r->id . "'>" . $r->name . "</option>";
        }
        return response()->json(['success' => true, 'message' => $str]);

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
                ->where(function($query) use ($dependee_key, $value) {
                    if (is_numeric($value)) {
                        $query->where($dependee_key, '=', $value);
                    } else {
                        $query->where($dependee_key, 'LIKE', '%' . $value . '%');
                    }
                })
                ->whereNull('deleted_at')
                ->get();
        $str = '';
        $i=0;
        foreach ($t as $r) {
            $selected=$i==0?'selected':'';
             ++$i;
            $str .= "<option value='" . $r->{$table_id} . "'".$selected.">" . $r->{$dependent_key} . "</option>";
           
        }
        return response()->json(['success' => true, 'message' => $str]);

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

        $t = \DB::table($table)->whereIn($dependee_key, $value)->get();
        $str = '';
        foreach ($t as $r) {
            $str .= "<option value='" . $r->{$table_id} . "'>" . $r->{$dependent_key} . "</option>";
        }
        return response()->json(['success' => true, 'message' => $str]);

    }
    public function load_Category(Request $request)
    { /*****load category for fast select  */
        $query = $request->input('query');
        $ar = [];
        if (!empty($query)) {
            $cat_options = \App\Models\Category::where('name', 'like', '%' . $query . '%')->orWhere('slug', 'like', '%' . $query . '%')->get(['id', 'name']);

            foreach ($cat_options as $t) {
                array_push($ar, ['text' => $t->name, 'value' => $t->id]);
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
            \DB::table($set_in_table)->whereIn('id', $rowids)->update([$field_to_set => $selected_user]);
            return response()->json(['success' => true, 'message' => "Assigned successfully"]);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            return response()->json(['success' => false, 'message' => $ex->getMessage()]);

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
            $row = \DB::table('input_material AS A')->select('B.name')->join('unit AS B', 'B.id', '=', 'A.unit_id')->where('A.id', $id)->first();

            $response = $row ? $row->name : '';
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
                $t = \DB::table($table)->whereId($rowid)->first();
                if (is_null($t)) {
                    return createResponse(false, 'Please refresh the page and try again');
                }
                $existing_json_data = $t->{$json_column_name} ? json_decode($t->{$json_column_name}, true) : [];
                if (!empty($existing_json_data)) {
                    $i = 0;
                    foreach ($existing_json_data as $item) {
                        if ($item[$key] == $json_key_val) {
                            break;
                        }
                        $i++;
                    }
                    unset($existing_json_data[$i]);

                    $updated_data = json_encode(array_values($existing_json_data));
                    \DB::table($table)->whereId($rowid)->update([$json_column_name => $updated_data]);
                }
                \DB::commit();
                return createResponse(true, 'Deleted  successfullly');
            } catch (\Exception $ex) { \Sentry\captureException($ex);
                \DB::rollback();
                return createResponse(false, $ex->getMessage());

            }
        } else {
            return createResponse(false, 'Invalid Request');
        }

    }
}
