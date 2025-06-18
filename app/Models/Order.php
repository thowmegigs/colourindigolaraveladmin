<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'orders';
    public $timestamps = 0;
    public function getFillable()
    {
        return $this->getTableColumns();
    }
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function items(): HasMany
    {
      return  $this->hasMany(OrderItem::class, 'order_id', 'id');
       
    }
    public function return_items(): HasMany
    {
      return $this->hasMany(OrderItem::class, 'order_id', 'id')->where('returned_qty', '>', 0);
       
    }
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id', 'id')->withDefault()->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function shipping_address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id', 'id');
    }
    public function billing_address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id', 'id');
    }
   
    public function applied_coupons(): HasMany
    {
        return $this->hasMany(AppliedCoupon::class, 'cart_session_id', 'cart_session_id');
    }
     protected static function booted()
    {
        static::deleting(function ($order) {
            // Delete related comments
            $post->return_items()->delete();
            $post->items()->delete();
            \DB::table('order_shipments')->where('order_id',$order->id)->delete();
            \DB::table('vendor_orders')->where('order_id',$order->id)->delete();

            // Or a direct query if you don't have a relation:
            // Comment::where('post_id', $post->id)->delete();
        });
    }

    // Defin
}
