<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
class ImageResizeController extends Controller
{
    public function product_image_resize(Request $request)
    {

        // according to path your image file
        $path = public_path('storage/products/' . $request->id);
        $img=null;
        if (File::exists($path)) 
        $img = Image::make($path . '/' . $request->name);
        else
        $img = Image::make(public_path('no_image.jpeg'));
        //manipulate image
        $img->resize($request->width, $request->height, function ($constraint) {
            $constraint->aspectRatio();
          //  $constraint->upsize();
        });

        $response = Response::make($img->encode('webp', 80));

        // set content-type
        $response->header('Content-Type', 'image/webp');

        // output
        return $response;
    }
    public function category_image_resize(Request $request)
    {

        // according to path your image file
        $path = public_path('storage/categories/');
        $img = Image::make($path . '/' . $request->name);

        //manipulate image
        $img->resize($request->width, $request->height);
        $response = Response::make($img->encode('webp', 80));

        // set content-type
        $response->header('Content-Type', 'image/webp');

        // output
        return $response;
    }
    public function collection_image_resize(Request $request)
    {

        // according to path your image file
        $path = public_path('storage/collections/');
        $img = Image::make($path . '/' . $request->name);

        if(!$request->has('width')){
            $img->resize(null, $request->height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        else{
                    $img->resize($request->width, $request->height, function ($constraint) {
                 //  $constraint->aspectRatio();
            });
        }

        $response = Response::make($img->encode('webp', 90));

        // set content-type
        $response->header('Content-Type', 'image/webp');

        // output
        return $response;
    }
    public function banner_image_resize(Request $request)
    {

        // according to path your image file
        $path = public_path('storage/banners/');
        $img = Image::make($path . '/' . $request->name);

        //manipulate image
        $img->resize($request->width, $request->height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $response = Response::make($img->encode('webp', 90));

        // set content-type
        $response->header('Content-Type', 'image/webp');

        // output
        return $response;
    }
    public function slider_image_resize(Request $request)
    {

        // according to path your image file
        $path = public_path('storage/sliders/');
        $img = Image::make($path . '/' . $request->name);

        //manipulate image
        $img->resize($request->width, $request->height, function ($constraint) {
            $constraint->aspectRatio();
        });

        $response = Response::make($img->encode('webp', 90));

        // set content-type
        $response->header('Content-Type', 'image/webp');

        // output
        return $response;
    }
}
