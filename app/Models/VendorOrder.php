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

public function canBeCompleted(): bool
{
    $statuses = json_decode($this->delivery_status_updates, true);
    if (!$statuses || !is_array($statuses)) {
        return false;
    }

    $latestDeliveryDate = null;

    // Step 1: Get latest delivery/exchange completion date
    foreach ($statuses as $status) {
        if (!isset($status['status'])) {
            continue;
        }

        $currentStatus = strtoupper($status['status']);

        if (in_array($currentStatus, ['DELIVERED','EXCHANGE COMPLETED']) && !empty($status['date'])) {
            $date = Carbon::parse($status['date']);
            if (is_null($latestDeliveryDate) || $date->gt($latestDeliveryDate)) {
                $latestDeliveryDate = $date;
            }
        }
    }

    // If no delivery recorded yet
    if (!$latestDeliveryDate) {
        return false;
    }

    // Step 2: Check if any return/exchange is active from return_shipments
    $hasActiveReturnOrExchange = false;

    $returnShipments = \App\Models\ReturnShipment::where('vendor_order_id', $this->id)->get();

    foreach ($returnShipments as $return) {
        $returnUpdates = json_decode($return->return_status_updates, true);

        if (!$returnUpdates || !is_array($returnUpdates)) {
            continue;
        }

        foreach ($returnUpdates as $update) {
            if (!isset($update['status'])) {
                continue;
            }

            $returnStatus = strtoupper($update['status']);

            if (
                Str::contains($returnStatus, 'RETURN') ||
                Str::contains($returnStatus, 'EXCHANGE')
            ) {
                if (
                    Str::contains($returnStatus, 'INITIATED') ||
                    Str::contains($returnStatus, 'PENDING') ||
                    Str::contains($returnStatus, 'PROCESSING')
                ) {
                    $hasActiveReturnOrExchange = true;
                    break 2; // No need to check further
                }
            }
        }
    }

    if ($hasActiveReturnOrExchange) {
        return false;
    }
   $return_window_days=\App\Models\Setting::first()->return_days;
   $returnWindowDays = $return_window_days ?? 3;
   $completionDeadline = $latestDeliveryDate->copy()->addDays($returnWindowDays);

    return now()->greaterThan($completionDeadline);
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