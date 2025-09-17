<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ShiprocketWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        $count = count($payload);
 
        if ($count > 0) {
            Log::info('Shiprocket Webhook Received:', $payload);

             DB::table('webhook_response')->insert([
                            'message' => json_encode($payload),
                            'order_id' => $request->input('order_id') ?? null
                        ]);
            if ($request->has('awb')) {
                DB::beginTransaction();

                try {
                    $awbCode     = $request->input('awb');
                    $orderId     = $request->input('order_id');
                    $status      = $request->input('current_status');
                    $shipStatus  = $request->input('shipment_status');
                    $courierName = $request->input('courier_name');
                    $etd         = $request->input('etd');
                    $scans       = $request->input('scans');

                    // Build status updates array
                    $statusUpdates = collect($scans)
                        ->filter(function ($scan) {
                            return strtoupper($scan['sr-status-label']) !== 'NA';
                        })
                        ->map(function ($scan) {

                            $st = $scan['sr-status-label'];
                            return [
                                'date'   => $scan['date'],
                                'status' => $st,
                                'icon'   => $st,
                            ];
                        })
                        ->toArray();

                    if ($orderId) {
                        if (str_contains($status, 'RETURN') || str_contains($status, 'EXCHANGE')) {
                            $returnOrder = DB::table('return_shipments')->where('uuid', $orderId)->first();
                           
                            $vendorOrder = DB::table('vendor_orders')
                                ->where('id', $returnOrder->vendor_order_id)
                                ->first();
                               
                            $mainOrder = DB::table('orders')
                                ->where('id', $vendorOrder->order_id)
                                ->first();
                             
                            if ($returnOrder && $vendorOrder) {
                            
                                  
                                $itemReturUpdates =$returnOrder->return_status_updates? json_decode($returnOrder->return_status_updates, true) : [];
                              
                              
                                $itemReturUpdates = array_merge($itemReturUpdates, $statusUpdates);
                               
                              
                                DB::table('return_items')
                                    ->where([
                                        'return_shipment_id' => $returnOrder->id,
                                       
                                    ])
                                    ->update(['return_status' => $shipStatus,
                                     'return_status_updates'=>json_encode($itemReturUpdates)]);
                                     
                                    $updateData1=[
                                            'return_status_updates'=> json_encode($itemReturUpdates),
                                            'return_status' => $status
                                        ];
                                    if ($status === 'DELIVERED' || $status === 'EXCHANGE DELIVERED') {
                                        $updateData['delivered_date'] = now();
                                    }
                                    DB::table('return_shipments')->where('uuid', $orderId)
                                    ->update($updateData);
                            }
                        } else {
                             $vendorOrder = DB::table('vendor_orders')
                                ->where('uuid', $orderId)
                                ->first();
                            // For normal orders
                             $vendorId = $vendorOrder->vendor_id;   // Last character
                             $mainOrderId = $vendorOrder->order_id;;
                           // dd($vendorId,$mainOrderId);
                           
                            $order = DB::table('orders')->where('id', $mainOrderId)->first();
                           
                             
                            if ($order && $vendorOrder) {
                               // $friendlyStatus = ucfirst(strtolower(getFriendlyShipmentStatus($status)));
                                $paidStatus = ($status == 'DELIVERED' || $status === '7') ? 'Paid' : 'Pending';

                                $vendorUpdates = json_decode($vendorOrder->delivery_status_updates, true) ?? [];

                             
                                $vendorUpdates = array_merge($vendorUpdates, $statusUpdates);
                                 $updateData = [
                                        'awb'                    => $awbCode,
                                        'delivery_status'        => $status,
                                        'courier_name'           => $courierName,
                                        'paid_status'            =>($status == 'DELIVERED' || $status === '7') ? 'Paid' : 'Unpaid',
                                        'delivery_status_updates'=> json_encode($vendorUpdates),
                                        'estimated_delivery'     => date('Y-m-d H:i:s', strtotime($etd)),
                                        'undelivered_reason'     => $request->input('undelivered_reason'),
                                        'pickup_exception'       => $request->input('pickup_exception_reason'),
                                        'updated_at'             => now(),
                                    ];

                                    if ($status === 'DELIVERED') {
                                        $updateData['delivered_date'] = now();
                                    }
                                DB::table('vendor_orders')
                                    ->where('uuid', $orderId)
                                    ->update($updateData);
                                DB::table('orders')
                                    ->where('id', $mainOrderId)
                                    ->update(['paid_status'=>$paidStatus]);
                                //    DB::table('vendor_order_status_updates')
                                //      ->insert([
                                //          'vendor_order_id'         => $vendorOrder->id,
                                //          'shipping_status'              =>$status,
                                        
                                //      ]);
                                  $item_status_updates=json_decode(DB::table('order_items')
                                                ->where([
                                                  //  'delivery_status' =>$status,
                                                    'order_id'  => $order->id,
                                                    'vendor_id' => $vendorId,
                                                ])->first()->delivery_status_updates,true);
                                $new_status=  array_merge($item_status_updates, $statusUpdates);
                                 
                                $updateData1=[
                                    'delivery_status_updates'=> json_encode($new_status),
                                    'delivery_status' => $status
                                ];
                                 if ($status === 'DELIVERED') {
                                        $updateData1['delivered_date'] = now();
                                    }
                                     DB::table('order_items')
                                                ->where([
                                                  //  'delivery_status' =>$status,
                                                    'order_id'  => $order->id,
                                                    'vendor_id' => $vendorId,
                                                ])
                                                ->update($updateData1);
                            }

                          
                        }
                          DB::commit();
                    }
                } catch (\Exception $ex) { \Sentry\captureException($ex);
                    DB::rollBack();

                    DB::table('system_errors')->insert([
                        'error'          => $ex->getMessage(),
                        'which_function' => 'webhook',
                        'params'         => json_encode($payload),
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Webhook processed successfully.'], 200);
    }
  public function handleDelhiveryWebhook(Request $request)
{
    $payload = $request->all();
    Log::info('Delhivery Webhook Received:', $payload);

    if (!isset($payload['Shipment']['Status'])) {
        return response()->json(['message' => 'Invalid payload'], 400);
    }

    $shipment   = $payload['Shipment'];
    $statusData = $shipment['Status'];

    $awb        = $shipment['AWB'] ?? null;
    $reference  = $shipment['ReferenceNo'] ?? null; // your order_id
    $statusType = $statusData['StatusType'] ?? null;
    $status     = $statusData['Status'] ?? null;
    $date       = $statusData['StatusDateTime'] ?? now();

    // Map Delhivery status → friendly status
    $friendlyStatus = $this->mapDelhiveryStatus($statusType, $status);

    DB::table('webhook_response')->insert([
        'message'  => json_encode($payload),
        'order_id' => $reference,
        'awb'      => $awb,
        'status'   => $friendlyStatus,
    ]);

    if (!$reference || !$awb) {
        return response()->json(['message' => 'Missing reference or AWB'], 400);
    }

    DB::beginTransaction();
    try {
        if (str_contains(strtoupper($friendlyStatus), 'RETURN')) {
            // 🔄 Return shipments
            $returnOrder = DB::table('return_shipments')->where('uuid', $reference)->first();
            if ($returnOrder) {
                $oldUpdates = $returnOrder->return_status_updates ? json_decode($returnOrder->return_status_updates, true) : [];
                $newUpdates = array_merge($oldUpdates, [[
                    'date'   => $date,
                    'status' => $friendlyStatus,
                    'icon'   => $friendlyStatus,
                ]]);

                DB::table('return_shipments')
                    ->where('uuid', $reference)
                    ->update([
                        'return_status'         => $friendlyStatus,
                        'return_status_updates' => json_encode($newUpdates),
                        'updated_at'            => now(),
                    ]);

                DB::table('return_items')
                    ->where('return_shipment_id', $returnOrder->id)
                    ->update([
                        'return_status'         => $friendlyStatus,
                        'return_status_updates' => json_encode($newUpdates),
                    ]);
            }
        } else {
            // 🔄 Vendor Orders
            $vendorOrder = DB::table('vendor_orders')->where('uuid', $reference)->first();
            if ($vendorOrder) {
                $oldUpdates = $vendorOrder->delivery_status_updates ? json_decode($vendorOrder->delivery_status_updates, true) : [];
                $newUpdates = array_merge($oldUpdates, [[
                    'date'   => $date,
                    'status' => $friendlyStatus,
                    'icon'   => $friendlyStatus,
                ]]);

                $updateData = [
                    'delivery_status'         => $friendlyStatus,
                    'delivery_status_updates' => json_encode($newUpdates),
                    'updated_at'              => now(),
                ];
                if ($friendlyStatus === 'DELIVERED') {
                    $updateData['delivered_date'] = now();
                    $updateData['paid_status'] = 'Paid';
                }

                DB::table('vendor_orders')
                    ->where('id', $vendorOrder->id)
                    ->update($updateData);

                // 🔄 Update parent order payment status
                DB::table('orders')
                    ->where('id', $vendorOrder->order_id)
                    ->update([
                        'paid_status' => $friendlyStatus === 'DELIVERED' ? 'Paid' : 'Unpaid'
                    ]);

                // 🔄 Insert vendor order status update entry
                // DB::table('vendor_order_status_updates')->insert([
                //     'vendor_order_id'  => $vendorOrder->id,
                //     'shipping_status'  => $friendlyStatus,
                //     'created_at'       => now(),
                //     'updated_at'       => now(),
                // ]);

                // 🔄 Update order items too
                $items = DB::table('order_items')
                    ->where('order_id', $vendorOrder->order_id)
                    ->where('vendor_id', $vendorOrder->vendor_id)
                    ->get();

                foreach ($items as $item) {
                    $itemUpdates = $item->delivery_status_updates ? json_decode($item->delivery_status_updates, true) : [];
                    $itemUpdates = array_merge($itemUpdates, [[
                        'date'   => $date,
                        'status' => $friendlyStatus,
                        'icon'   => $friendlyStatus,
                    ]]);

                    $itemUpdateData = [
                        'delivery_status'        => $friendlyStatus,
                        'delivery_status_updates'=> json_encode($itemUpdates),
                        'updated_at'             => now(),
                    ];
                    if ($friendlyStatus === 'DELIVERED') {
                        $itemUpdateData['delivered_date'] = now();
                    }

                    DB::table('order_items')
                        ->where('id', $item->id)
                        ->update($itemUpdateData);
                }
            }
        }

        DB::commit();
    } catch (\Exception $ex) {
        DB::rollBack();
        \Sentry\captureException($ex);

        DB::table('system_errors')->insert([
            'error'          => $ex->getMessage(),
            'which_function' => 'delhivery_webhook',
            'params'         => json_encode($payload),
        ]);
    }

    return response()->json(['message' => 'Webhook processed successfully.'], 200);
}

    private function mapDelhiveryStatus($statusType, $status)
{
    $statusType = strtoupper($statusType);
    $status     = ucfirst(strtolower($status));

    $map = [
        // Forward / Normal Orders
        'UD' => [
            'Manifested'   => 'ORDER PLACED',
            'Not Picked'   => 'APPROVED',
            'In Transit'   => 'IN TRANSIT',
            'Pending'      => 'APPROVED',
            'Dispatched'   => 'OUT FOR DELIVERY',
        ],
        'DL' => [
            'Delivered' => 'DELIVERED',
        ],

        // Return shipments
        'RT' => [
            'In Transit' => 'IN TRANSIT',
            'Pending'    => 'APPROVED',
            'Dispatched' => 'OUT FOR DELIVERY',
        ],
        'DL_RTO' => [
            'RTO' => 'RETURN REQUESTED',
        ],

        // Reverse pickups (Exchange/Return pickups)
        'PP' => [
            'Open'       => 'EXCHANGE REQUESTED',
            'Scheduled'  => 'APPROVED',
            'Dispatched' => 'OUT FOR PICKUP',
        ],
        'PU' => [
            'In Transit' => 'PICKED UP',
            'Pending'    => 'APPROVED',
            'Dispatched' => 'OUT FOR DELIVERY',
        ],
        'DL_DTO' => [
            'DTO' => 'DELIVERED',
        ],
        'CN' => [
            'Canceled' => 'CANCELLED',
        ],
    ];

    // Special handling where DL with "RTO"
    if ($statusType === 'DL' && strtoupper($status) === 'RTO') {
        return 'RETURN REQUESTED';
    }
    if ($statusType === 'DL' && strtoupper($status) === 'DTO') {
        return 'DELIVERED';
    }

    return $map[$statusType][$status] ?? $status;
}


    public function track(Request $request)
    {
        $shiprocketService = app(\App\Services\ShiprocketService::class);
        $response = $shiprocketService->getOrderTracking($request->id);

        // Optional: return or log $response
        // return response()->json($response);
    }
}
