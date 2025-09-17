<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class Collection extends Model
{
    use HasFactory;
    protected $table='collections';
    public $timestamps=0;
    
     public function getFillable(){
        return  $this->getTableColumns();
     }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
  protected static function booted(): void
    {
       

        static::deleted(function ($collection) {
            $collectionId = $collection->id;

          
            $tables = ['website_content_sections', 'content_sections'];

            foreach ($tables as $table) {
                DB::table($table)
                    ->whereJsonContains('collection_ids', $collectionId)
                    ->get()
                    ->each(function ($record) use ($collectionId, $table) {
                        // Remove product ID from product_ids JSON
                        $ids = json_decode($record->collection_ids, true);
                        $ids = array_filter($ids, fn($id) => $id != $collectionId);

                        if (empty($ids)) {
                            DB::table($table)->where('id', $record->id)->update(['deleted_at' => now()]);
                        } else {
                            DB::table($table)->where('id', $record->id)->update([
                                'collection_ids' => json_encode(array_values($ids))
                            ]);
                        }

                        // Remove from products1 if exists
                        if ($record->collections1) {
                            $items = json_decode($record->collections1, true);
                            $items = array_filter($items, fn($item) => $item['id'] != $collectionId);

                            if (empty($items)) {
                                DB::table($table)->where('id', $record->id)->update(['deleted_at' => now()]);
                            } else {
                                DB::table($table)->where('id', $record->id)->update([
                                    'collections1' => json_encode(array_values($items))
                                ]);
                            }
                        }

                       
                    });
            }
        });
    }
  public function setNameAttribute($value)
  {
      $this->attributes['name'] = $value;
      $this->attributes['slug'] = \Str::slug($value);
  }
  
  
}