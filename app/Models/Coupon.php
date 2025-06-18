<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    use HasFactory;
    protected $table='coupons';
    public $timestamps=0;
     public function getFillable(){
        return  $this->getTableColumns();
     }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
      protected static function booted(): void
    {
       

        static::deleted(function ($coupon) {
            $couponId = $coupon->id;

          
            $tables = ['website_content_sections', 'content_sections'];

            foreach ($tables as $table) {
                DB::table($table)
                    ->whereJsonContains('coupon_ids', $couponId)
                    ->get()
                    ->each(function ($record) use ($couponId, $table) {
                        // Remove product ID from product_ids JSON
                        $ids = json_decode($record->coupon_ids, true);
                        $ids = array_filter($ids, fn($id) => $id != $couponId);

                        if (empty($ids)) {
                            DB::table($table)->where('id', $record->id)->update(['deleted_at' => now()]);
                        } else {
                            DB::table($table)->where('id', $record->id)->update([
                                'coupon_ids' => json_encode(array_values($ids))
                            ]);
                        }

                        // Remove from products1 if exists
                        if ($record->coupons1) {
                            $items = json_decode($record->coupons1, true);
                            $items = array_filter($items, fn($item) => $item['id'] != $couponId);

                            if (empty($items)) {
                                DB::table($table)->where('id', $record->id)->update(['deleted_at' => now()]);
                            } else {
                                DB::table($table)->where('id', $record->id)->update([
                                    'coupons1' => json_encode(array_values($items))
                                ]);
                            }
                        }

                       
                    });
            }
        });
    }
   
  
}