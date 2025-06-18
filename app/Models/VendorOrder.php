<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class VendorOrder  extends Model 
{
    
    protected $table='vendor_orders';
    public $timestamps=0;
    public function getFillable(){
        return  $this->getTableColumns();
     }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    
 public function  vendor(){
    return $this->belongsTo(Vendor::class);
 }
  
 public function  order(){
    return $this->belongsTo(Order::class);
 }
 public function  shipping_status_updates(){
    return $this->hasMany(VendorOrderStatusUpdate::class);
 }
 public function getOrderItemsAttribute()
{
    return \App\Models\OrderItem::with(['product', 'variant'])->where('order_id', $this->order_id)
        ->where('vendor_id', $this->vendor_id)
        ->get();
}
    public function items()
{
    $vendorId = \Auth::guard('vendor')->id();
    $vendorId = $vendorId?$vendorId:3;
   
    return $this->order->items()->where('vendor_id', $vendorId);
}
   
  
}