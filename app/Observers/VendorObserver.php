<?php 
namespace App\Observers;

use App\Models\Vendor;

class VendorObserver
{
    public function saved(Vendor $vendor)
    {
        // Index the current category
        $vendor->searchable();

    }

    public function updated(Vendor $vendor)
    {
        // Optional: already handled by saved(), but include if custom logic needed
        $this->saved($vendor);
    }

    public function deleted(Vendor $vendor)
    {
        // Remove the deleted category from the index
        $vendor->unsearchable();
  }

    // Optional: for force delete
    public function forceDeleted(Category $category)
    {
        $this->deleted($category);
    }
}
