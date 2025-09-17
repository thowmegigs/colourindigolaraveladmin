<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteProductFolders extends Command
{
    protected $signature = 'products:delete';

    protected $description = 'Delete folders in storage/app/products/ for soft-deleted products';

    public function handle(): void
    {
        $this->info("Fetching soft-deleted products...");

        // $productIds = DB::table('products')
        //     ->whereNotNull('deleted_at')
        //     ->pluck('id');

        // if ($productIds->isEmpty()) {
        //     $this->info("No soft-deleted products found.");
        //     return;
        // }
       $productIds=[24,25,26,27,28];
        foreach ($productIds as $id) {
            $folderPath = "products/{$id}";
//$this->info("Storage path: " . storage_path("app/{$folderPath}"));

            if (Storage::exists($folderPath)) {
                Storage::deleteDirectory($folderPath);
                $this->info("Deleted folder: {$folderPath}");
            } else {
                $this->warn("Folder not found: {$folderPath}");
            }
        }

        $this->info("Done.");
    }
}
