<?php 
namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductDiscountImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {
            $name     = trim($row[0]);
            $category = trim($row[1]);
            $brand    = trim($row[2]);
            $sku      = trim($row[3]);
            $discount = (float) $row[4];

            $categoryId = Category::where('name', $category)->value('id');
            $brandId    = Brand::where('name', $brand)->value('id');

            $product = Product::where([
                ['name', $name],
                ['sku', $sku],
                ['category_id', $categoryId],
                ['brand_id', $brandId],
            ])->first();

            if ($product) {
                $product->discount = $discount;
                $product->save();
            }
        }
    }
}
