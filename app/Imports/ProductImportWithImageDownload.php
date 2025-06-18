<?php 
namespace App\Imports;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantImage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImportWithImageDownload implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->shift(); // skip heading

        foreach ($rows as $row) {
            $categoryId = \App\Models\Category::where('name', $row[2])->value('id');
             $brandId = \App\Models\Brand::where('name', $row[3])->value('id');
            $product = Product::create([
                'name' => $row[0],
                'description' => $row[1],
                'category_id' =>$categoryId,
                'brand_id' => $brandId,
                'sku' => $row[4],
                'price' => $row[5],
                'sale_price' => $row[6],
                'quantity' => $row[7],
                'discount' => round((float)$row[5] - ((float)$row[6] / 100) * 100, 2),
                'discount_type'=>'Percent',
                'has_variant' => strtolower($row[10]) === 'yes' ? 1 : 0,
                'tags' => $row[17],
                'width' => $row[19],
                'length' => $row[20],
                'height' => $row[21],
                'weight' => $row[22],
                'meta_title' => $row[23],
                'meta_keywords' => $row[24],
                'meta_description' => $row[25],
               
            ]);
            $product->image =$this->downloadImage($row[8], "products/{$product->id}");
            $product->size_chart_image = $this->downloadImage($row[18], "products/{$product->id}");
            $product->save();
            if (!empty($row[9])) {
                foreach (explode(',', $row[9]) as $imageUrl) {
                   // $imagename=$this->downloadImage(trim($imageUrl), "products/{$product->id}");
                    
                    ProductImage::create([
                        'product_id' => $product->id,
                        'name' => $this->downloadImage(trim($imageUrl), "products/{$product->id}"),
                    ]);
                }
            }

            // Handle variants
            if (strtolower($row[10]) === 'yes') {
                $variant = ProductVariant::create([
                    'name'=>$row[12].'-'.$row[13],
                    'product_id' => $product->id,
                     'price' => $row[15],
                    'sale_price' => $row[26],
                     'discount' =>3,
                    'discount_type'=>'Percent',
                    'sku' => $row[13],
                    
                    'quantity' => $row[7],
                    'image' => $this->downloadImage($row[8], "products/{$product->id}/variants"), // reuse product image or separate column if needed
                ]);

                // Copy variant images (reuse product images column or add a new one)
                if (!empty($row[9])) {
                    foreach (explode(',', $row[9]) as $imageUrl) {
                        ProductVariantImage::create([
                            'product_variant_id' => $variant->id,
                            'name' => $this->downloadImage(trim($imageUrl), "products/{$product->id}/variants/{$variant->id}"),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Download and store an image from URL to the given folder.
     */
    private function downloadImage($url, $folder)
    {
        if (!$url) return null;

        try {
            $contents = Http::withOptions(['verify' => false])->timeout(10)->get($url)->body();
           
            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $fileName = uniqid() . '.' . $ext;
            $path = "$folder/$fileName";

            Storage::disk('public')->put($path, $contents);
            return $fileName ; // Return public path
        } catch (\Exception $e) { \Sentry\captureException($e);
            dd($e->getMessage());
            return null;
        }
    }
}
