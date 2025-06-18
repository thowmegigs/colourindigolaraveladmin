<?php 
namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    public function saved(Category $category)
    {
        // Index the current category
        $category->searchable();

        // Re-index all children (path might have changed)
        foreach ($category->children as $child) {
            $child->searchable();
        }
    }

    public function updated(Category $category)
    {
        // Optional: already handled by saved(), but include if custom logic needed
        $this->saved($category);
    }

    public function deleted(Category $category)
    {
        // Remove the deleted category from the index
        $category->unsearchable();

        // Optionally, also remove or re-index children
        foreach ($category->children as $child) {
            $child->unsearchable();  // Remove from Algolia
            $child->delete();        // Delete from DB (triggers observer recursively)
        }
          $categoryId = $category->id;

            $product->variants()->delete();
            $product->reviews()->delete();

            $tables = ['website_content_sections', 'content_sections'];

            foreach ($tables as $table) {
                DB::table($table)
                    ->whereJsonContains('category_ids', $categoryId)
                    ->get()
                    ->each(function ($record) use ($categoryId, $table) {
                        // Remove product ID from product_ids JSON
                        $ids = json_decode($record->category_ids, true);
                        $ids = array_filter($ids, fn($id) => $id != $categoryId);

                        if (empty($ids)) {
                            DB::table($table)->where('id', $record->id)->update(['deleted_at' => now()]);
                        } else {
                            DB::table($table)->where('id', $record->id)->update([
                                'category_ids' => json_encode(array_values($ids))
                            ]);
                        }

                        // Remove from products1 if exists
                        if ($record->categories1) {
                            $items = json_decode($record->categories1, true);
                            $items = array_filter($items, fn($item) => $item['id'] != $categoryId);

                            if (empty($items)) {
                                DB::table($table)->where('id', $record->id)->update(['deleted_at' => now()]);
                            } else {
                                DB::table($table)->where('id', $record->id)->update([
                                    'categories1' => json_encode(array_values($items))
                                ]);
                            }
                        }

                        // Remove from collection_products_when_single_collection_set if exists
                       
                    });
            }

            DB::table('collections')
                        ->whereJsonContains('category_id', $categoryId)
                        ->get()
                        ->each(function ($record) use ($categoryId) {
                
                  
                                $ids = json_decode($record->category_id, true);
                                $ids = array_filter($ids, fn($id) => $id != $categoryId);

                                if (empty($ids)) {
                                    DB::table('collections')->where('id', $record->id)->update(['deleted_at' => now()]);
                                } else {
                                    DB::table($table)->where('id', $record->id)->update([
                                        'category_id' => json_encode(array_values($ids))
                                    ]);
                                }
                            
                
            });
    }

    // Optional: for force delete
    public function forceDeleted(Category $category)
    {
        $this->deleted($category);
    }
}
