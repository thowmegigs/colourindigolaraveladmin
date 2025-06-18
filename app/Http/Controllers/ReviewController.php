<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(){
        return "dfdsf";

    }
    public function store(Request $request)
        {
            
            $request->validate([
                'review_id' => 'required',
                 'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
             
            ]);

            \DB::transaction(function () use ($request) {
                $review = \App\Models\Review::find($request->review_id);

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $imageFile) {
                        $filename = \Str::uuid()->toString() . '.' . $imageFile->getClientOriginalExtension();
                        $path = $imageFile->storeAs('review_images/'.$review->product_id,$filename, 'public');

                        $review->images()->create([
                            'image' => $filename,
                        ]);
                    }
                }
            });

            return response()->json(['message' => 'Review submitted successfully.']);
        }
}
