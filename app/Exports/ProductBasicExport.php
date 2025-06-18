<?php 
namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductBasicExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with(['category', 'brand'])->get();
    }

    public function headings(): array
    {
        return ['Name', 'Category', 'Brand', 'SKU'];
    }

    public function map($product): array
    {
        return [
            $product->name,
            optional($product->category)->name,
            optional($product->brand)->name,
            $product->sku,
        ];
    }
}
