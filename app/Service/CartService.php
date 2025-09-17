<?php

namespace App\Service;

class CartService
{
    public function returnCart($cart_items): array
    {
        // get data from session (this equals Session::get(), use empty array as default)
        $shoppingCart = [];
        $total_discount=0;
        $total=0;
        $sub_total=0;
        foreach ($cart_items as $item) {
            $variantId = $item->variant_id == null ? '' : $item->variant_id;
            $unique_cart_item_id = $item->product_id . $item->variant_id;
            $unique_cart_item_id = trim($unique_cart_item_id);
            $discount = ($item->price - $item->sale_price) * $item->qty;
            $total_discount+=$discount;
           
            $additional_amount = 0;
            if ($item->addon_items != null) {
                $items = json_decode($item->addon_items, true);
                foreach ($items as $g) {
                    $additional_amount += $g['qty'] * $g['price'];
                }
            }
            if ($item->addon_products != null) {
                $items = json_decode($item->addon_products, true);
                foreach ($items as $g) {
                    $additional_amount += $g['qty'] * $g['amount'];
                }
            }
            $sub_total+=$item->net_cart_amount;
            $total+=($item->price + $additional_amount)*$item->qty;
            $g = [
                'id'=>$item->id,
                'variant_id' => $item->variant_id,
                'product_id' => $item->product_id,
                'variant_name' => $item->variant_name,
                'qty' => $item->qty,
                'cart_session_id' => $item->cart_session_id,
                'discount' => $discount,
                'price' => $item->price,
                'sale_price' => $item->sale_price,
                'net_amount' => $item->net_cart_amount,
                'name' => $item->name, 'tax' => $item->total_tax,
                'image' => $item->image,
                'addon_items' => $item->addon_items,
                'addon_products' => $item->addon_products,
                'is_combo_offer' => $item->is_combo,
                'product_discount_offer_text' => $item->product_discount_offer_detail,

            ];

            $shoppingCart[] = $g;

        }
        return ['cart_items'=>$shoppingCart,'total'=>$total,'sub_total'=>$sub_total,'discount'=>$total_discount];
    }

}
