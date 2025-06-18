<?php

 namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class CategoryExport implements FromCollection, WithHeadings, WithColumnWidths
{
      public function collection()
    {
        return Category::select('name')->doesntHave('children')->whereStatus('Active')->whereNull('deleted_at')->get();
    }
    public function headings(): array
    {
        return ['Name'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // Set width of column A (Category Name) to 30
        ];
    }
}
