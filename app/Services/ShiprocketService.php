<?php 
namespace App\Services;

use Illuminate\Support\Facades\Http;
use DB;
class ShiprocketService
{
    protected $token;

    public function __construct()
    {
        
         $response = Http::post('https://apiv2.shiprocket.in/v1/external/auth/login', [
                'email' => config('services.shiprocket.email'),
                'password' => config('services.shiprocket.password'),
            ]);
           $this->token= $response['token'] ?? null;
    }

    protected function headers()
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }

    public function createOrder(array $orderData)
    {
      
        $response = Http::withHeaders($this->headers())
            ->post('https://apiv2.shiprocket.in/v1/external/orders/create/adhoc', $orderData);

        return $response->json();
    }
  public function createForwardShipments($vendorOrders)
{
    try {
        DB::transaction(function() use ($vendorOrders) {
            foreach ($vendorOrders as $order) {
                if($order->is_transferred=='No'){
                 
                
                $mainOrder = $order->order;
                $customer_ship_address=$mainOrder->shipping_address;
                 if (!$customer_ship_address->city || !$customer_ship_address->state || !$customer_ship_address->pincode) {
                    throw new \Exception("Customer {$customer->name} shipping address is not complete, missing some information .");
                    }
                    if (empty($customer_ship_address->address1)) {
                                throw new \Exception("Customer {$customer->name} shipping address is  missing  .");
                    }

                $groupedProducts = $order->order_items->groupBy(
                    fn($item) => $item->product->vendor->id ?? null
                );

                foreach ($groupedProducts as $vendorId => $items) {
                    $vendor = $items->first()->product->vendor;

                    if (!$vendor) {
                        throw new \Exception("Some seller not found for vendor order {$order->id}");
                    }

                    if (!$vendor->pickup_location_name) {
                        throw new \Exception("Seller {$vendor->name} pickup location not registered with Shiprocket.");
                    }

                    $orderItems = [];
                    $subtotal = 0;

                    foreach ($items as $item) {
                        $orderItems[] = [
                            "name" => $item->name,
                            "sku" => $item->variant_id
                                ? $item->variant->sku
                                : $item->product->sku,
                            "units" => $item->qty,
                            "selling_price" => $item->sale_price,
                        ];
                        $subtotal += $item->sale_price * $item->qty;
                    }

                    $vendor_share_ratio = $subtotal / $mainOrder->subtotal;
                    $vendor_final_amount = $mainOrder->net_payable * $vendor_share_ratio;
                    $dimensions = getFinalShipmentDimensionsAndWeight($items);
                    $pickup_location_name = $vendor->pickup_location_name;

                    $orderData = [
                        "order_id" => $order->uuid,
                        "order_date" => now()->format("Y-m-d"),
                        "pickup_location" => $pickup_location_name,
                        "billing_customer_name" => $mainOrder->billing_address->name ?? $mainOrder->user->name,
                        "billing_last_name" => "",
                        "shipping_last_name" => "",
                        "billing_address" => $mainOrder->billing_address?->address1 . " " . $mainOrder->billing_address?->address2,
                        "billing_city" => $mainOrder->billing_address?->city->name,
                        "billing_pincode" => $mainOrder->billing_address?->pincode,
                        "billing_state" => $mainOrder->billing_address?->state->name,
                        "billing_country" => "India",
                        "billing_email" => $mainOrder->billing_address?->email ?? $mainOrder->user->email,
                        "billing_phone" => $mainOrder->billing_address?->phone_number ?? $mainOrder->user->phone,
                        "shipping_is_billing" => $mainOrder->shipping_address->id == $mainOrder->billing_address->id,
                        "shipping_customer_name" => $mainOrder->shipping_address?->name ?? $mainOrder->user->name,
                        "shipping_address" => $mainOrder->shipping_address?->address1 . " " . $mainOrder->shipping_address?->address2,
                        "shipping_city" => $mainOrder->shipping_address?->city->name,
                        "shipping_pincode" => $mainOrder->shipping_address?->pincode,
                        "shipping_state" => $mainOrder->shipping_address?->state->name,
                        "shipping_country" => "India",
                        "shipping_email" => $mainOrder->shipping_address?->email ?? $mainOrder->user->email,
                        "shipping_phone" => $mainOrder->shipping_address?->phone_number ?? $mainOrder->user->phone,
                        "order_items" => $orderItems,
                        "payment_method" => $mainOrder->payment_method === "COD" ? "COD" : "Prepaid",
                        "sub_total" => $vendor_final_amount,
                        "length" => $dimensions["length"],
                        "breadth" => $dimensions["breadth"],
                        "height" => $dimensions["height"],
                        "weight" => $dimensions["weight"],
                    ];

                    // Call Shiprocket API
                    $response = $this->createOrder($orderData);

                    if (!isset($response["status_code"]) || $response["status_code"] != 1) {
                        $message = $response["message"] ?? $response["status"] ?? 'Unknown error';
                        if (str_contains($message, "INVOICED")) {
                            throw new \Exception("Already transferred");
                        }
                        if (str_contains($message, "Wrong Pickup location")) {
                            throw new \Exception("Please add vendor address in Shiprocket with name '{$vendor->name} Pickup Location', then transfer.");
                        }
                        throw new \Exception($message);
                    }
                    if(empty($response["shipment_id"])){
                          throw new \Exception('Shipment ID missing in response for vendor order '.$order->uuid);

                    }
                 
                    $newStatus = [
                            "icon" => "Approved",
                            "status" => "APPROVED",
                            "date" => now(),
                            "message" => "",
                    ];
                    $vendor_order_status_updates=json_decode($order->delivery_status_updates ?? "[]", true);
                    $vendor_order_status_updates[] = $newStatus;
                    $order->delivery_status = $newStatus["status"];
                    //Awb in case of shiprocket is generated after mainfest generation means when 
                    // admin assign courier but in delhivery assigned instanlty 
                    $order->shiprocket_shipment_id =$response["shipment_id"]?? null;
                    $order->shiprocket_order_id = $response["order_id"]?? null;
                    $order->is_transferred = 'Yes';
                    $order->delivery_status_updates = json_encode($vendor_order_status_updates);
                    $order->save();
                    foreach ($order->order_items as $item) {
                      

                        $updates = json_decode($item->delivery_status_updates ?? "[]", true);
                        $updates[] = $newStatus;

                        $item->delivery_status = $newStatus["status"];
                        $item->delivery_status_updates = json_encode($updates);
                        $item->save();
                    }
                    // Update DB after successful shipment creation
                   
                   
                }
            }
            }
        }); // End of transaction
    } catch (\Throwable $e) {
         \Sentry\captureException($e);
           \DB::table("system_errors")->insert([
                "error" => $e->getMessage(),
                "which_function" =>
                    "Shiprocket Servicee createShipments vendor function " .
                    $e->getLine(),
            ]);
        throw $e; // rollback happens automatically
    }
}
  public function transferReturnExhShipments($returnShipment){
        $return=$returnShipment;
        $returnOrder = [];
         $order_items = [];
        $order = $return->vendor_order->order;
        $patment_method =
            $order->payment_method == "COD" ? "COD" : "Prepaid";
        $vendor = $return->vendor;
        // dd($vendor->toArray());
        $vendor_order = $return->vendor_order;
        $customer = $order->user;

        $customer_ship_address = $order->shipping_address;
         if (!$customer_ship_address->city || !$customer_ship_address->state || !$customer_ship_address->pincode) {
                    throw new \Exception("Customer {$customer->name} shipping address is not complete, missing some information .");
         }
        if (empty($customer_ship_address->address1)) {
                    throw new \Exception("Customer {$customer->name} shipping address is  missing  .");
         }
        $only_order_items = $return->return_items->map(function ($item) {
            return $item->order_item;
        });
        $final_package_dimesnion = getFinalShipmentDimensionsAndWeight(
            $only_order_items
        );

        if ($return->type == "Exchange") {
            $returnOrder = [
                "order_items" => $return->return_items
                    ->map(function ($item) {
                        $product = optional($item->order_item->product);
                        $variant = optional($item->order_item->variant);
                        $exchange_variant = optional(
                            $item->exchange_variant
                        );
                        $variant_attributes = $variant
                            ? json_decode($variant->atributes_json, true)
                            : null;
                        $color =
                            $variant_attributes &&
                            isset($variant_attributes["Color"])
                                ? $variant_attributes["Color"]
                                : "";
                        $size =
                            $variant_attributes &&
                            isset($variant_attributes["Size"])
                                ? $variant_attributes["Size"]
                                : "";

                        return [
                            "name" => $product->name ?? "Unknown Product",
                            "selling_price" => $variant
                                ? $variant->sale_price
                                : $product->sale_price,
                            "units" => $item->quantity ?? 1,
                            "hsn" => "",
                            "sku" => $variant
                                ? $variant->sku
                                : $product->sku,
                            "tax" => "",
                            "discount" => $item->discount_share ?? 0.0,
                            "brand" => $vendor->name ?? "",
                            "color" => $color,
                            "exchange_item_id" =>
                                (string) $exchange_variant->id,
                            "exchange_item_name" =>
                                $exchange_variant->product->name ?? "",
                            "exchange_item_sku" =>
                                $exchange_variant->sku ?? "",
                            "qc_enable" => false,
                            "qc_product_name" => $product->name ?? "",
                            "qc_product_image" => $variant
                                ? asset(
                                    "storage/products/" .
                                        $product->id .
                                        "/variants/" .
                                        $variant->image
                                )
                                : asset(
                                    "storage/products/" .
                                        $product->id .
                                        "/" .
                                        $product->image
                                ),
                            "qc_brand" => $vendor->name ?? "",
                            "qc_color" => $color ?? "",
                            "qc_size" => $size ?? "",
                            "accessories" => "",
                            "qc_used_check" => "1",
                            "qc_sealtag_check" => "1",
                            "qc_brand_box" => "1",
                            "qc_check_damaged_product" => "yes",
                        ];
                    })
                    ->toArray(),

                // Buyer Pickup Info
                "buyer_pickup_first_name" => $customer_ship_address->name
                    ? $customer->name
                    : "", // replace with actual
                "buyer_pickup_last_name" => "",
                "buyer_pickup_email" => $customer->email,
                "buyer_pickup_address" => $customer_ship_address->address1,
                "buyer_pickup_address_2" =>
                    $customer_ship_address->address2,
                "buyer_pickup_city" => $customer_ship_address->city->name,
                "buyer_pickup_state" => $customer_ship_address->state->name,
                "buyer_pickup_country" => "India",
                "buyer_pickup_phone" =>$customer_ship_address->phone_number?? $customer->phone,
                "buyer_pickup_pincode" => $customer_ship_address->pincode,

                // Buyer Shipping Info (same as pickup)
                "buyer_shipping_first_name" => $customer_ship_address->name
                    ? $customer->name
                    : "", // replace with actual
                "buyer_shipping_last_name" => "",
                "buyer_shipping_email" => $customer->email,
                "buyer_shipping_address" =>
                    $customer_ship_address->address1,
                "buyer_shipping_address_2" =>
                    $customer_ship_address->address2,
                "buyer_shipping_city" => $customer_ship_address->city->name,
                "buyer_shipping_state" =>
                    $customer_ship_address->state->name,
                "buyer_shipping_country" => "India",
                "buyer_shipping_phone" => $customer_ship_address->phone_number?? $customer->phone,
                "buyer_shipping_pincode" => $customer_ship_address->pincode,

                // Seller warehouse location
                "seller_pickup_location_id" => $vendor->pickup_location_id,
                "seller_shipping_location_id" =>
                    $vendor->pickup_location_id,

                // Order meta
                "exchange_order_id" => $return->uuid,
                "return_order_id" => $return->uuid,
                "payment_method" => $patment_method,
                "order_date" => now()->toDateString(),

                "existing_order_id" => $return->vendor_order->uuid,

                // Charges
                "sub_total" => $return->return_items->sum(
                    fn($item) => ($item->order_item->variant
                        ? $item->order_item->variant->sale_price
                        : $item->order_item->product->sale_price) -
                        $item->order_item->discount_share
                ),
                "shipping_charges" => "",
                "giftwrap_charges" => "",
                "total_discount" => $return->return_items->sum(
                    fn($item) => $item->order_item->discount_share
                ),

                "transaction_charges" => "",

                "return_reason" => "Size or color issue",
                "return_length" =>
                    $final_package_dimesnion["length"] >= 1
                        ? $final_package_dimesnion["length"]
                        : 1,
                "return_breadth" =>
                    $final_package_dimesnion["breadth"] >= 1
                        ? $final_package_dimesnion["breadth"]
                        : 1,
                "return_height" =>
                    $final_package_dimesnion["height"] >= 1
                        ? $final_package_dimesnion["height"]
                        : 1,
                "return_weight" =>
                    $final_package_dimesnion["weight"] >= 1
                        ? $final_package_dimesnion["weight"]
                        : 1,

                "exchange_length" =>
                    $final_package_dimesnion["length"] >= 1
                        ? $final_package_dimesnion["length"]
                        : 1,
                "exchange_breadth" =>
                    $final_package_dimesnion["breadth"] >= 1
                        ? $final_package_dimesnion["breadth"]
                        : 1,
                "exchange_height" =>
                    $final_package_dimesnion["height"] >= 1
                        ? $final_package_dimesnion["height"]
                        : 1,
                "exchange_weight" =>
                    $final_package_dimesnion["weight"] >= 1
                        ? $final_package_dimesnion["weight"]
                        : 1,
            ];
            //dd($returnOrder);
           return $this->createExchOrder($returnOrder);
            
           
        } else {
            // dd($customer->toArray());
            $returnOrder = [
                "order_items" => $return->return_items
                    ->map(function ($item) {
                        $product = optional($item->order_item->product);
                        $variant = optional($item->order_item->variant);
                        // $exchange_variant = optional($item->exchange_variant);
                        $variant_attributes = $variant
                            ? json_decode($variant->atributes_json, true)
                            : null;
                        $color =
                            $variant_attributes &&
                            isset($variant_attributes["Color"])
                                ? $variant_attributes["Color"]
                                : "";
                        $size =
                            $variant_attributes &&
                            isset($variant_attributes["Size"])
                                ? $variant_attributes["Size"]
                                : "";
                        $itemImageForQCCheuque = str_replace(
                            ".webp",
                            ".jpg",
                            $variant ? $variant->image : $product->image
                        );
                        return [
                            "name" => $product->name ?? "Unknown Product",
                            "selling_price" => $variant
                                ? $variant->sale_price
                                : $product->sale_price,
                            "units" => $item->quantity ?? 1,
                            "hsn" => "",
                            "sku" => $variant
                                ? $variant->sku
                                : $product->sku,
                            "tax" => "",
                            "discount" => $item->discount_share ?? 0.0,
                            "brand" => $vendor->name ?? "",
                            "color" => $color,
                            // 'exchange_item_id' => (string) $exchange_variant->id,
                            // 'exchange_item_name' => $exchange_variant->product->name ?? '',
                            // 'exchange_item_sku' => $exchange_variant->sku ?? '',
                            "qc_enable" => false,
                            "qc_product_name" => $product->name ?? "",
                            "qc_product_image" => $variant
                                ? asset(
                                    "storage/products/" .
                                        $product->id .
                                        "/variants/" .
                                        $variant->image
                                )
                                : asset(
                                    "storage/products/" .
                                        $product->id .
                                        "/" .
                                        $product->image
                                ),
                            "qc_brand" => $vendor->name ?? "",
                            "qc_color" => $color ?? "",
                            "qc_size" => $size ?? "",
                            "accessories" => "",
                            "qc_used_check" => "1",
                            "qc_sealtag_check" => "1",

                            "qc_check_damaged_product" => "yes",
                        ];
                    })
                    ->toArray(),

                // Buyer Pickup Info
                "order_id" => $return->uuid,
                "pickup_customer_name" => $customer->name ?? "v", // replace with actual
                "pickup_last_name" => "",
                "company_name" => $vendor->name,
                "pickup_email" => $customer->email,
                "pickup_address" => $customer_ship_address->address1,
                "pickup_address_2" => $customer_ship_address->address2,
                "pickup_city" => $customer_ship_address->city->name,
                "pickup_state" => $customer_ship_address->state->name,
                "pickup_country" => "India",

                "order_date" => date("Y-m-d"),
                "pickup_phone" =>  $customer_ship_address->phone_number??ltrim($customer->phone, "0"),
                "pickup_pincode" => $customer_ship_address->pincode,

                // Buyer Shipping Info (same as pickup)
                "shipping_customer_name" => $vendor->name,
                "shipping_last_name" => "",
                "shipping_email" => $vendor->email ?? "",
                "shipping_address" => $vendor->address,
                "shipping_address_2" => $vendor->address2,
                "shipping_city" => optional($vendor->city)->name ?? "",
                "shipping_state" => optional($vendor->state)->name ?? "",
                "shipping_country" => "India",
                "shipping_phone" => ltrim($vendor->phone, "0") ?? "",
                "shipping_pincode" => $vendor->pincode ?? "",
                "shipping_isd_code" => "91",
                "payment_method" => $patment_method,

                "sub_total" => $return->return_items->sum(
                    fn($item) => ($item->order_item->variant
                        ? $item->order_item->variant->sale_price
                        : $item->order_item->product->sale_price) -
                        $item->order_item->discount_share
                ),
                "total_discount" => $return->return_items->sum(
                    fn($item) => $item->order_item->discount_share
                ),

                "length" =>
                    $final_package_dimesnion["length"] >= 0.5
                        ? $final_package_dimesnion["length"]
                        : 0.5,
                "breadth" =>
                    $final_package_dimesnion["breadth"] >= 0.5
                        ? $final_package_dimesnion["breadth"]
                        : 0.5,
                "height" =>
                    $final_package_dimesnion["height"] >= 0.5
                        ? $final_package_dimesnion["height"]
                        : 0.5,
                "weight" =>
                    $final_package_dimesnion["weight"] >= 0.5
                        ? $final_package_dimesnion["weight"]
                        : 0.5,
            ];
            // dd($returnOrder);
           return  $this->createReturnOrder($returnOrder);
           

           
        }
        
  }
    public function createReturnOrder(array $orderData)
    {
      
        $response = Http::withHeaders($this->headers())
            ->post('https://apiv2.shiprocket.in/v1/external/orders/create/return', $orderData);
        $resp=$response->json();
        //  dd($resp); 
       if($resp['status_code']>=400){
   
                        $firstError = collect($resp['errors'])             // Convert the errors array to a collection
                    ->flatten()                            // Flatten all nested arrays into one
                    ->first(); 
               // dd($firstError);       
             \DB::table('shiprocket_errors')->insert([
                     'errors'=>json_encode($resp),
                     'line_no'=>42,
                     'function'=>'createReturnOrder in ShiprocketService',
                       'order_data'=>json_encode($orderData)
             ]);
           return ['message'=>$firstError,'success'=>false]; 
        }
        else{
          
         return ['message'=>'Return Order create on shiprocket successfully','success'=>true];
        }
    }
    public function createExchOrder(array $orderData)
    {
      
        $response = Http::withHeaders($this->headers())
            ->post('https://apiv2.shiprocket.in/v1/external/orders/create/exchange', $orderData);

        $resp=$response->json();
        if($resp['status_code']>=400){
             \DB::table('shiprocket_errors')->insert([
                     'errors'=>json_encode($resp),
                     'line_no'=>61,
                     'function'=>'createExchOrder in ShiprocketService',
                     'order_data'=>json_encode($orderData)
             ]);
              
           return ['message'=>'Failed to ship the exchange order,contact admin for issue ','success'=>false]; 
        }
        else
         return ['message'=>'Exchange Order Shiped','success'=>true];
    }
    public function addPickupLocationOfVendor($vendor)
    {
        $old_pickup=$vendor->pickup_location_name;
        $new_pickup='';
        if($old_pickup){
           
            $last_number=explode('__',$old_pickup);

            $incrmented=isset($last_number[1])?intVal($last_number[1])+1:1;
           // dd($incrmented);
            $new_pickup=$vendor->name.'_pickup_location__'.$incrmented; 
        }
        else{
           $new_pickup=$vendor->name.'_pickup_location__1'; 
        }
     
          $response = Http::withHeaders($this->headers())
             ->post('https://apiv2.shiprocket.in/v1/external/settings/company/addpickup', [
                    'pickup_location' =>$new_pickup,
                    'name' => $vendor->name,
                    'email' => $vendor->email,
                    'phone' => $vendor->phone,
                    'address' => $vendor->address,
                    'address_2' => $vendor->address2,
                    'city' =>  $vendor->city?->name,
                    'state' =>  $vendor->state?->name,
                    'country' => 'India',
                    'pin_code' =>  $vendor->pincode,
                    'address_type'=>'vendor',
                    'vendor_name'=>$vendor->name
    ]);

            $data=$response->json();
     
            if(isset($data['address'])){
                \DB::table('vendors')->where('id',$vendor->id)->update([
                    'pickup_location_name'=>$new_pickup,'pickup_location_id'=>$data['address']['id']
                ]);
             return  ['success'=>true,'message'=>'ok'];
            }
          
       else{
        // Decode the JSON message
        $decodedMessage = json_decode($response['message'], true);
        $values=array_values($decodedMessage);
        // Access the first error message
        $errorMessage = $values[0] ?? 'Unknown error';
             \DB::table('shiprocket_errors')->insert([
                     'errors'=>json_encode($data),
                     'line_no'=>68,
                     'function'=>'addPickuplocaotio in ShiprocketService',
                     'vendor_id'=>$vendor->id
             ]);
           return  ['success'=>false,'message'=>$errorMessage];
        }
      
      
    }

    public function getOrderTracking($shipmentId)
    {
        $response = Http::withHeaders($this->headers())
            ->get("https://apiv2.shiprocket.in/v1/external/courier/track/shipment/{$shipmentId}");

        return $response->json();
    }
    public function generateDocument($type,$id)
{
   
        $endpoints = [
            'label'    => 'https://apiv2.shiprocket.in/v1/external/courier/generate/label',
            'manifest' => 'https://apiv2.shiprocket.in/v1/external/manifests/generate',
            'invoice'  => 'https://apiv2.shiprocket.in/v1/external/orders/print/invoice',
        ];
        if($type!='invoice'){
        
            $response =Http::withHeaders($this->headers())->post($endpoints[$type], [
                'shipment_id' => [$id],
            ]);
            //dd($response->json());
                return $response->json();
        }
        else{
            
                $response =Http::withHeaders($this->headers())->post($endpoints[$type], [
                    'ids' => [$id],
                ]);
            return $response->json();
        }
   
}
}
