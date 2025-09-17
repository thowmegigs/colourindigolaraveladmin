<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
class Category extends Model
{
    use HasFactory,Searchable;
    protected $table='categories';
    public $timestamps=0;
     public function getFillable(){ 
        return  $this->getTableColumns();
     }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
     public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'path' => $this->full_path, // add this line
        ];
    }
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \Str::slug($value);
    }
     public function getAllChildIds()
    {
        $ids = $this->children()->pluck('id')->toArray(); // Get direct child IDs

        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllChildIds()); // Recursively get child IDs
        }

        return $ids;
    }
    public function category():BelongsTo
    {
    return $this->belongsTo(Category::class,'category_id','id');
    }
   
    public function children():HasMany
    {
    return $this->hasMany(Category::class,'category_id','id');
    }
    public function products():HasMany
    {
    return $this->hasMany(Product::class,'category_id','id');
    }
    public function parent()
{
    return $this->belongsTo(Category::class, 'category_id');
}
    public function getFullPathAttribute()
        {
            $names = [];
            $category = $this;

            while ($category) {
                array_unshift($names, $category->name);
                $category = $category->parent;
            }

            return implode(' > ', $names);
        }
   
  
}