<?php

namespace App\Models;

 use  Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slider extends Model
{
    use HasFactory;
    protected $table='sliders';
    public $timestamps=0;
    protected $fillable = [
        'name',
        'images_meta',
      
    ];
    public function images():HasMany
    {
    return $this->hasMany(AppCarouselImage::class,'slider_id','id');
    }
   
  
}