<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Service\CartService;
use Auth;
use Illuminate\Http\Request;
use \Carbon\Carbon;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function index(Request $r)
    {

        return view('frontend.cart');

    }

    public function cartData(Request $r)
    {

        // $email = $r->email;
        // $phone = $r->phone;
        try {
            $user = Auth::user();
            $user_id = 1;
            if ($user) {
                $user_id = $user->id;
            }
          //  session()->forget('cart_session_id');
           if (!session()->has('cart_session_id')) {
            session(['cart_session_id' =>  uniqid('', true)]);
        }
         $cart_session_id = session('cart_session_id');
            $cart_items = [];
            if ($cart_session_id) {
                $cart_items = \DB::table('carts')->whereCartSessionId($cart_session_id)->get();
            }

            $applied_coupons_names = [];
            $cartValueAndShippingDiscountresult = null;

            if (!empty($cart_items[0])) {
                $cart_session_id = $cart_items[0]->cart_session_id;
                checkAppliedCouponsValidity($cart_items);
                $p = applyAppliedCouponsTableToCart($cart_items, $cart_session_id, $user_id, false);
                $cartValueAndShippingDiscountresult = $p['cartValueAndrShippingDiscountresult'];
                $applied_coupons_names = $p['applied_coupons_names'];
            }
            //  dd(cart_update_arr);

            $minimu_cart_offer = getOnylCartMinimumAmountOffers($cart_items, $user_id);
            $eligible_offer = getEligibleOffers($cart_items, $user_id);
            $applied_coupons_names = remove_duplicate_coupon_names($applied_coupons_names);
            $cart_items = \DB::table('carts')->whereCartSessionId($cart_session_id)->get();

            // session(['cart_discount' => $cartValueAndShippingDiscountresult ? $cartValueAndShippingDiscountresult['cart_amount_discount'] : 0.0,
            //     'shipping_discount' => $cartValueAndShippingDiscountresult ? $cartValueAndShippingDiscountresult['shipping_discount'] : 0.0]);
            //
            $cart_details = $this->cartService->returnCart($cart_items);
            $setting = \DB::table('settings')->first();
            $shipping_charge = $setting != null ? $setting->delivery_charge : 0;
            return response()->json(['success' => true,

                'cart_discount' => $cartValueAndShippingDiscountresult ? $cartValueAndShippingDiscountresult['cart_amount_discount'] : 0.0,
                'shipping_discount' => $cartValueAndShippingDiscountresult ? $cartValueAndShippingDiscountresult['shipping_discount'] : 0.0,
                'applicable_offers' => !empty($eligible_offer) ? remove_duplicate_coupon_names1($eligible_offer) : null,
                'minimum_cart_amount_offer' => !empty($minimu_cart_offer) ? $minimu_cart_offer[0] : null,
                'applied_coupons' => !empty($applied_coupons_names) ? $applied_coupons_names : null,
                'cart_items' => $cart_details['cart_items'],
                'total' => $cart_details['total'],
                'count' => count($cart_details['cart_items']),
                'sub_total' => $cart_details['sub_total'],
                'total_discount' => $cart_details['discount'],
                'shipping_charge' => intval($shipping_charge),

                'cart_session_id' => $cart_session_id,

            ], 200);

        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '==Line==' . $ex->getLine() . '==File==' . $ex->getFile(),
                'created_at' => Carbon::now(),
                'which_function' => 'Cart Index function',
            ]);
            return response()->json(['success' => false,
                'message' => 'Some Error occurred,Please try later ',

            ], 400);
        }
    }

    public function store(Request $r)
    {

        //.dd('hi');

        $post = $r->all();
        // $email = $r->email;
        // $phone = $r->phone;
        $user_id = 1;
        $user = Auth::user();
        if ($user) {
            $user_id = $user->id;
        }

        // $cart_id = $post['id'];
    //   session()->forget('cart_session_id');
        if (!session()->has('cart_session_id')) {
            session(['cart_session_id' =>  uniqid('', true)]);
        }
        
         $cart_session_id = session('cart_session_id');
       
        $productId = $r->product_id;
        $qty = $r->qty;
        $variantId = $r->has('variant_id') ? $r->variant_id : null;
        $attributes = $r->has('variant_attributes_val') ? $post['variant_attributes_val'] : null;
        if (!empty($attributes)) {
            $variant_row = getVariantRowFromAttributeVals($attributes, $productId);

            $variantId = $variant_row->id;
        }
        $addon_items = $r->has('addon_items') ? json_encode($post['addon_items'], true) : null;
        $addon_products = $r->has('addon_products') ? json_encode($post['addon_products'], true) : null;

        \DB::beginTransaction();

        $unique_cart_item_id = $productId . $variantId ?? '';
        $unique_cart_item_id = trim($unique_cart_item_id);
        $row = Cart::whereProductId($productId)->when(!is_null($variantId), function ($q) use ($variantId) {
            return $q->whereVariantId($variantId);
        })->first();
        try
        {
            $prod_row = \App\Models\Product::with('variants')->whereId($post['product_id'])->first();

            if (!is_null($row)) {

                if ($post['qty'] == 0 || empty($post['qty'])) {
                    $row->delete();
                    //$this->cartService->removeCart($productId,$variantId);
                } else {
                    $addon_items = !empty($addon_items) ? $addon_items : $row->addon_items;
                    $addon_products = !empty($addon_products) ? $addon_products : $row->addon_products;
                    $additional_amount = 0;
                    if ($addon_items != null) {
                        $items = json_decode($addon_items, true);
                        foreach ($items as $g) {
                            $additional_amount += $g['qty'] * $g['price'];
                        }
                    }
                    if ($addon_products != null) {
                        $items = json_decode($addon_products, true);
                        foreach ($items as $g) {
                            $additional_amount += $g['qty'] * $g['amount'];
                        }
                    }
                    $row->update([
                        'qty' => $qty,

                        'net_cart_amount' => $qty * ($row->sale_price + $additional_amount),
                        'total_discount' => ($row->price - $row->sale_price) * $qty,

                        'addon_items' => $addon_items,
                        'addon_products' => $addon_products,

                    ]);
                    //   dd('here');

                }
                
            } else {
                $additional_amount = 0;
                if ($addon_items != null) {
                    $items = json_decode($addon_items, true);
                    foreach ($items as $g) {
                        $additional_amount += $g['qty'] * $g['price'];
                    }
                }
                if ($addon_products != null) {
                    $items = json_decode($addon_products, true);
                    foreach ($items as $g) {
                        $additional_amount += $g['qty'] * $g['amount'];
                    }
                }
                $qty = $post['qty'];
                $thumb_image_small = !empty(getThumbnailsFromImage($prod_row->image)) ? getThumbnailsFromImage($prod_row->image)['medium'] : null;
                $thumb_image_small = $thumb_image_small != null ? url('/storage/products/' . $post['product_id'] . '/thumbnail/' . $thumb_image_small) : null;
                if (empty($variantId)) {

                    $avaialble_stock = $prod_row->quantity;
                    if ($avaialble_stock < $qty) {
                        return response()->json(['success' => false, 'message' => 'No more product is available ,Please try later when stock is back'], 400);
                    }

                } else {
                    $c_row = \DB::table('product_variants')->whereId($variantId)->first();
                    $avaialble_stock = $c_row->quantity;
                    if ($avaialble_stock < $qty) {
                        return response()->json(['success' => false, 'message' => 'No more product is available ,Please try later when stock is back'], 400);
                    }
                }
                $post['is_combo'] = 'No';

                $sale_price = $post['sale_price'];
                $price = $post['price'];
                $latest_cart_item = Cart::whereUserId($user_id)->latest()->first();
                //$cart_session_id = is_null($latest_cart_item) ? $user_id . uniqid() : $latest_cart_item->cart_session_id;
                $post['cart_session_id'] = $cart_session_id;
                $post['user_id'] = $user_id;
                $post['variant_id'] = $variantId;
                  $post['atributes_json'] = !empty($variantId) ? getChoosenVariantAttributesJson($prod_row->variants, $variantId) : null;
                $post['variant_name'] = !empty($variantId) ? getChoosenVariantName($prod_row->variants, $variantId) : null;
                $post['net_cart_amount'] = $qty * ($sale_price + $additional_amount);
                $post['total_discount'] = ($price - $sale_price) * $qty;
                $post['total_tax'] = $sale_price * (($post['sgst'] + $post['sgst'] + $post['igst']) / 100) * $qty;
                $post['image'] = $thumb_image_small;
                $post['addon_items'] = $addon_items;
                $post['addon_products'] = $addon_products;
                $row = Cart::create($post);

            }

            $cart_items = \DB::table('carts')->whereCartSessionId($cart_session_id)->whereUserId($user_id)->get();
            checkAppliedCouponsValidity($cart_items);
            /***belwo one  will add automtic coupons to table with insert and update_ar in the applied coupon table */
            $this->createAppliedCoupons($cart_items, $user_id);

            $applied_coupons_names = [];
            $cartValueAndShippingDiscountresult = null;
            if (!empty($cart_items)) {
                $cart_session_id = $cart_items[0]->cart_session_id;
                /***bewlo one line will apply applied coupons to cart items */
                $p = applyAppliedCouponsTableToCart($cart_items, $cart_session_id, $user_id);
                $cartValueAndrShippingDiscountresult = $p['cartValueAndrShippingDiscountresult'];
                $applied_coupons_names = $p['applied_coupons_names'];
            }
            $cart_items = \DB::table('carts')->whereCartSessionId($cart_session_id)->whereUserId($user_id)->get();

            $minimu_cart_offer = getOnylCartMinimumAmountOffers($cart_items, $user_id);
            $eligible_offer = getEligibleOffers($cart_items, $user_id);

            \DB::commit();
            $applied_coupons_names = remove_duplicate_coupon_names($applied_coupons_names);
            $cart_details = $this->cartService->returnCart($cart_items);
            $setting = \DB::table('settings')->first();
            $shipping_charge = $setting != null ? $setting->delivery_charge : 0;
           session(['cart_discount'=>$cartValueAndrShippingDiscountresult ? $cartValueAndrShippingDiscountresult['shipping_discount'] : 0.0]);
           session(['shipping_discount'=>$cartValueAndrShippingDiscountresult ? $cartValueAndrShippingDiscountresult['cart_amount_discount'] : 0.0]);
         
           return response()->json(['success' => true,

                'cart_discount' => $cartValueAndrShippingDiscountresult ? $cartValueAndrShippingDiscountresult['cart_amount_discount'] : 0.0,
                'shipping_discount' => $cartValueAndrShippingDiscountresult ? $cartValueAndrShippingDiscountresult['shipping_discount'] : 0.0,
                'applicable_offers' => !empty($eligible_offer) ? remove_duplicate_coupon_names1($eligible_offer) : null,
                'minimum_cart_amount_offer' => !empty($minimu_cart_offer) ? $minimu_cart_offer[0] : null,
                'applied_coupons' => !empty($applied_coupons_names) ? $applied_coupons_names : null,
                'cart_items' => $cart_details['cart_items'],
                'total' => $cart_details['total'],
                'count' => count($cart_details['cart_items']),
                'sub_total' => $cart_details['sub_total'],
                'total_discount' => $cart_details['discount'],
                'shipping_charge' => $shipping_charge,
                'cart_session_id' => $cart_session_id,

            ], 201);

        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();
            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '==Line==' . $ex->getLine() . '==File==' . $ex->getFile(),
                'created_at' => Carbon::now(),
                'which_function' => 'Cart Saving/updating',
            ]);
            return response()->json(['success' => false, 'message' => $ex->getMessage().'='.$ex->getLine() ], 400);

        }
    }

    public function createAppliedCoupons($cart_items, $user_id)
    {
        $applicable_coupon_rows = [];
        $cart_session_id = $cart_items[0]->cart_session_id;
        $cart_update_arr_for_product_offer_detail = [];

        $valid_coupons = \App\Models\Coupon::where([
            'discount_method' => 'Automatic',
        ])->whereStatus('Active')->whereDate('start_date', '<=', Carbon::now())
            ->whereRaw('(CASE WHEN total_usage_limit IS NOT NULL AND total_usage_limit>0 THEN total_usage_limit>total_used_till_now ELSE true END)')
            ->whereDate('end_date', '>=', Carbon::now())->orderBy('minimum_order_amount', 'DESC')->get();
        if (!empty($valid_coupons->toArray())) {
            foreach ($valid_coupons as $coupon_row) {

                $result = checkCouponApplicabilityForInsert($cart_items, $user_id, $coupon_row);

                $applicable_coupon_rows = array_merge($applicable_coupon_rows, $result['applicable_coupon_rows']);
                $cart_update_arr_for_product_offer_detail = array_merge($cart_update_arr_for_product_offer_detail, $result['cart_update_arr_for_product_offer_detail']);

            }
            /***applicable_coupon_rows== wo coupons rows hai jo ki applied table mein insert hone ke yogya ha****/
            \DB::table('applied_coupons')->whereCartSessionId($cart_session_id)->whereUserId($user_id)
                ->whereCouponMethod('Automatic')->delete();

            insertApplicableCouponsForInsertIntoAppliedTableWithProductOfferTextUpdate($cart_session_id, $user_id,
                $applicable_coupon_rows, $cart_update_arr_for_product_offer_detail, $cart_items
            );

        }

        return;
    }

    public function applyCouponCode(Request $r)
    {

        $cart_session_id =session('cart_session_id');;
        $coupon_code = $r->coupon_code;
        // $email = $r->email;
        // $phone = $r->phone;
        try {
            $user = Auth::user();
            $user_id = 1;
            if ($user) {
                $user_id = $user->id;
            }

            $coupon_row = \App\Models\Coupon::where([
                'coupon_code' => $coupon_code, 'discount_method' => 'Coupon Code',
            ])->whereDate('start_date', '<=', Carbon::now())->whereDate('end_date', '>=', Carbon::now())->whereStatus('Active')->first();
            if (is_null($coupon_row)) {
                return response()->json(['success' => false, 'message' => 'Coupon is invalid'], 400);
            }
            if ($coupon_row->total_usage_limit != null) {
                if ($coupon_row->total_used_till_now >= $coupon_row->total_usage_limit) {
                    return response()->json(['success' => false, 'message' => 'Coupon can not be used now '], 400);
                }
            }
            $cart_update_arr = [];
            $cart_insert_arr = [];
            $coupon_id = $coupon_row->id;

            $applicable_coupon_rows = [];
            $cart_update_arr_for_product_offer_detail = [];
            $cart_items = \DB::table('carts')->whereUserId($user_id)->get();
            $result = checkCouponApplicabilityForInsert($cart_items, $user_id, $coupon_row);
            $applicable_coupon_rows = $result['applicable_coupon_rows'];

            if (\Session::has('error')) {
                return response()->json(['success' => false, 'message' => \Session::get('error')], 400);

            }
            if (empty($applicable_coupon_rows)) {
                return response()->json(['success' => false, 'message' => 'Coupon conditions not satisfied,so not applied.Please read coupon terms & conditions'], 400);
            }

            $cart_update_arr_for_product_offer_detail = $result['cart_update_arr_for_product_offer_detail'];
            $exist_count = \DB::table('applied_coupons')->whereCartSessionId($cart_session_id)->whereUserId($user_id)
                ->whereCouponMethod('Coupon Code')->whereCouponId($coupon_id)->count();
            if ($exist_count > 0) {
                return response()->json(['success' => false, 'message' => 'Coupon is already used'], 400);
            }

            /***creating array for update or insert in cart items due to above coupon when applied   */

            insertApplicableCouponsForInsertIntoAppliedTableWithProductOfferTextUpdate($cart_session_id, $user_id,
                $applicable_coupon_rows, $cart_update_arr_for_product_offer_detail, $cart_items
            );

            $cart_items = \DB::table('carts')->whereUserId($user_id)->get();

            $applied_coupons_names = [];
            $cartValueAndShippingDiscountresult = null;
            if (!empty($cart_items)) {
                $cart_session_id = $cart_items[0]->cart_session_id;
                $p = applyAppliedCouponsTableToCart($cart_items, $cart_session_id, $user_id);

                $cartValueAndShippingDiscountresult = $p['cartValueAndrShippingDiscountresult'];
                $applied_coupons_names = $p['applied_coupons_names'];
            }
            //  dd(cart_update_arr);
            $applied_coupons_names = remove_duplicate_coupon_names($applied_coupons_names);
            $minimu_cart_offer = getOnylCartMinimumAmountOffers($cart_items, $user_id);
            $eligible_offer = getEligibleOffers($cart_items, $user_id);
            $exist = \DB::table('applied_coupons')->whereCartSessionId($cart_session_id)->whereUserId($user_id)
                ->whereCouponId($coupon_id)->count();
            $is_coupon_applied = $exist > 0 ? true : false;
            if ($is_coupon_applied) {
                $cart_items = \DB::table('carts')->whereUserId($user_id)->get();
                $cart_details = $this->cartService->returnCart($cart_items);
                $setting = \DB::table('settings')->first();
                $shipping_charge = $setting != null ? $setting->delivery_charge : 0;
                return response()->json(['success' => true,

                    'cart_discount' => $cartValueAndShippingDiscountresult ? $cartValueAndShippingDiscountresult['cart_amount_discount'] : 0.0,
                    'shipping_discount' => $cartValueAndShippingDiscountresult ? $cartValueAndShippingDiscountresult['shipping_discount'] : 0.0,
                    'applicable_offers' => !empty($eligible_offer) ? remove_duplicate_coupon_names1($eligible_offer) : null,
                    'minimum_cart_amount_offer' => !empty($minimu_cart_offer) ? $minimu_cart_offer[0] : null,
                    'applied_coupons' => !empty($applied_coupons_names) ? $applied_coupons_names : null,
                    'cart_items' => $cart_details['cart_items'],
                    'total' => $cart_details['total'],
                    'count' => count($cart_details['cart_items']),
                    'sub_total' => $cart_details['sub_total'],
                    'total_discount' => $cart_details['discount'],
                    'coupon_response' => 'Coupon Code ' . $coupon_row->coupon_code . ' applied successfully',
                    'cart_session_id' => $cart_session_id,
                    'shipping_charge' => $shipping_charge,

                ], 201);
            } else {
                return response()->json(['success' => false,
                    'message' => \Session::has('error') ? session('error') : 'Coupon failed to be applied'], 400);
            }
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '==Line==' . $ex->getLine() . '==File==' . $ex->getFile(),
                'created_at' => Carbon::now(),
                'which_function' => 'Apply Coupon Code function',
            ]);
            return response()->json(['success' => false,
                'message' => 'Some Error occurred,Please try later ',

            ], 400);
        }

    }
    public function removeCoupon(Request $r)
    {
        $cart_session_id = session('cart_session_id');

        $coupon_code = $r->coupon_code;
        // $email = $r->email;
        // $phone = $r->phone;
        try {
            $user = Auth::user();
            $user_id = $user->id;
            $coupon_row = \App\Models\Coupon::where([
                'coupon_code' => $coupon_code, 'discount_method' => 'Coupon Code',

            ])->first();

            $applied_coupon_rows = null;

            if (is_null($coupon_row)) {
                return response()->json(['success' => false, 'message' => 'Coupon is invalid'], 400);
            } else {
                $applied_coupon_rows = \DB::table('applied_coupons')->where([
                    'coupon_id' => $coupon_row->id, 'coupon_method' => 'Coupon Code',
                    'user_id' => $user->id, 'cart_session_id' => $cart_session_id,

                ])->get();

                \DB::table('applied_coupons')->where([
                    'coupon_id' => $coupon_row->id, 'coupon_method' => 'Coupon Code',
                    'user_id' => $user->id, 'cart_session_id' => $cart_session_id,

                ])->delete();

            }
            $cart_items = \DB::table('carts')->whereUserId($user_id)->get();
            $update_ar = [];
            $insert_ar = [];
            foreach ($applied_coupon_rows as $g) {

                if ($g->insert_ar != null) {
                    $insert_ar = array_merge($insert_ar, [...json_decode($g->insert_ar, true)]);
                }

                if ($g->update_ar != null) {
                    $update_ar = array_merge($update_ar, [...json_decode($g->update_ar, true)]);
                }

            }

            if (!empty($update_ar)) {
                $cartInstance = new \App\Models\Cart;
                $updates = [];
                foreach ($update_ar as $p) {
                    $item_id = $p['id'];
                    $filteres = [];
                    $g = $cart_items->toArray();
                    foreach ($g as $v) {
                        if ($v->id == $item_id) {
                            $filteres[] = $v;
                        }
                    }

                    foreach ($filteres as $related_cart_item) {
                        $t = $related_cart_item;
                        $item_qty_present_in_cart = $t->qty;
                        $price = $t->price;

                        $net_cart_amount = $t->sale_price * $t->qty;
                        $updates[] = ['id' => $t->id,
                            'discount_type' => null,
                            'discount' => 0,
                            'total_discount' => ($t->price - $t->sale_price) * $t->qty,
                            'net_cart_amount' => $net_cart_amount,
                            'product_discount_offer_detail' => '',
                            'is_combo' => 'No',
                            'discount_applies_on_qty' => null,
                        ];
                    }
                }
                foreach ($updates as $y) {
                    \DB::table('carts')->whereId($y['id'])->update($y);

                }

            }

            if (!empty($insert_ar)) {
                $deleteable_cart_ids = [];
                foreach ($insert_ar as $y) {
                    $prod_id = $y['product_id'];
                    $g = $cart_items->toArray();

                    foreach ($g as $v) {
                        if ($v->product_id == $prod_id && $v->cart_session_id = $cart_session_id &&
                            $v->user_id == $user_id && $v->is_combo == 'Yes') {

                            $deleteable_cart_ids[] = $v->id;
                        }
                    }

                    \DB::table('carts')->whereIn('id', $deleteable_cart_ids)->delete();

                }
            }

            $applied_coupons_names = [];
            $cartValueAndShippingDiscountresult = null;
            if (!empty($cart_items)) {
                $cart_session_id = $cart_items[0]->cart_session_id;
                $p = applyAppliedCouponsTableToCart($cart_items, $cart_session_id, $user_id, false);
                $cartValueAndShippingDiscountresult = $p['cartValueAndrShippingDiscountresult'];
                $applied_coupons_names = $p['applied_coupons_names'];
            }
            //  dd(cart_update_arr);

            $minimu_cart_offer = getOnylCartMinimumAmountOffers($cart_items, $user_id);
            $eligible_offer = getEligibleOffers($cart_items, $user_id);

            $cart_items = \DB::table('carts')->whereUserId($user_id)->get();
            $applied_coupons_names = remove_duplicate_coupon_names($applied_coupons_names);
            session(['cart_discount' => $cartValueAndShippingDiscountresult ? $cartValueAndShippingDiscountresult['cart_amount_discount'] : 0.0,
                'shipping_discount' => $cartValueAndShippingDiscountresult ? $cartValueAndShippingDiscountresult['shipping_discount'] : 0.0]);

            $data = [
                'applicable_offers' => !empty($eligible_offer) ? remove_duplicate_coupon_names1($eligible_offer) : null,
                'minimum_cart_amount_offer' => !empty($minimu_cart_offer) ? $minimu_cart_offer[0] : null,
                'applied_coupons' => !empty($applied_coupons_names) ? $applied_coupons_names : null,

            ];
            return response()->json(['success' => true, 'view' => view('frontend.partials.big_cart', with($data))->render()], 200);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '==Line==' . $ex->getLine() . '==File==' . $ex->getFile(),
                'created_at' => Carbon::now(),
                'which_function' => 'Revmoe Coupon function',
            ]);
            return response()->json(['success' => false,
                'message' => 'Some Error occurred,Please try later ',

            ], 400);
        }

    }
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteCart(Request $r)
    {

        \DB::beginTransaction();
        try {
            $product_id = $r->product_id;
            $cart_session_id = session('cart_session_id');
            // $is_cart_page = $r->is_cart_page;
            $variant_id = $r->has('variant_id') ? $r->variant_id : null;

            $cart_item = Cart::whereProductId($product_id)->whereCartSessionId($cart_session_id)->when(!is_null($variant_id), function ($q) use ($variant_id) {
                return $q->whereVariantId($variant_id);
            })->first();

            $user_id = $cart_item->user_id;
            $id = $cart_item->id;
            /**affected_by_coupon_ids shows the id in applied_coupon row not coupon id */
            $affected_coupon_ids = !empty($cart_item->affected_by_coupon_ids)
            ? json_decode($cart_item->affected_by_coupon_ids, true) : [];

            $cart_items = \DB::table('carts')->whereUserId($cart_item->user_id)
                ->where('id', '!=', $id)
                ->where('cart_session_id', $cart_item->cart_session_id)->get();
            $deletable_row_ids = [];
            $item_product_id = $cart_item->product_id;
            $affected_coupon_ids_row = null;

            if (!empty($affected_coupon_ids)) {
                $affected_coupon_ids_row = \DB::table('applied_coupons')->whereIn('coupon_id', $affected_coupon_ids)->get();
            } else {
                $affected_coupon_ids_row = \DB::table('applied_coupons')->where('due_to_product_id', $item_product_id)->get();
            }

            $insert_ar = [];
            $update_ar = [];
            if (!empty($affected_coupon_ids_row)) {
                foreach ($affected_coupon_ids_row as $g) {

                    if (!empty($g->due_to_product_id) && $g->due_to_product_id == $item_product_id) {
                        array_push($deletable_row_ids, $g->id);
                        if ($g->insert_ar != null) {
                            $insert_ar = array_merge($insert_ar, [...json_decode($g->insert_ar, true)]);
                        }

                        if ($g->update_ar != null) {
                            $update_ar = array_merge($update_ar, [...json_decode($g->update_ar, true)]);
                        }

                    } else {
                        $should_delete_this_id = true;

                        if (!empty($cart_items)) {
                            foreach ($cart_items as $t) {

                                $item_affected_coupon_ids = !empty($t->affected_by_coupon_ids)
                                ? json_decode($t->affected_by_coupon_ids, true) : [];

                                if (!empty($item_affected_coupon_ids)) {
                                    if (in_array($g->coupon_id, $item_affected_coupon_ids)) {

                                        $should_delete_this_id = false;
                                        break;

                                    }

                                }
                            }

                            if ($should_delete_this_id) {
                                if ($g->insert_ar != null) {
                                    $insert_ar = array_merge($insert_ar, [...json_decode($g->insert_ar, true)]);
                                }

                                if ($g->update_ar != null) {
                                    $update_ar = array_merge($update_ar, [...json_decode($g->update_ar, true)]);
                                }

                                array_push($deletable_row_ids, $g->id);
                            }
                        }
                    }

                }
            }

            if (!empty($deletable_row_ids)) {
                \DB::table('applied_coupons')->whereIn('id', $deletable_row_ids)->delete();
            }
            if (!empty($insert_ar)) {
                $insert_ar = array_map(function ($element) {
                    return (array) $element;
                }, $insert_ar);

                \DB::table('carts')->whereIn('product_id', array_column($insert_ar, 'product_id'))->delete();
            }
            if (!empty($update_ar)) {
                \DB::table('carts')->whereIn('product_id', array_column($update_ar, 'product_id'))->delete();
            }

            \DB::table('carts')->whereId($id)->delete();

            \DB::commit();
            $cart_items = \DB::table('carts')->whereUserId($user_id)->get();

            $applied_coupons_names = [];
            $cartValueAndShippingDiscountresult = null;
            if (count($cart_items->toArray()) > 0) {
                checkAppliedCouponsValidity($cart_items);
                $cart_session_id = $cart_items[0]->cart_session_id;
                $p = applyAppliedCouponsTableToCart($cart_items, $cart_session_id, $user_id, false);
                $cartValueAndShippingDiscountresult = $p['cartValueAndrShippingDiscountresult'];
                $applied_coupons_names = $p['applied_coupons_names'];
            } else {
                \DB::table('applied_coupons')->whereUserId($user_id)->delete();
            }

            //  dd(cart_update_arr);

            $minimu_cart_offer = count($cart_items->toArray()) > 0 ? getOnylCartMinimumAmountOffers($cart_items, $user_id) : [];
            $eligible_offer = count($cart_items->toArray()) > 0 ? getEligibleOffers($cart_items, $user_id) : [];
            $applied_coupons_names = remove_duplicate_coupon_names($applied_coupons_names);
            $cart_details = $this->cartService->returnCart($cart_items);
            $setting = \DB::table('settings')->first();
            $shipping_charge = $setting != null ? $setting->delivery_charge : 0;
            return response()->json(['success' => true,

                'cart_discount' => $cartValueAndShippingDiscountresult ? $cartValueAndShippingDiscountresult['cart_amount_discount'] : 0.0,
                'shipping_discount' => $cartValueAndShippingDiscountresult ? $cartValueAndShippingDiscountresult['shipping_discount'] : 0.0,
                'applicable_offers' => !empty($eligible_offer) ? remove_duplicate_coupon_names1($eligible_offer) : null,
                'minimum_cart_amount_offer' => !empty($minimu_cart_offer) ? $minimu_cart_offer[0] : null,
                'applied_coupons' => !empty($applied_coupons_names) ? $applied_coupons_names : null,
                'cart_items' => $cart_details['cart_items'],
                'total' => $cart_details['total'],
                'count' => count($cart_details['cart_items']),
                'sub_total' => $cart_details['sub_total'],
                'total_discount' => $cart_details['discount'],

                'cart_session_id' => $cart_session_id,
                'shipping_charge' => $shipping_charge,

            ], 201);

        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();
            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '==Line==' . $ex->getLine() . '==File==' . $ex->getFile(),
                'created_at' => Carbon::now(),
                'which_function' => 'Cart Delete  function',
            ]);
            return response()->json(['success' => false,
                'message' => 'Some Error occurred,Please try later ',

            ], 400);
        }
    }
    public function clear_cart(Request $r)
    {
        $cart_session_id = session('cart_session_id');
        \DB::table('carts')->whereCartSessionId($cart_session_id)->delete();
        \DB::table('applied_coupons')->whereCartSessionId($cart_session_id)->delete();
        return response()->json(['success' => true,
            'message' => 'Cart Cleared Successfully ',

        ], 400);
    }
}
