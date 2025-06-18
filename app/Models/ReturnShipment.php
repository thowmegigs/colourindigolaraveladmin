<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class ReturnShipment extends Model
{
    use HasFactory;
    protected $table='return_shipments';
    public $timestamps=0;
   
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    
    public function vendor_order():BelongsTo
    {
      return $this->belongsTo(VendorOrder::class,'vendor_order_id','id');
    } 
    public function vendor():BelongsTo
    {
      return $this->belongsTo(Vendor::class,'vendor_id','id');
    } 
    public function return_items():HasMany
    {
      return $this->HasMany(ReturnItem::class,'return_shipment_id','id');
    } 
 
 }