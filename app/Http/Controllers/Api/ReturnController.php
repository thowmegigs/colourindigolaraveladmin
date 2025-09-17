<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
class ReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload1(Request $request)
    {
            $file_name1 = null;
            $file_name2 = null;
            $file_name3 = null;
            $file_name4 = null;
            $qr_image = null;
           
            $validator = Validator::make($request->all(), [
                'images' => 'required|array|max:4',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                
            ]);

           if ($validator->fails()) {
                    $errors = $validator->errors()->toArray(); // get errors as associative array

                    // Store error as a single string (optional, for internal logging)
                    $errorString = implode(' | ', $validator->errors()->all());
                    \DB::table('system_errors')->insert([
                        'error' => $errorString,
                        'which_function' => 'Submit Return Request',
                    ]);

                    return response()->json([
                        'status' => false,
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $errors,
                    ], 422); // 422 Unprocessable Entity
                }
     
    try{
            $uploades_files = [];
            // chmod(\Storage::path('public/returns'), 0755);
              Log::error("jsjdjdjdj");
            foreach ($request->file('images') as $image)
            {
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $slug = \Str::slug($originalName);
                $extension = $image->getClientOriginalExtension();
                $newName = $slug . '-' . time() . '-' . \Str::random(6) . '.' . $extension;
          
                $image->storeAs('public/returns', $newName);

                $uploades_files[] =$newName;

            }
           
              $return_id = $request->return_id;
              $item_id = $request->item_id;
             
             
              \DB::table('return_items')->where(['id'=>$return_id,'order_item_id'=>$item_id])->update([
                'first_image' => isset($uploades_files[0])?$uploades_files[0]:null, 
                'second_image' => isset($uploades_files[1])?$uploades_files[1]:null,
                'third_image' => isset($uploades_files[2])?$uploades_files[2]:null,
                'fourth_image' => isset($uploades_files[3])?$uploades_files[3]:null,
               
              ]);
            return response()->json(['success'=>true,'message' =>'Return request submitted successfully'], 200);
        }
         catch (\Exception $ex) { \Sentry\captureException($ex);
           
            Log::error($ex->getMessage() . '===' . $ex->getLine());
            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '=== at line' . $ex->getLine(),
                'which_function' => 'Submit Return Request',
            ]);
            return response()->json(['success'=>false,'message' => 'Failed to submit the  request '], 400);
        }

    }
    public function add_update_bank_detail(Request $request)
    {

        $qr_image = null;
       $validator = Validator::make($request->all(), [
            'bank_name' => 'required_without:qr_image|max:255',
            'account_number' => 'required_without:qr_image|max:255',
            'account_holder' => 'required_without:qr_image|max:255',
            'ifsc' => 'required_without:upi_id|max:100',
            'upi_id' => 'required_without:account_number',
            'user_id' => 'required|numeric',
            'qr_image' => 'required_without:account_number|mimes:png,jpg,jpeg|max:1024',
        ]);
        if ($validator->fails()) {
                    $errors = $validator->errors()->toArray(); // get errors as associative array
                      $errorString = implode(' | ', $validator->errors()->all());
                    \DB::table('system_errors')->insert([
                        'error' => $errorString,
                        'which_function' => 'customer uplaod qrcode in api/returncontroller ',
                    ]);
                }
         $user_bank=\DB::table('customer_banks')->where('user_id',$request->user_id)->first();
        if($user_bank){
            if($user_bank->qr_image){
                    $path = "public/qr_images/{$user_bank->qr_image}";

                    if (\Storage::exists($path)) 
                    {
                        \Storage::delete($path);
                        
                    }
            }
          \DB::table('customer_banks')->where('user_id',$request->user_id)->delete();
        }
        \DB::beginTransaction();
        $user_id =$request->user_id;
        try {
            $filename=null;
          
            if ($request->hasFile('qr_image')) {
                $filenameWithExt = $request->file('qr_image')->getClientOriginalName();

                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('qr_image')->getClientOriginalExtension();

                $qr_image = $user_id . '_' . time() . '.' . $extension;

                $path = $request->file('qr_image')->storeAs('public/qr_image', $qr_image);
            }
            $update_ar = [
                'user_id' => $user_id,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder' => $request->account_holder,
                'ifsc' => $request->ifsc,
                'qr_image'=> $filename,
                'upi_id'=>$request->upi_id

            ];
            if (!empty($qr_image)) {
                $update_ar['qr_image'] = $qr_image;
            }
            
                 \DB::table('customer_banks')->insert($update_ar);
         

            \DB::commit();
            return response()->json($update_ar, 200);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();

            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '=== at line' . $ex->getLine(),
                'which_function' => 'Bank Detail Submit',
            ]);
            return response()->json(['message' => 'Failed to add bank detail'], 400);
        }

    }
    public function get_bank_detail($user_id)
    {
       
        return response()->json(['data' => \DB::table('customer_banks')->where('user_id',$user_id)->first()], 200);
    }
    
public function uploadUpiQr(Request $request)
{
    
  $validator = Validator::make($request->all(), [
                  'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                  'upi_code'=>'required', // max 2MB
                 'user_id' => 'required|numeric', // 
            ]);
if ($validator->fails()) {
                    $errors = $validator->errors()->toArray(); // get errors as associative array
   $errorString = implode(' | ', $validator->errors()->all());
                    \DB::table('system_errors')->insert([
                        'error' => $errorString,
                        'which_function' => 'customer uplaod qrcode in api/returncontroller ',
                    ]);

                    return response()->json([
                        'status' => false,
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $errors,
                    ], 422); // 422 Unprocessable Entity
                }
     
 try{
    $user_info=\DB::table('users')->where('id',$request->user_id)->first();
    if($user_info->qrcode_image){
     $path = "public/qr_images/{$user_info->qrcode_image}";

     if (\Storage::exists($path)) 
     {
        \Storage::delete($path);
        
            }
     }
      $file = $request->file('file');

    // Generate a unique name using timestamp and random string
    $filename = 'upi_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
 // Store in public storage (or customize the disk)
  $content = file_get_contents($file->getRealPath());
    $path = \Storage::disk('public')->put('qr_images/' . $filename, $content);

    //dd($path);
    \DB::table('users')->where('id',$request->user_id)->update(['upi_id'=>$request->upi_code,'qrcode_image'=>$filename]);
    // Return full URL to the uploaded file
    return response()->json([
        'success' => true,
        'message' => $path,
        'data'=>['upi_code'=>$request->upi_code,'image'=>$filename]
        
    ]);
     }
         catch (\Exception $ex) { \Sentry\captureException($ex);
           
            Log::error($ex->getMessage() . '===' . $ex->getLine());
            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '=== at line' . $ex->getLine(),
                'which_function' => 'upi qrcode uplaoded in api/returncontroller ',
            ]);
            return response()->json(['success'=>false,'message' => 'Failed to add the payment details '], 400);
        }
}

}