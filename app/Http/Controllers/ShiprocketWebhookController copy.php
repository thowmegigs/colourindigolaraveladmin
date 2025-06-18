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
            ]);

            if ($request->has('awb')) {
                DB::beginTransaction();
                try{
                $awbCode     = $request->input('awb');
                $orderId     = $request->input('order_id');
                $status      = $request->input('current_status');
                 $shipStatus      = $request->input('shipment_status');
                $courierName = $request->input('courier_name');
                $etd         = $request->input('etd');
                $scans       = $request->input('scans');

                // Build status updates array
              $statusUpdates = collect($scans)
                    ->filter(function ($scan) {
                        return strtoupper($scan['sr-status-label']) !== 'NA';
                    })
                    ->map(function ($scan) {
                        $friendly = ucfirst(strtolower($scan['sr-status-label']));
                        $st = ucfirst(strtolower(getFriendlyShipmentStatus($friendly)));
                        return [
                            'date'   => $scan['date'],
                            'status' => $st,
                            'icon'   => $st,
                        ];
                    })
                    ->toArray();

                if ($orderId) {
                   if (str_contains($status, 'Return') || str_contains($status, 'Exchange'))
                    {
                       
                        $returnOrder        = DB::table('return_shipments')->where('uuid', $orderId)->first();
                        $vendorOrder  = DB::table('vendor_orders')
                                            ->where('id', $returnOrder->vendor_order_id)
                                           
                                            ->first();

                        if ($returnOrder && $vendorOrder) {
                           $mainOrder=DB::table('orders')
                                            ->where('id', $vendorOrder->order_id)
                                            ->first();
                           
                            //$paidStatus     = ($status == 'DELIVERED' || $status == '7') ? 'Paid' : 'Pending';

                            // Update order delivery history
                            $orderUpdates = json_decode($mainOrder->order_delivery_updates, true) ?? [];
                            $vendorUpdates = json_decode($vendorOrder->delivery_status_updates, true) ?? [];
                            $returnOrderUpdates = json_decode($returnOrder->return_status_updates, true) ?? [];

                            $orderUpdates  = array_merge($orderUpdates, $statusUpdates);
                            $vendorUpdates = array_merge($vendorUpdates, $statusUpdates);
                            $returnOrderUpdates = array_merge($returnOrderUpdates, $statusUpdates);

                            DB::table('vendor_shipments')
                                 ->where('id', $returnOrder->vendor_order_id)
                                ->update([
                                   'return_status'  => $shipStatus,
                                   'return_status_updates'=> json_encode($returnOrderUpdates),
                                   
                                ]);

                            DB::table('orders')
                                ->where('id', $order->id)
                                ->update([
                                    'delivery_status'        => 'Mixed',
                                     'updated_at'=>date('Y-m-d H:i:s'),
                                   
                                    'order_delivery_updates' => json_encode($orderUpdates),
                                ]);
                            DB::table('vendor_orders')
                                ->where('id', $vendorOrder->id)
                                ->update([
                                    'delivery_status'        => 'Mixed',
                                    'updated_at'=>date('Y-m-d H:i:s'),
                                   
                                    'delivery_status_updates' => json_encode($vendorOrderUpdates),
                                ]);

                            DB::table('order_items')
                                ->where([
                                    'order_id'  => $order->id,
                                    'vendor_id' => $vendorId,
                                ])
                                ->update(['delivery_status' => $status]);
                            DB::table('return_items')
                                ->where([
                                    'return_shipment_id'  => $returnOrder ->id,
                                    
                                ])
                                ->update(['return_status' => $shipStatus]);
                        
                        }
                  }
                else{/**for normal order */

                }
                       \DB::commit();
                }
            }catch(\Exception $ex){
                DB::rollback();
                DB::table('system_errors')->insert([
                    'error'=>$ex->getMessage(),
                    'which_function'=>'webhoook',
                    'params'=>json_encode($payload)


                ]);

            }
            }
        }

        return response()->json(['message' => 'Webhook processed successfully.'], 200);
    }

    public function track(Request $request)
    {
        $shiprocketService = app(\App\Services\ShiprocketService::class);
        $response = $shiprocketService->getOrderTracking($request->id);

        // Optionally return or log the response
        // return response()->json($response);
    }
}
