<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
class ImageUploadController extends Controller
{
    public function showForm(Request $request){
        return view('upload');
    }
    public function doUpload(Request $request)
    {
       $request->validate([
        'image' => 'required|image|max:5120', // max 5MB
    ]);

    $file = $request->file('image');

    if (!$file->isValid()) {
        return response()->json(['error' => 'Invalid image.'], 400);
    }
    $folder="categories";
 $manager = new ImageManager(new Driver());
    $image = $manager->read($file->getPathname());

    // ✅ Unique filename
    $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $slugged = \Str::slug($filename);
    $uniqueName = $slugged . '_' . uniqid() . '.webp';

    // ✅ Convert and store original image
    $originalWebp = $image->toWebp(95);
    $originalPath = "{$folder}/{$uniqueName}";
   // $originalTempPath = storage_path("app/temp_{$uniqueName}");
  //  file_put_contents($originalTempPath, (string) $originalWebp);
    $stored = $file->storeAs("public/{$folder}", $uniqueName); // Stores in storage/app/public/test

    // ✅ Thumbnail sizes
    // product 
    // $sizes = [
    //     'tiny'  => 100,
    //     'small' => 300,
    //     'medium' => 600,
    //     'large' => 1000,'xlarge'=>1600
    // ];
//    slider ,section header $sizes = [
//     'tiny'  => 360,
//     'small' => 480,
//     'medium' => 768,
//     'large' => 1224,
//     ];
//    banner/collection $sizes = [
//     'tiny'  => 200,
//     'small' => 350,
//     'medium' => 550,
    //  'large'=>750,
    // xlarge=>1200

//     
//     ];
//   // category  $sizes = [
//     'tiny'  => 130,
    
//     ];
  $sizes = [
      'tiny'  => 200,
    'small' => 350,
    'medium' => 500,
  'large'=>700
    ];
    $thumbnailPaths = [];
    foreach ($sizes as $label => $width) {
    // Read fresh image (to avoid cumulative degradation)
    $thumb = $manager->read($file->getPathname());

    // Resize proportionally
    $thumb->scaleDown(width: $width);

    // Convert to WebP at best quality
    $thumbWebp = $thumb->toWebp(quality: 100);

    // Generate thumbnail name
    $thumbName = $slugged . '_' . uniqid() . "_{$label}.webp";

    // Store directly in public/test/thumbnails using put
    \Storage::disk('public')->put("{$folder}/thumbnail/{$thumbName}", (string) $thumbWebp);

    $thumbnailPaths[$label] = "storage/{$folder}/thumbnail/{$thumbName}";
}



        return redirect()->back()->with('success',"Uploaded");
    }
}
