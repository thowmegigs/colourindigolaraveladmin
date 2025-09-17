<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ProductVariantTemplateExport implements FromArray, WithHeadings, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'parent_sku',  // Reference to parent product
            'sku',
             'price',
            'sale_price',
            'quantity',
            'color',
            'size'
        ];
    }

    public function array(): array
    {
        return [
            [
                "TROUSER-001",                // product_id
                'TROUSER-001-RED-L', // sku
                 '549',              // price
                '499',              // sale_price
                '10',               // quantity
                'Red',              // color
                'L',                // size
            ],
            [
                "TROUSER-001",
                'TROUSER-001-GREEN-M',
               '549',
                '499',
                '8',
                'Green',
                'M',
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, // product_id
            'B' => 20, // sku
            'C' => 25, // name
            'D' => 12, // price
            'E' => 12, // sale_price
            'F' => 10, // quantity
            'G' => 10, // color
          
        ];
    }
}
