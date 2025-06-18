<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use \Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Scout\Searchable;
class Vendor  extends Authenticatable 
{
    use HasFactory,Searchable, Notifiable, HasRoles;
    protected $table='vendors';
    public $timestamps=0;
    protected $fillable = [
        'name',
        'email', 'phone', 'state_id', 'city_id', 'pincode', 'address','address2','status',
        'password', 'plain_password','gst','pan',
        'email_verified','phone_verified'
    ];
      protected $hidden = [
        'password'
       
    ];
 public function setPasswordAttribute($value)
    {

        $this->attributes['password'] = Hash::make($value);
        $this->attributes['plain_password'] = $value;

    }
    public function state()
    {
        return $this->belongsTo(\App\Models\State::class, 'state_id', 'id')->withDefault();
    }
    public function city()
    {
        return $this->belongsTo(\App\Models\City::class, 'city_id', 'id')->withDefault();
    }
     public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \Str::slug($value);
    }
       public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            // add this line
        ];
    }

   
  
}