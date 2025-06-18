<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Imagick\Driver;
class GenerateThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-thumbnails';

    /**
     * The console command description.
     *
     * @var string
     */
     

    protected $description = 'Convert all images in a folder to tiny thumbnails and store them in a thumbnail subfolder';

     public function handle()
    {
        $sourcePath = storage_path('app/public/categories');
        $thumbWidth =150;
        $thumbHeight =150;

        if (!is_dir($sourcePath)) {
            $this->error("❌ Source directory does not exist: $sourcePath");
            return 1;
        }

        $destPath = $sourcePath . '/thumbnail';
        if (!File::exists($destPath)) {
            File::makeDirectory($destPath, 0755, true);
        }

        

        $manager = new ImageManager(Driver::class,
              autoOrientation: true);// or 'gd' // or 'gd'
        // $image = $manager->read($filerequest->getPathname());

        // // Convert & encode image to webp with quality 80
        // $webpImage = $image->toWebp(80);
        $converted = 0;
        $thumbed = 0;
        $images = File::files($sourcePath);
        foreach ($images as $image) {
            $ext = strtolower($image->getExtension());
            $originalName = $image->getFilename();
            $basename = pathinfo($originalName, PATHINFO_FILENAME);


            try {
                $img = $manager->read($image->getPathname());

                // 1. Convert JPG/PNG to WebP (original size)
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $webpFilepath =$sourcePath.'/'. $basename .'.webp';
                    $img->toWebp(85)->save($webpFilepath);
                    $this->info("✅ Converted to WebP: $webpFilepath");
                    $converted++;
                

                // 2. Create Thumbnail
                $thumbFile = $destPath . '/tiny_' . $basename . '.webp';
                $img->cover($thumbWidth, $thumbHeight)->toWebp(80)->save($thumbFile);
                $this->info("🖼️  Thumbnail created:  $thumbFile ");
                $thumbed++;
                }

            } catch (\Throwable $e) {
                $this->error("❌ Failed processing $originalName: " . $e->getMessage());
            }
        }

        $this->line("\n🎉 Done! {$converted} images converted to WebP and {$thumbed} thumbnails created.");
        return 0;
    }
}