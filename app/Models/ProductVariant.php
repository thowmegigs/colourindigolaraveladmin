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
class ProductVariant extends Model
{
    use HasFactory,Searchable;
    protected $table='product_variants';
    public $timestamps=0;
    protected static function booted(): void
    {
        static::creating(function ($variant) {
            $variant->sku = $variant->sku?$variant->sku:self::generateSku($variant);;
        });
        
    }

    public static function generateSku($variant)
    {
        $product = $variant->product;

        $count = self::where('product_id', $variant->product_id)->count() + 1;
        $increment = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "{$product->sku}-{$variant->name}-V{$increment}";
    }
     public function getFillable(){
        return  $this->getTableColumns();
     }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function images():HasMany
    {
    return $this->hasMany(ProductVariantImage::class,'product_variant_id','id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function toSearchableArray()
      {
          $product = $this->product;
        //   $images = $this->images->map(function ($image) {
        //               return asset("storage/products/{$product->id}/variants/" . $image->image);
        //           })->toArray();
        $facetAttributes = json_decode($product->facet_attributes?->attributes_json ?? '{}', true);
        $main_atribute=$this->attributes_json;
        $main_atribute=$main_atribute?json_decode($main_atribute,true):null;
        $data = [
             'objectID'   => "p-{$this->id}",
              'id' => $this->id,
               'product_id' => $product->id,
                'name'       => $product->name,
                 'vendor' => optional($product->vendor)->name,
                'category'   => optional($product->category)->slug,
                'brand'      => optional($product->brand)->name ?? null,
                'size'       =>$main_atribute?(isset($main_atribute['Size'])?$main_atribute['Size']:null):null,
                'color'       =>$main_atribute?(isset($main_atribute['Color'])?$main_atribute['Color']:null):null,
                'price'      => $this->price,
                'sale_price'      => $this->sale_price,
                'quantity'      => $this->quantity,
                'image'=>$this->image,
                'images' => $this->images,
             
          ];
          return array_merge($data, $facetAttributes);
          
    }
  
          public function shouldBeSearchable()
      {
            $this->loadMissing('product');
          // Only index products that are active/published (adjust based on your logic)
          return $this->product->deleted_at === null && $this->product && $this->product->status === 'Active' && count($this->toSearchableArray()) > 0 && $this->product->has_variant === 'Yes';
      }
 

  
 }