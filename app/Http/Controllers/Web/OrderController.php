<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Controller;
use Auth;
use Batch;
use Illuminate\Http\Request;
use \Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkout(Request $r)
    {
        $data['user_address'] = \DB::table('user_address')->whereUserId(auth()->id())->first();
       $data['states'] = \DB::table('states')->get();
        $data['setting'] = \DB::table('settings')->first();
        $credit = \DB::table('points_history')->whereUserId(auth()->id())->whereStatus('Completed')->whereMode('Credit')->sum('points');
        $debit = \DB::table('points_history')->whereUserId(auth()->id())->whereStatus('Completed')->whereMode('Debit')->sum('points');
          $data['user_city']= $data['user_address'] ?\DB::table('cities')->where('id',$data['user_address']->billing_city)->first():null;
        $data['wallet'] = ($credit - $debit) * $data['setting']->point_value;
        return view('frontend.checkout', with($data));
    }
    public function index()
    {
        $data['orders'] = \App\Models\Order::with('items')->whereUserId(auth()->id())->latest()->get();
        return view('frontend.account.order_history', with($data));
    }

    public function show($id)
    {
        $order = \App\Models\Order::with(['user' => function ($q) {
            $q->select('id', 'name', 'phone', 'lat', 'lang', 'address', 'pincode');
        }, 'driver' => function ($q) {
            $q->select('id', 'name', 'phone');
        }, 'items'])->whereId($id)->first();

        return response()->json(['data' => $order], 200);
    }

    public function store(Request $r)
    {

        $setting = \DB::table('settings')->first();
        $shipping_charge = $setting != null ? $setting->delivery_charge : 0;
        // $email = $r->email;
        // $phone = $r->phone;
        $cart_level_discount = session('cart_discount') ?? 0;
        $shipping_discount = session('shipping_discount') ?? 0;
        $shipping_cost = $shipping_charge - $shipping_discount;
        //  dlog('cart sesion',$cart_session_id);
        $user = Auth::user();
        if (is_null($user)) {
            return response()->json(['success' => false, 'message' => 'Please login to place order'], 400);
        }
        $user_id = $user->id;
        $cart_session_id = session('cart_session_id');
\DB::table('carts')->where('cart_session_id', $cart_session_id)->update(['user_id'=>$user_id]);
        $carts = \DB::table('carts')->where('cart_session_id', $cart_session_id)->get()->toArray();
        $order_items_ar = [];
        $total_items = 0;
        $total_discount = 0;
        $net_payable = 0;
        $total_amount_after_discount = 0;
        $total_amount = 0;
        $total_tax = 0;
        $product_qty_update_ar = [];
        $variant_qty_update_ar = [];

        foreach ($carts as $t) {

            unset($t->id);
            unset($t->category_id);
            unset($t->created_at);
            unset($t->updated_at);
            unset($t->discount_applies_on_qty);
            unset($t->affected_by_coupon_ids);

            $total_items += 1;

            $order_items_ar[] = (array) $t;
            $total_discount += $t->total_discount;
            $total_amount_after_discount += $t->net_cart_amount;
            $total_amount += $t->price * $t->qty;
            $total_tax += $t->total_tax;
            $product_qty_update_ar[] = ['id' => $t->product_id, 'quantity' => ['-', $t->qty]];
            if (!empty($t->variant_id)) {

                $variant_qty_update_ar[] = ['id' => $t->variant_id, 'quantity' => ['-', $t->qty]];
            }

        }
        $orderid = null;
        \DB::beginTransaction();
        try
        {
            $order_for_cart_exist = \DB::table('orders')->whereCartSessionId($cart_session_id)->whereUserId($user_id)->exists();
            if (!$order_for_cart_exist) {
                $prodInstance = new \App\Models\Product;
                Batch::update($prodInstance, $product_qty_update_ar, 'id');
                if (!empty($variant_qty_update_ar)) {
                    $prodInstance1 = new \App\Models\ProductVariant;
                    Batch::update($prodInstance1, $variant_qty_update_ar, 'id');
                }
            }
            if (!empty($order_items_ar)) {
                \DB::table('order_items')->whereCartSessionId($cart_session_id)->whereUserId($user_id)->delete();
                \DB::table('orders')->whereCartSessionId($cart_session_id)->whereUserId($user_id)->delete();
                unset($order_items_ar['max_qty_allowed']);

                \DB::table('order_items')->insert($order_items_ar);

                $net_payable = $total_amount_after_discount + $total_tax + $shipping_cost - $cart_level_discount;
                $razor_orderId = null;
                $order_id = date('ymdhi') . $user_id;
                $status_updates = [
                    [
                        'name' => 'Order Placed',
                        'message' => '', 'date' => date('Y-m-d H:i:s'),
                    ],
                ];

                $order_ar = [
                    'total_amount' => $total_amount,
                    'total_amount_after_discount' => $total_amount_after_discount,
                    'cart_session_id' => $cart_session_id,
                    'net_payable' => $net_payable - $r->wallet_amount_used,
                    'shipping_cost' => $shipping_cost,
                    'total_tax' => $total_tax,
                    'total_discount' => $total_discount,
                    'user_id' => $user_id,
                    'slot_time' => $r->slot_time,
                    'no_of_items' => $total_items,
                    'slot_date' => date("Y-m-d", strtotime($r->slot_date)),
                    'uuid' => $order_id,
                    'cart_level_discount' => $cart_level_discount,
                    'payment_method' => $r->has('payment_method') ? $r->payment_method : 'cod',
                    'razorpay_order_id' => $razor_orderId,
                    'order_delivery_updates' => json_encode($status_updates),
                    'wallet_amount_used' => $r->has('wallet_amount_used') ? $r->wallet_amount_used : 0,
                    'delivery_instructions' => $r->delivery_instructions,
                ];
                $orderid = \DB::table('orders')->insertGetId($order_ar);

                \DB::table('order_items')->whereCartSessionId($cart_session_id)->update(['order_id' => $orderid]);

                $billing_address = [
                    'user_id' => auth()->id(),
                    'billing_address1' => $r->billing_address1,
                    'billing_address2' => $r->billing_address2,
                    'billing_fname' => $r->billing_fname,
                    'billing_lname' => $r->billing_lname,
                    'billing_city' => $r->billing_city,
                    'billing_state' => $r->has('billing_state') ? $r->billing_state : 'Uttar Pradesh',
                    'billing_pincode' => $r->billing_pincode,
                    'billing_email' => $r->billing_email,
                    'billing_phone' => $r->billing_phone,
                    'shipping_fname' => $r->billing_fname,
                    'shipping_lname' => $r->billing_lname,
                    'shipping_address1' => $r->has('ship_to_diffrent_address') ? $r->shipping_address1 : $r->billing_address1,
                    'shipping_address2' => $r->has('ship_to_diffrent_address') ? $r->shipping_address2 : $r->billing_address2,
                    'shipping_city' => $r->has('ship_to_diffrent_address') ? $r->shipping_city : $r->billing_city,
                    'shipping_state' => $r->has('ship_to_diffrent_address') ? $r->shipping_state : $r->billing_state,
                    'shipping_pincode' => $r->has('ship_to_diffrent_address') ? $r->shipping_pincode : $r->billing_pincode,

                ];
                if (!\DB::table('user_address')->whereUserId(auth()->id())->exists()) {
                    \DB::table('user_address')->insert(
                        $billing_address
                    );
                }

                if ($setting->point_system == 'Yes') {
                    if ($r->wallet_amount_used > 0) {
                        // $prev_wallet_point = \DB::table('user_point_and_wallet')->whereUserId($user_id)->first()->point;
                        $used_point = ceil($r->wallet_amount_used / $setting->point_value);
                        \DB::table('points_history')->insert([
                            'user_id' => $user_id, 'points' => $used_point,
                            'created_at' => date('Y-m-d H:i:s'), 'mode' => 'Debit', 'status' => 'Completed',
                            'order_id' => $order_id, 'remarks' => 'Spent in Order Payment',
                        ]);

                        //\DB::table('user_point_and_wallet')->whereUserId($user_id)->decrement('point', $used_point);

                    }

                    $amount_paid = $net_payable - $r->wallet_amount_used;
                    if ($setting->minimum_order_amount_for_point != null && $amount_paid >= $setting->minimum_order_amount_for_point) {
                        $point_earned = floor($amount_paid / $setting->point_value);
                        \DB::table('points_history')->insert([
                            'user_id' => $user_id, 'points' => $point_earned,
                            'remarks' => 'Earned in Order',
                            'created_at' => date('Y-m-d H:i:s'), 'mode' => 'Credit', 'order_id' => $order_id,
                        ]);

                    }
                }

            }
            if ($r->payment_method == 'Online') {
                \DB::commit();
                // session('order_id',$orderid);
                \Session::put('order_id', $orderid);
                \Session::save();
                $report = new PaymentController;

                $razor_orderId = $report->createOrderId($net_payable, $user_id);
                return response()->json(['success' => true, 'razorpay_orderid' => $razor_orderId, 'amount' => $net_payable], 200);
            } else {
                if ($setting->point_system == 'Yes') {
                    if ($r->wallet_amount_used > 0) {
                        // $prev_wallet_point = \DB::table('user_point_and_wallet')->whereUserId($user_id)->first()->point;
                        $used_point = ceil($r->wallet_amount_used / $setting->point_value);
                        \DB::table('points_history')->insert([
                            'user_id' => $user_id, 'points' => $used_point,
                            'created_at' => date('Y-m-d H:i:s'), 'mode' => 'Debit', 'status' => 'Completed',
                            'order_id' => $order_id, 'remarks' => 'Spent in Order Payment',
                        ]);

                        //\DB::table('user_point_and_wallet')->whereUserId($user_id)->decrement('point', $used_point);

                    }

                    $amount_paid = $net_payable - $r->wallet_amount_used;
                    if ($setting->minimum_order_amount_for_point != null && $amount_paid >= $setting->minimum_order_amount_for_point) {
                        $point_earned = floor($amount_paid / $setting->point_value);
                        \DB::table('points_history')->insert([
                            'user_id' => $user_id, 'points' => $point_earned,
                            'remarks' => 'Earned in Order',
                            'created_at' => date('Y-m-d H:i:s'), 'mode' => 'Credit', 'order_id' => $order_id,
                        ]);

                    }
                }
                \DB::table('carts')->whereUserId($user_id)->delete();
                session()->forget('shoppingCart');
                $applied_coupons = \DB::table('applied_coupons')->where(['cart_session_id' => $cart_session_id, 'user_id' => $user_id])->get();
                $coupon_usage_updates = [];
                if (count($applied_coupons->toArray()) > 0) {
                    foreach ($applied_coupons as $h) {
                        $coupon_id = $h->coupon_id;
                        // if (!empty($coupon_usage_updates)) {
                        //     if (!in_array($coupon_id, array_keys($coupon_usage_updates))) {
                        //         $coupon_usage_updates[$coupon_id] = 1;
                        //     } else {
                        //         $coupon_usage_updates[$coupon_id] = $coupon_usage_updates[$coupon_id] + 1;
                        //     }

                        // } else {
                        $coupon_usage_updates[$coupon_id] = 1;
                        // }

                    }
                    if (!empty($coupon_usage_updates)) {
                        foreach ($coupon_usage_updates as $coupon_id => $count) {
                            \DB::table('coupons')->whereId($coupon_id)
                                ->increment('total_used_till_now', 1);
                            if (\DB::table('coupon_usage_by_customers')->whereUserId($user_id)->whereCouponId($coupon_id)->exists()) {
                                \DB::table('coupon_usage_by_customers')->whereUserId($user_id)->whereCouponId($coupon_id)
                                    ->increment('count', 1);
                            } else {
                                \DB::table('coupon_usage_by_customers')->insert([
                                    'user_id' => $user_id, 'coupon_id' => $coupon_id, 'count' => 1,
                                ]);
                            }
                        }
                    }
                }
                 session()->forget('cart_session_id');
                \DB::commit();
                return response()->json(['success' => true]);
            }

        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();
            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '==Line==' . $ex->getLine() . '==File==' . $ex->getFile(),
                'created_at' => Carbon::now(),
                'which_function' => 'Order place Saving',
            ]);
            return response()->json(['success' => false, 'message' => $ex->getMessage()], 400);

        }
    }

}
