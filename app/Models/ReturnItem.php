<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class ReturnItem extends Model
{
    use HasFactory;
    protected $table='return_items';
    public $timestamps=0;
     public function getFillable(){
        return  $this->getTableColumns();
     }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    
    public function order_item():BelongsTo
    {
      return $this->belongsTo(OrderItem::class,'order_item_id','id');
    } 
    public function order():BelongsTo
    {
      return $this->belongsTo(Order::class,'order_id','id');
    } 
  
public function exchange_variant():BelongsTo
{
  return $this->belongsTo(ProductVariant::class,'exchange_variant_id','id');
} 
  
public function return_shipment():BelongsTo
{
  return $this->belongsTo(ReturnShipment::class,'return_shipment_id','id');
} 
 

 
	public function user():BelongsTo
{
  return $this->belongsTo(User::class,'user_id','id')->withDefault()->withTrashed();
} 
 }