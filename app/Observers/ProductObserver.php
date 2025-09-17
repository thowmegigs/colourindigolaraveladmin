<?php

namespace App\Observers;

use App\Models\Product;
namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function created(Product $product)
    {
        $product->searchable();

     
    }

    public function updated(Product $product)
    {
        $product->searchable();

       
    }

    public function deleted(Product $product)
    {
        $product->unsearchable();

        if ($product->has_variant=='Yes') {
            $product->variants->each->unsearchable();
        }
          $productId = $product->id;
            $product->reviews()->delete();

            $tables = ['website_content_sections', 'content_sections'];

            foreach ($tables as $table) {
                \DB::table($table)
                    ->whereJsonContains('product_ids', $productId)
                    ->get()
                    ->each(function ($record) use ($productId, $table) {
                        // Remove product ID from product_ids JSON
                        $ids = json_decode($record->product_ids, true);
                        $ids = array_filter($ids, fn($id) => $id != $productId);

                        if (empty($ids)) {
                            DB::table($table)->where('id', $record->id)->delete();
                        } else {
                            DB::table($table)->where('id', $record->id)->update([
                                'product_ids' => json_encode(array_values($ids))
                            ]);
                        }

                        // Remove from products1 if exists
                        if ($record->products1) {
                            $items = json_decode($record->products1, true);
                            $items = array_filter($items, fn($item) => $item['id'] != $productId);

                            if (empty($items)) {
                                DB::table($table)->where('id', $record->id)->delete();
                            } else {
                                DB::table($table)->where('id', $record->id)->update([
                                    'products1' => json_encode(array_values($items))
                                ]);
                            }
                        }

                        // Remove from collection_products_when_single_collection_set if exists
                        if ($record->collection_products_when_single_collection_set) {
                            $items = json_decode($record->collection_products_when_single_collection_set, true);
                            $items = array_filter($items, fn($item) => $item['id'] != $productId);

                            if (empty($items)) {
                                DB::table($table)->where('id', $record->id)->update(['deleted_at' => now()]);
                            } else {
                                DB::table($table)->where('id', $record->id)->update([
                                    'collection_products_when_single_collection_set' => json_encode(array_values($items))
                                ]);
                            }
                        }
                    });
            }

            
    }
}