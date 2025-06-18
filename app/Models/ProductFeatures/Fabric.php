<?php

namespace App\Models\ProductFeatures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class Fabric extends Model
{
    
     protected $table='fabrics';
     public $timestamps=0;
     public function getFillable(){
        return  $this->getTableColumns();
      }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    
   
  

 

 
	public function user():BelongsTo
{
  return $this->belongsTo(User::class,'user_id','id');
} 
 
 }