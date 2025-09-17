<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;
use DB;
class DelhiveryService
{
    protected string $base;
    protected string $token;

    public function __construct()
    {
        $this->token = config('services.delhivery.token');
        $this->base  = config('services.delhivery.base_url');
    }

   protected function request(string $method, string $uri, array $payload = [])
{
   
    return Http::withHeaders([
            'Authorization' => 'Token ' . $this->token,
            'Accept'        => 'application/json',
        ])
        ->asForm()
        ->post("{$this->base}{$uri}",$payload)
        ->throw()
        ->json();
}

    /**
     * 1. Create Warehouse (Pickup Location)
     */
    public function createOrUpdateWarehouseAddress($vendor): array
    {
        try {
            $already_exists=$vendor->delhivery_pickup_name?true:false;
            $action=$already_exists?'edit':'create';
            $pickup_name =$vendor->delhivery_pickup_name??strtolower(str_replace(' ','_',$vendor->name)) . '_pickup_location';
           
            $payload = [
                'name'    => $pickup_name,
                'email'   => $vendor->email,
                'phone'   => $vendor->phone,
                'address' => $vendor->address . ' ' . $vendor->address2,
                'return_address' => $vendor->address . ' ' . $vendor->address2,
                'registered_name' =>$pickup_name,
                'city' => $vendor->city?->name,
                'return_city' => $vendor->city?->name,
                'return_state' => $vendor->state?->name,
                'pin'  => $vendor->pincode,
                'return_pin'  => $vendor->pincode,
                'return_country'  => 'India',

            ];
       // dd($payload);
            $response = $this->request('post', "/api/backend/clientwarehouse/{$action}/", $payload);
               \DB::table('vendors')->where('id',$vendor->id)->update([
                    'delhivery_pickup_name'=>$pickup_name,
                ]);
            return [
                'success' => true,
                'message' => 'Warehouse created successfully.',
                'data'    => $response,
            ];
        } catch (Throwable $e) {
           /// dd($e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    
   
    /**
     * 2. Order Creation / Manifest (Waybill Auto-Generated)
     */
public function createForwardShipments($vendorOrders)
{
    try {
        DB::transaction(function() use ($vendorOrders) {
            foreach ($vendorOrders as $order) {
               
                  if($order->is_transferred=='No'){
                  
                $mainOrder = $order->order;
                $vendor = $order->vendor;
                 $customer=$mainOrder->user;
                   $customer_ship_address = $mainOrder->shipping_address;
                  // dd($vendor->delhivery_pickup_name);

                if (!$vendor) {
                    throw new \Exception("Some seller not found for vendor order {$order->id}");
                }
                if (!$vendor->delhivery_pickup_name) {
                    throw new \Exception("Seller {$vendor->name} pickup location not registered with Delhivery.");
                }
                if (!$customer_ship_address->city || !$customer_ship_address->state || !$customer_ship_address->pincode) {
                    throw new \Exception("Customer {$customer->name} address is not complete, missing some information .");
                  }
                    if (empty($customer_ship_address->address1)) {
                                        throw new \Exception("Customer {$customer->name} shipping address is  missing  .");
                            }
                // Prepare shipment details
                $items = $order->order_items;
                $subtotal = 0;
                $orderItemsDesc = [];

                foreach ($items as $item) {
                    $orderItemsDesc[] = "{$item->product->name} x{$item->qty}";
                    $subtotal += $item->sale_price * $item->qty;
                }

                $vendor_share_ratio  = $subtotal / $mainOrder->subtotal;
                $vendor_final_amount = $mainOrder->net_payable * $vendor_share_ratio;
                $dimensions = getFinalShipmentDimensionsAndWeight($items);
             
                $shipmentPayload = [
                    "pickup_location" => [
                        "name" => $vendor->delhivery_pickup_name,
                    ],
                    "shipments" => [
                        [
                          
                            "order"          => $order->uuid,
                            "add"            => $customer_ship_address?->address1 . " " . $customer_ship_address?->address2,
                            "phone"          => $customer_ship_address->phone_number??$customer->phone,
                            'city' =>  $customer_ship_address->city?->name,
                            'state' => $customer_ship_address->state?->name,
                            'pin' => $customer_ship_address->pincode,
                            'country' => 'India',
                            "name"           => $customer->name ?? $customer->email,
                            "payment_mode"   => $mainOrder->payment_method === 'COD' ? 'COD' : 'Prepaid',
                            "cod_amount"     => $mainOrder->payment_method === 'COD' ? $vendor_final_amount : 0,
                            "products_desc"  => implode(', ', $orderItemsDesc),
                            "quantity"       => $items->sum('qty'),
                            "weight"         => $dimensions['weight'] ?? 0.5,
                            "shipment_width" => $dimensions['breadth'] ?? null,
                            "shipment_height"=> $dimensions['height'] ?? null,
                            "shipment_length"=> $dimensions['length'] ?? null,
                            "seller_name"    => $vendor->name,
                            // "seller_inv"     => $order->invoice_no ?? ('INV-' . $order->id),
                        ]
                    ]
                ];

                // Call Delhivery API
                $response = $this->request('post', '/api/cmu/create.json', [
                    'format' => 'json',
                    'data'   => json_encode($shipmentPayload),
                ]);
                $package = $response['packages'][0] ?? null;
                if (!$package) {
                    throw new \Exception("No package response received for vendor order {$order->uuid}");
                }

                // Check if Delhivery marked it as not serviceable
                if ($package['serviceable'] === false) {
                    $remarks = implode(", ", $package['remarks'] ?? []);
                    throw new \Exception("Error:{$remarks}");
                }

                // Check for missing waybill (generic failure)
                if (empty($package['waybill'])) {
                    throw new \Exception("Failed to generate AWB for vendor order {$order->uuid}");
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
                    $order->shiprocket_shipment_id =$response["shipment_id"]?? null;
                    $order->shiprocket_order_id = $response["order_id"]?? null;
                    $order->is_transferred = 'Yes';
                    //awb generated while order is transfered  in delhivery
                    $order->awb = $response['packages'][0]['waybill'];
                    $order->delivery_status_updates = json_encode($vendor_order_status_updates);
                    $order->save();
                    foreach ($order->order_items as $item) {
                      

                        $updates = json_decode($item->delivery_status_updates ?? "[]", true);
                        $updates[] = $newStatus;

                        $item->delivery_status = $newStatus["status"];
                        $item->delivery_status_updates = json_encode($updates);
                        $item->save();
                    }
            }
        }
        }); // Transaction ends here

    } catch (\Throwable $e) {
        \Sentry\captureException($e);
           \DB::table("system_errors")->insert([
                "error" => $e->getMessage(),
                "which_function" =>
                    "Delhivery  Servicee createShipments vendor function " .
                    $e->getLine(),
            ]);
        // Rethrow to propagate error
        throw $e;
    }
}
public function transferReturnExhShipments($returnShipment)
{
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
        $only_order_items = $return->return_items->map(function ($item) {
            return $item->order_item;
        });
          if (!$vendor) {
                    throw new \Exception("Some seller not found for vendor order {$order->id}");
                }
        if (!$vendor->delhivery_pickup_name) {
                    throw new \Exception("Seller {$vendor->name} pickup location not registered with Delhivery.");
                }
        if (!$customer_ship_address->city || !$customer_ship_address->state || !$customer_ship_address->pincode) {
                    throw new \Exception("Customer {$customer->name} shipping address is not complete, missing some information .");
         }
        if (empty($customer_ship_address->address1)) {
                    throw new \Exception("Customer {$customer->name} shipping address is  missing  .");
         }
       
    try {
        DB::transaction(function() use ($return,$vendor,$only_order_items,$vendor_order,$order) {
           $mainOrder = $order;
               

              
                $items =$only_order_items;
                $subtotal = 0;
                $orderItemsDesc = [];

                foreach ($items as $item) {
                    $orderItemsDesc[] = "{$item->product->name} x{$item->qty}";
                    $subtotal += $item->sale_price * $item->qty;
                }
                $total_amount =$subtotal;
                $dimensions = getFinalShipmentDimensionsAndWeight($items);

                $shipmentPayload = [
                    "pickup_location" => [
                        "name" => $vendor->delhivery_pickup_name,
                    ],
                    "shipments" => [
                       [
                           
                            "order"          => $return->uuid,
                            "add"            => $customer_ship_address?->address1 . " " . $customer_ship_address?->address2,
                            "phone"          => $customer_ship_address->phone_number??$customer->phone,
                            'city' =>  $customer_ship_address->city?->name,
                            'state' => $customer_ship_address->state?->name,
                            'pin' => $customer_ship_address->pincode,
                            'country' => 'India',
                            "name"           => $customer->name ?? $customer->email,
                            "payment_mode"   =>$return->type === 'Exchange' ? 'REPL' : 'Pickup',
                            "total_amount"     =>$total_amount,
                            "products_desc"  => implode(', ', $orderItemsDesc),
                            "quantity"       => $items->sum('qty'),
                            "weight"         => $dimensions['weight'] ?? 0.5,
                            "shipment_width" => $dimensions['breadth'] ?? null,
                            "shipment_height"=> $dimensions['height'] ?? null,
                            "shipment_length"=> $dimensions['lenght'] ?? null,
                          
                            // "seller_inv"     => $order->invoice_no ?? ('INV-' . $order->id),
                        ]
                    ]
                ];

                // Call Delhivery API
                $response = $this->request('post', '/api/cmu/create.json', [
                    'format' => 'json',
                    'data'   => $shipmentPayload,
                ]);
                return array_merge($response,['success'=>true]);

                
             
                   

            
        }); // Transaction ends here

    } catch (\Throwable $e) {
        \Sentry\captureException($e);
           \DB::table("system_errors")->insert([
                "error" => $e->getMessage(),
                "which_function" =>
                    "Delhivery  Servicee createShipments vendor function " .
                    $e->getLine(),
            ]);
        // Rethrow to propagate error
        throw $e;
    }
}


    /**
     * 3. Bulk Waybill Generation
     */
    public function generateBulkWaybill(int $count, string $clientName): array
    {
        try {
            $response = $this->request('get', "/waybill/api/bulk/json/", [
                'cl'    => $clientName,
                'count' => $count,
            ]);

            return [
                'success' => true,
                'message' => 'Waybills generated successfully.',
                'data'    => $response,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    /**
     * 4. Shipping Label / Packing Slip
     */
    public function packingSlip(array $params): array
    {
        try {
            $response = $this->request('get', '/api/p/packing_slip', $params);

            return [
                'success' => true,
                'message' => 'Packing slip generated successfully.',
                'data'    => $response,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    /**
     * 5. Pickup Request Creation
     */
    public function createPickupRequest(array $payload): array
    {
        try {
            $response = $this->request('post', '/fm/request/new/', $payload);

            return [
                'success' => true,
                'message' => 'Pickup request created successfully.',
                'data'    => $response,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => null,
            ];
        }
    }
    public function checkPincode(string $pincode): array
{
    try {
        $payload = [
            'pincode' => $pincode,
            'format'  => 'json'
        ];

        $response = $this->request('post', '/api/pincode/check/json/', $payload);

        // Response structure can vary; commonly returns 'serviceable' => true/false
        $isServiceable = $response['serviceable'] ?? false;
        $codAvailable  = $response['cod'] ?? false;
        $prepaidAvailable = $response['prepaid'] ?? false;

        return [
            'success' => true,
            'message' => $isServiceable ? 'Pincode is serviceable' : 'Pincode is not serviceable',
            'data' => [
                'serviceable' => $isServiceable,
                'cod'         => $codAvailable,
                'prepaid'     => $prepaidAvailable,
            ],
        ];
    } catch (\Throwable $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'data'    => null,
        ];
    }
}
}
