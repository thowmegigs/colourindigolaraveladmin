<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class FacetAttribute extends Model
{
    use HasFactory;
   
     public $timestamps=0;
     public function getFillable(){
        return  $this->getTableColumns();
     }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
     public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id', 'id');
    }
     public function attribute_values()
    {
        return $this->HasOne(\App\Models\FacetAttributesValue::class, 'facet_attribute_id', 'id');
    }
    protected static function booted(): void
    {
       
         static::created(function () {
            self::updateAlgoliaFacets();
        });
         static::updated(function ($attribute) {
            $targetId=$attribute->id;
            $products = \App\Models\ProductFacetAttributeValue::whereRaw("JSON_SEARCH(attributes_json, 'one', ?, NULL, '$.*.id') IS NOT NULL", [$attribute->id])->get();
            foreach ($products as $product) {
                        $attributes = $product->attributes_json;  // This should be casted to array or decoded

                        // If it's a JSON string, decode it first:
                        if (is_string($attributes)) {
                            $attributes = json_decode($attributes, true);
                        }

                        // Loop through each attribute and update the name where id matches
                        foreach ($attributes as &$attr) {
                            if (isset($attr['id']) && $attr['id'] == $targetId) {
                                $attr['name'] = $newName;
                            }
                        }

                        // Save updated attributes back to the model
                        $product->attributes_json = json_encode($attributes);
                        $product->save();
                    }
           
           
           
           
            self::updateAlgoliaFacets();
        });
        static::deleted(function ($attribute) {
          \App\Models\FacetAttributesValue::where('facet_attribute_id',$attribute->id)->delete();
        });
    }
  public static function updateAlgoliaFacets()
{
    $facetKeys = self::pluck('name')->unique()->values()->all();

    $client = \Algolia\AlgoliaSearch\SearchClient::create(
        config('scout.algolia.id'),
        config('scout.algolia.secret')
    );

    $index = $client->initIndex('products');

    $index->setSettings([
        'attributesForFaceting' => $facetKeys,
    ]);
}
   
  
}