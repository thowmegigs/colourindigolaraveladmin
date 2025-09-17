<?php 
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
class ProductTemplateExportCopy implements FromArray, WithHeadings,WithColumnWidths
{
    public function headings(): array
    {
        return [
            'name', 'description', 'category', 'brand', 'sku', 'price', 'sale_price', 'quantity',
            'image', 'images','has_variant','attributes','variant_size', 'variant_color', 'variant_sku', 'variant_price', 'variant_stock',
            'tags','size_chart_image','width','length','height','weight','meta_title','meta_keywords',
            'meta_description','variant_sale_orice' ];
    }

    public function array(): array
    {
        return [
            [
                'Cotton T-Shirt', 'High-quality cotton', 'T-Shirts', 'MyBrand', 'TSHIRT-001', '499','300', '5', 
                'https://someurl.com/image.jpg',"https://someurl.com/image.jpg,https://someurl.com/image.jpg",
                'Yes',"size,color",'M', 'Red', 'TSHIRT-001-M-RED', '499', '50',
                'casual,summer,cotton','https://someurl.com/size_chart_image.jpg','22','45','44','77',"Casula Cotton ","",""
            ]
          
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A'  => 25, // name
            'B'  => 40, // description
            'C'  => 20, // category
            'D'  => 20, // brand
            'E'  => 20, // sku
            'F'  => 12, // price
            'G'  => 12, // sale_price
            'H'  => 10, // quantity
            'I'  => 30, // image
            'J'  => 40, // images
            'K'  => 12, // has_variant
            'L'  => 30, // attributes
            'M'  => 15, // variant_size
            'N'  => 15, // variant_color
            'O'  => 20, // variant_sku
            'P'  => 12, // variant_price
            'Q'  => 12, // variant_stock
            'R'  => 25, // tags
            'S'  => 30, // size_chart_image
            'T'  => 10, // width
            'U'  => 10, // length
            'V'  => 10, // height
            'W'  => 10, // weight
            'X'  => 30, // meta_title
            'Y'  => 25, // meta_keywords
            'Z'  => 40, // meta_description
        ];
    }
    
}
