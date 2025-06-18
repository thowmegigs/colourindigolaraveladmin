<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class Address extends Model
{
    use HasFactory;
    protected $table='addresses';
    public $timestamps=0;
     public function getFillable(){
        return  $this->getTableColumns();
     }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
     public function state()
    {
        return $this->belongsTo(\App\Models\State::class, 'state_id', 'id')->withDefault();
    }
    public function city()
    {
        return $this->belongsTo(\App\Models\City::class, 'city_id', 'id')->withDefault();
    }
   
    
   
  
}