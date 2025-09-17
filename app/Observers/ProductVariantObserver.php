<?php

namespace App\Observers;


use App\Models\ProductVariant;

class ProductVariantObserver
{
    public function created(ProductVariant $variant)
    {
        $variant->searchable();

       
    }

    public function updated(ProductVariant $variant)
    {
        $variant->searchable();

       
    }

    public function deleted(ProductVariant $variant)
    {
        $variant->unsearchable();

        
    }
}
