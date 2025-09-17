<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Log;
class Product extends Model
{
    use HasFactory, Searchable;

    protected $table = 'products';
    public $timestamps = false;

    public function getFillable()
    {
        return $this->getTableColumns();
    }

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
         if (!array_key_exists('slug', $this->attributes) || empty($this->attributes['slug'])) {
            $this->attributes['slug'] = \Str::slug($value) .'-'.\Str::lower(\Str::random(6));
        }
        $this->attributes['uuid'] =  intval(microtime(true));
    }

    // protected static function booted(): void
    // {
    //     static::creating(function ($product) {
    //         $product->sku = $product->sku?$product->sku:self::generateSku($product);
    //     });

       
    // }

    // public static function generateSku($product)
    // {
    //     $categoryCode = strtoupper(substr($product->category->name ?? 'GEN', 0, 2));
    //     $brandCode = strtoupper(substr($product->brand->name ?? 'NB', 0, 2));
    //     $count = self::where('category_id', $product->category_id)->count() + 1;
    //     $increment = str_pad($count, 4, '0', STR_PAD_LEFT);

    //     return "S{$categoryCode}-{$brandCode}-{$increment}";
    // }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->withDefault();
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function product_images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function facet_attributes(): HasOne
    {
        return $this->hasOne(ProductFacetAttributeValue::class, 'product_id', 'id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function toSearchableArray()
    {
        
        $images = $this->images->map(function ($image) {
            return asset("storage/products/{$this->id}/" . $image->image);
        })->toArray();

        $facetAttributes = json_decode($this->facet_attributes?->attributes_json ?? '{}', true);
      

       $ar = [];
       if(count($facetAttributes)>0){
          foreach ($facetAttributes as $item) {
                $ar[$item['name']] = $item['value'];
            }
        }
        $data = [
            'objectID'           => "p-{$this->id}",
            'id'                 => $this->id,
            'name'               => $this->name,
            'slug'               => $this->slug,
            'sku'                => $this->sku,
            'description'        => $this->description,
            'vendor'             => $this->vendor?->name,
            'short_description'  => $this->short_description,
            'price'              => $this->price,
            'sale_price'         => $this->sale_price,
            'tags'               => $this->tags,
            'category'           => optional($this->category)->name,
            'brand'              => optional($this->vendor)->name,
            'image'              => asset("storage/products/{$this->id}/" . $this->image),
            'images'             => $images,
            'has_variant'        => $this->has_variant,
        ];
        $x=array_merge($data, $ar);
     
        return $x;
    }

    public function shouldBeSearchable()
    {
        return $this->deleted_at === null &&
               $this->status === 'Active' &&
               count($this->toSearchableArray()) > 0;
    }
}
