<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;
    protected $table='order_items';
    public $timestamps=0;
     public function getFillable(){
        return  $this->getTableColumns();
     }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function product(){
      return $this->belongsTo(Product::class,'product_id','id');
    }
    public function variant(){
      return $this->belongsTo(ProductVariant::class,'variant_id','id');
    }
    public function vendor(){
      return $this->belongsTo(Vendor::class,'vendor_id','id');
    }
   
  
 }