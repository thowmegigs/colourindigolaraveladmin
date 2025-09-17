<?php 
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
class ProductTemplateExport implements FromArray, WithHeadings,WithColumnWidths
{
    public function headings(): array
    {
        return [
            'name', 'description', 'category','sku', 'price', 'sale_price', 'quantity',
            'has_variant',
            'sizes'];
    }

    public function array(): array
    {
        return [
            [
                'Cotton Trouser', 'High-quality cotton', 'Men Casual Trousers',  'TROUSER-001', '499','300', '5', 
                'Yes',"X,S,M"
            ]
          
        ];
    }
public function columnWidths(): array
    {
        return [
            'A' => 50, // name
            'B' => 70, // description
            'C' => 15, // category
            'D' => 20, // brand
            'E' => 15, // sku
            'F' => 10, // price
            'G' => 12, // sale_price
            'H' => 10, // quantity
            'I' => 12, // has_variant
            'J' => 20, // color
            'K' => 15, // size
            'L' => 10, // width
            'M' => 25, // length
            'N' => 40, // height
            'O' => 60, // height
            'P' => 60, // height
           
        ];
    }   
    
}
