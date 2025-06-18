<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;          // make sure this exists
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class ProductImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row): void
    {
        $index = $row->getIndex();           // Excel row number
        $data  = $row->toArray();            // Row as associative array

        /* -----------------------------------------------------------------
         | 1.  Basic look-ups and guards
         * -----------------------------------------------------------------*/
        $category = Category::where('name', $data['category'] ?? null)->first();
        if (! $category) {
            throw ValidationException::withMessages([
                'category' => "Row {$index}: Category '{$data['category']}' not found.",
            ]);
        }

        $vendorId = auth()->guard('vendor')->id();
        if (! $vendorId) {
            throw ValidationException::withMessages([
                'login' => "Row {$index}: Vendor not authenticated.",
            ]);
        }
 
        /* -----------------------------------------------------------------
         | 2.  Upsert the parent product
         * -----------------------------------------------------------------*/
        $dimension = [
            'width'  => $data['width']  ?? null,
            'height' => $data['height'] ?? null,
            'length' => $data['length'] ?? null,
            'weight' => $data['weight'] ?? null,
        ];
        $has_dim=$data['length'] && $data['width'] && $data['height'] && $data['width']  && $data['weight'];
 if (!$has_dim) {
            throw ValidationException::withMessages([
                'login' => "Row {$index}: Product Dimesnions height,width,length,weight required",
            ]);
        }
        $product = Product::updateOrCreate(
            [
                'sku'       => $data['sku'],
                'vendor_id' => $vendorId,
            ],
            [
                'name'              => $data['name'],
                'description'       => $data['description'],
                'category_id'       => $category->id,
                'brand_id'          => $vendorId,
                'vendor_id'         => $vendorId,
                'sku'               => $data['sku'],
                'price'             => $data['price'],
                'sale_price'        => $data['sale_price'],
                'quantity'          => $data['quantity'],
                'discount'          => round(
                    ((float) $data['price'] - (float) $data['sale_price']) /
                    (float)  $data['price'] * 100,
                    2
                ),
                'discount_type'     => 'Percent',
                'has_variant'       => 'No',                    // will flip later
                'package_dimension' => json_encode($dimension),
                'status'            => 'In-Active',
                'meta_keywords'     => $data['meta_keywords']    ?? '',
                'meta_description'  => $data['meta_description'] ?? '',
            ]
        );

        /* -----------------------------------------------------------------
         | 3.  Parse comma-separated sizes + SKUs
         * -----------------------------------------------------------------*/
        $sizes    = isset($data['sizes'])
            ? array_filter(array_map('trim', explode(',', $data['sizes'])))
            : [];

        $sizeSkus = isset($data['size_skus'])
            ? array_filter(array_map('trim', explode(',', $data['size_skus'])))
            : [];

        if ($sizes && count($sizes) !== count($sizeSkus)) {
            throw ValidationException::withMessages([
                'sizes' => "Row {$index}: Number of sizes and size SKUs must match.",
            ]);
        }

        /* -----------------------------------------------------------------
         | 4.  Create / update variants
         * -----------------------------------------------------------------*/
        foreach ($sizes as $i => $size) {
            $variantSku = $sizeSkus[$i] ?? null;

            if (! $variantSku) {
                throw ValidationException::withMessages([
                    'size_skus' => "Row {$index}: Missing SKU for size '{$size}'.",
                ]);
            }

            ProductVariant::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'name'       => $size,                 // unique key can be (product_id, name)
                ],
                [
                    'sku'             => $variantSku,
                    'name'            => $size,            // e.g. "M"
                    'price'           => $product->price,
                    'sale_price'      => $product->sale_price,
                    'quantity'        => $product->quantity,
                    'attributes_json' => json_encode(['Size' => $size]),
                ]
            );
        }

        /* -----------------------------------------------------------------
         | 5.  Flip parent product’s variant flag
         * -----------------------------------------------------------------*/
        if ($sizes) {
            $product->update(['has_variant' => 'Yes']);
        }
    }
}
