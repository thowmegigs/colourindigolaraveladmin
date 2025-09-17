<?php 
namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ProductExport implements FromArray, WithHeadings, WithColumnWidths
{
    public function array(): array
    {
        $products = Product::with(['category', 'vendor'])->get();
        $data = [];

        foreach ($products as $product) {
            if ($product->has_variant == 'Yes' && $product->variants->count()) {
                foreach ($product->variants as $variant) {
                    $data[] = $this->mapProduct($product, $variant);
                }
            } else {
                $data[] = $this->mapProduct($product);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'name', 'description', 'category', 'brand', 'sku', 'price', 'sale_price', 'quantity',
            'image', 'images', 'has_variant', 'attributes', 'variant_size', 'variant_color',
            'variant_sku', 'variant_price', 'variant_stock', 'tags', 'size_chart_image',
            'width', 'length', 'height', 'weight',
            'meta_title', 'meta_keywords', 'meta_description','variant_sale_price'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, 'B' => 30, 'C' => 15, 'D' => 15, 'E' => 15,
            'F' => 10, 'G' => 10, 'H' => 10, 'I' => 30, 'J' => 40,
            'K' => 10, 'L' => 20, 'M' => 15, 'N' => 15, 'O' => 20,
            'P' => 10, 'Q' => 10, 'R' => 30, 'S' => 20,
            'T' => 8, 'U' => 8, 'V' => 8, 'W' => 8,
            'X' => 20, 'Y' => 20, 'Z' => 30
        ];
    }

    protected function mapProduct($product, $variant = null)
    {
        return [
            $product->name,
            $product->description,
            optional($product->category)->name,
            optional($product->brand)->name,
            $product->sku,
            $product->price,
            $product->sale_price,
            $product->quantity,
            '',
            '',
            $product->has_variant,
            $product->attributes,
            $variant->size ?? '',
            $variant->color ?? '',
            $variant->sku ?? '',
            $variant->price ?? '',
            $variant->stock ?? '',
            $product->tags,
            '',
            $product->package_dimension['width'] ?? '',
            $product->package_dimension['length'] ?? '',
            $product->package_dimension['height'] ?? '',
            $product->package_dimension['weight'] ?? '',
            $product->meta_title,
            $product->meta_keywords,
            $product->meta_description,$variant->sale_price
        ];
    }
}
