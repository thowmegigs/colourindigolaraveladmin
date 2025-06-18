<?php 
namespace App\Imports;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Row;

class ProductVariantImport implements OnEachRow, WithHeadingRow, SkipsEmptyRows
{
    public function onRow(Row $row)
    {
      \DB::transaction(function () use ($row) {
        $index = $row->getIndex(); // Excel row number (starts from 2 due to heading)
        $data = $row->toArray();

        // Check if product_id exists
        $par= $data['parent_sku'] ?? null;
        if(!$data['parent_sku']){
              throw ValidationException::withMessages([
                'row' => "Row sku  not present.",
            ]);
        }
       $product=Product::where('sku',$data['parent_sku'])->first();
      
        if (!$product) {
            throw ValidationException::withMessages([
                'row' => "Row {$index}: Parent SKU {$data['parent_sku']} not found.",
            ]);
        }
        
    $name='';
    $atribute_json=null;
      $color = trim((string) ($data['color'] ?? ''));
        $size  = trim((string) ($data['size']  ?? ''));
    if($color && $size){
        $name=$color.'-'.$size;
        $atribute_json=json_encode(['Color'=>$color,'Size'=>$size]);
    }
    else if($color && !$size){
        $name=$color;
         $atribute_json=json_encode(['Color'=>$color]);
    }
    if($size && !$color){
        $name=$size;
         $atribute_json=json_encode(['Size'=>$size]);
    }
        // Create or update the variant
        ProductVariant::updateOrCreate(
            [
                'product_id' => $product->id,
                
                 'sku' => $data['sku']
               
            ],
            [
                'name' => $name,
                'price' => $data['price'] ?? 0,
                'sku' => $data['sku'] ?? 0,
                'sale_price' => $data['sale_price'] ?? 0,
                'sale_price' => $data['sale_price'] ?? 0,
                'quantity' => $data['quantity'] ?? 0,
                'atributes_json'=>$atribute_json
            ]
        );
         $this->updateParentAttributes($product, $color, $size);
    });
    
    }
      protected function updateParentAttributes($product, ?string $color, ?string $size): void
    {
        // Decode or initialise
        $attrs       = $product->attributes           ? json_decode($product->attributes, true)           : [];
        $searchables = $product->searchable_attributes ? json_decode($product->searchable_attributes, true) : [];

        /* ----------  COLOR ---------- */
        if ($color) {
            // attributes → Color as ARRAY
            $colorBlock = collect($attrs)->firstWhere('name', 'Color');

            if ($colorBlock) {
                $values = is_array($colorBlock['value'])
                    ? $colorBlock['value']
                    : explode(',', (string) $colorBlock['value']);

                if (! in_array($color, $values, true)) {
                    $values[] = $color;
                }

                // replace in original array
                foreach ($attrs as &$attr) {
                    if ($attr['name'] === 'Color') {
                        $attr['value'] = array_values(array_unique($values));
                        break;
                    }
                }
            } else {
                // id 1 is assumed for “Color”; adjust if different in your DB
                $attrs[] = ['id' => 1, 'name' => 'Color', 'value' => [$color]];
            }

            // searchable_attributes → always array
            $searchables['Color'] = array_values(array_unique(
                array_merge($searchables['Color'] ?? [], [$color])
            ));
        }

        /* ----------  SIZE ---------- */
        if ($size) {
            // attributes → Size as COMMA-string
            $sizeBlock = collect($attrs)->firstWhere('name', 'Size');
            if ($sizeBlock) {
                $values = explode(',', (string) $sizeBlock['value']);
                if (! in_array($size, $values, true)) {
                    $values[] = $size;
                }
                foreach ($attrs as &$attr) {
                    if ($attr['name'] === 'Size') {
                        $attr['value'] = implode(',', array_unique($values));
                        break;
                    }
                }
            } else {
                // id 3 is assumed for “Size”; adjust if different
                $attrs[] = ['id' => 3, 'name' => 'Size', 'value' => $size];
            }

            // searchable_attributes
            $searchables['Size'] = array_values(array_unique(
                array_merge($searchables['Size'] ?? [], [$size])
            ));
        }

        /* ----------  Write back ---------- */
        $product->update([
            'attributes'            => json_encode($attrs, JSON_UNESCAPED_UNICODE),
            'searchable_attributes' => json_encode($searchables, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
