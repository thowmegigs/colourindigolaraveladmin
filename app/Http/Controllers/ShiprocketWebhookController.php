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
                        if (str_contains($status, 'Return') || str_contains($status, 'Exchange')) {
                            $returnOrder = DB::table('return_shipments')->where('uuid', $orderId)->first();
                            $vendorOrder = DB::table('vendor_orders')
                                ->where('id', $returnOrder->vendor_order_id)
                                ->first();

                            if ($returnOrder && $vendorOrder) {
                                $mainOrder = DB::table('orders')
                                    ->where('id', $vendorOrder->order_id)
                                    ->first();

                                $orderUpdates = json_decode($mainOrder->order_delivery_updates, true) ?? [];
                                $vendorUpdates = json_decode($vendorOrder->delivery_status_updates, true) ?? [];
                                $returnOrderUpdates = json_decode($returnOrder->return_status_updates, true) ?? [];

                                $orderUpdates = array_merge($orderUpdates, $statusUpdates);
                                $vendorUpdates = array_merge($vendorUpdates, $statusUpdates);
                                $returnOrderUpdates = array_merge($returnOrderUpdates, $statusUpdates);

                                DB::table('vendor_shipments')
                                    ->where('id', $returnOrder->vendor_order_id)
                                    ->update([
                                        'return_status'         => $shipStatus,
                                        'return_status_updates' => json_encode($returnOrderUpdates),
                                    ]);

                                DB::table('orders')
                                    ->where('id', $mainOrder->id)
                                    ->update([
                                        'delivery_status'        => 'Mixed',
                                        'updated_at'             => now(),
                                        'order_delivery_updates' => json_encode($orderUpdates),
                                    ]);

                                DB::table('vendor_orders')
                                    ->where('id', $vendorOrder->id)
                                    ->update([
                                        'delivery_status'         => 'Mixed',
                                        'updated_at'              => now(),
                                        'delivery_status_updates' => json_encode($vendorUpdates),
                                    ]);
                                DB::table('vendor_order_status_updates')
                                    ->insert([
                                        'vendor_order_id'         => $vendorOrder->id,
                                        'shipping_status'              =>$status,
                                        
                                    ]);

                                DB::table('order_items')
                                    ->where([
                                        'order_id'  => $mainOrder->id,
                                        'vendor_id' => $vendorOrder->vendor_id,
                                    ])
                                    ->update(['delivery_status' => $status]);

                                DB::table('return_items')
                                    ->where([
                                        'return_shipment_id' => $returnOrder->id,
                                    ])
                                    ->update(['return_status' => $shipStatus]);
                            }
                        } else {
                            // For normal orders
                            [$mainOrderId, $vendorId] = explode('/', $orderId);
                           
                            $order = DB::table('orders')->where('uuid', $mainOrderId)->first();
                            $vendorOrder = DB::table('vendor_orders')
                                ->where('order_id', $order->id ?? 0)
                                ->where('vendor_id', $vendorId)
                                ->first();
                             
                            if ($order && $vendorOrder) {
                               // $friendlyStatus = ucfirst(strtolower(getFriendlyShipmentStatus($status)));
                                $paidStatus = ($status == 'DELIVERED' || $status === '7') ? 'Paid' : 'Pending';

                                $orderUpdates = json_decode($order->order_delivery_updates, true) ?? [];
                                $vendorUpdates = json_decode($vendorOrder->delivery_status_updates, true) ?? [];

                                $orderUpdates = array_merge($orderUpdates, $statusUpdates);
                                $vendorUpdates = array_merge($vendorUpdates, $statusUpdates);

                                DB::table('vendor_orders')
                                    ->where('order_id', $order->id)
                                    ->where('vendor_id', $vendorId)
                                    ->update([
                                        'awb'                    => $awbCode,
                                        'delivery_status'        => $status,
                                        'courier_name'           => $courierName,
                                        'paid_status'            =>($status == 'DELIVERED' || $status === '7') ? 'Paid' : 'Unpaid',
                                        'delivery_status_updates'=> json_encode($vendorUpdates),
                                        'estimated_delivery'     => date('Y-m-d H:i:s', strtotime($etd)),
                                        'undelivered_reason'     => $request->input('undelivered_reason'),
                                        'pickup_exception'       => $request->input('pickup_exception_reason'),
                                        'updated_at'             => now(),
                                    ]);
                               DB::table('vendor_order_status_updates')
                                    ->insert([
                                        'vendor_order_id'         => $vendorOrder->id,
                                        'shipping_status'              =>$status,
                                        
                                    ]);
                                  
                                DB::table('orders')
                                    ->where('id', $order->id)
                                    ->update([
                                        'delivery_status'        => $status,
                                        'paid_status'            => $paidStatus,
                                        'order_delivery_updates' => json_encode($orderUpdates),
                                    ]);

                                DB::table('order_items')
                                    ->where([
                                        'order_id'  => $order->id,
                                        'vendor_id' => $vendorId,
                                    ])
                                    ->update(['delivery_status' => $status]);
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

    public function track(Request $request)
    {
        $shiprocketService = app(\App\Services\ShiprocketService::class);
        $response = $shiprocketService->getOrderTracking($request->id);

        // Optional: return or log $response
        // return response()->json($response);
    }
}
