<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Twilio\Rest\Client;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
class RegisterController extends Controller
{
    /**
     * Display register page.
     *
     * @return \Illuminate\Http\Response
     */
    public function account(Request $r)
    {
        $data["title"] = "My Account";
        $data["orders"] = \App\Models\Order::with("items")
            ->whereUserId(auth()->id())
            ->latest()
            ->get();
        $data["address"] = \App\Models\Address::with([
            "bill_state",
            "bill_city",
            "ship_state",
            "ship_city",
        ])
            ->whereUserId(auth()->id())
            ->first();

        $data["me"] = \DB::table("users")
            ->whereId(auth()->id())
            ->first();
        $data["city"] = \DB::table("cities")
            ->whereId($data["address"]->billing_city)
            ->first();
        $data["states"] = \DB::table("states")->get();

        return view("frontend.account", with($data));
    }
    public function show(Request $r)
    {
        $data["states"] = getList("State");
        $data["title"] = "Registration";
        return view("auth.register", with($data));
    }
    public function uploadDocuments(Request $request,$id=null)
    {
        // Validate file inputs
        
         $user = is_null($id)?\Auth::guard("vendor")->user():\App\Models\Vendor::findOrFail($id);
          $rules = [
                "business_license_image" => "nullable|mimes:pdf|max:2048",
                "trademark_image" => "nullable|mimes:pdf|max:2048",
            ];

            // Make GST and PAN fields required only if not present
            $rules['gst'] = $user->gst ? "nullable" : "required";
            $rules['pan'] = $user->pan ? "nullable" : "required";

            $rules['gst_image'] = ($user->gst_image ? "nullable" : "required") . "|mimes:pdf|max:2048";
            $rules['pan_image'] = ($user->pan_image ? "nullable" : "required") . "|mimes:pdf|max:2048";

            $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [];
        $data["gst"] = $request->gst;
        $data["pan"] = $request->pan;
        foreach (
            [
                "gst_image",
                "pan_image",
                "business_license_image",
                "trademark_image",
            ]
            as $field
        ) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename =
                    $field .
                    "_" .
                    time() .
                    "_" .
                    Str::random(6) .
                    "." .
                    $file->getClientOriginalExtension();

                // Store using storeAs
                $file->storeAs(
                    "vendor_documents/" . $user->id,
                    $filename,
                    "public"
                );

                $data[$field] = $filename;
            }
        }

        \DB::table("vendors")
            ->where("id", $user->id)
            ->update($data);
        return redirect()
            ->back()
            ->with("success", "Documents uploaded successfully.");
    }

    public function updatePassword(Request $request,$id=null)
    {
       $user = is_null($id)?\Auth::guard("vendor")->user():\App\Models\Vendor::findOrFail($id);
        // Validate input
        $validator = Validator::make($request->all(), [
            "current_password" => "required|string",
            "password" => "required|string|min:8|confirmed",
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()
                ->back()
                ->withError("Current password is incorrect.");
        }

        // Update the password
        $user->password = $request->password;
        $user->save();
        return redirect()
            ->back()
            ->with("success", "Password updated successfully.");
    }
    public function updateProfilePicture(Request $request,$id=null)
    {
      $user = is_null($id)?\Auth::guard("vendor")->user():\App\Models\Vendor::findOrFail($id);
        $old_user = $user;
        if ($request->hasFile("avatar")) {
            $file = $request->file("avatar");

            // Define folder and filenames
            $folder = "vendor_logo";
            $originalExtension = strtolower(
                $file->getClientOriginalExtension()
            );
            $filenameBase = "vendor_" . $user->id;
            $originalFilename = $filenameBase . "." . $originalExtension;
            $tinyFilename = "tiny_" . $filenameBase . ".webp";

            // Store original file
            $path = $file->storeAs($folder, $originalFilename, "public");

            // Create thumbnail using Intervention Image v3
            $manager = new ImageManager(new Driver()); // Imagick or Gd
            $image = $manager->read($file->getPathname());

            $image->scaleDown(width: 130); // resize maintaining aspect ratio

            $tinyWebp = $image->toWebp(95);
            \Storage::disk("public")->put(
                "{$folder}/{$tinyFilename}",
                (string) $tinyWebp
            );

            // Save original image name in DB
            $user->logo_image = $originalFilename;
            $user->save();

            return response()->json([
                "message" => "Uploaded successfully",
                "path" => asset("storage/" . $path), // Access via public/storage
            ]);
        }

        // Save path to user model...

        return response()->json(["message" => "No file uploaded"], 400);
    }
    public function updateProfile(Request $request,$id=null)
    {
        $user = is_null($id)?\Auth::guard("vendor")->user():\App\Models\Vendor::findOrFail($id);
        
        $request->validate([
            "name" => "required|string|max:255",
            "email" =>
                "nullable|string|email|max:255|unique:vendors,email," .
                $user->id,
          
            "phone" =>
                "required|numeric|digits_between:10,12|unique:vendors,phone," .
                $user->id,
            'address'=>'required|string|max:555',
            'address2'=>'required|string|max:555',
            'city_id'=>'required|numeric',
            'state_id'=>'required|numeric',
            'pincode'=>'required|numeric|digits_between:4,6',
        ]);
        $data = $request->all();
        try {
           
            $oldAddressData = [
                    'address' => $user->address,
                    'address2' => $user->address2,
                    'pincode' => $user->pincode,
                    'state_id' => $user->state_id,
                    'city_id' => $user->city_id,
                ];
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->address2 = $request->address2;
            $user->pincode = $request->pincode;
            $user->state_id = $request->state_id;
            $user->city_id = $request->city_id;
          

            $user->save();
            \Auth::guard("vendor")->login($user);
              $newAddressData = [
                    'address' => $user->address,
                    'address2' => $user->address2,
                    'pincode' => $user->pincode,
                    'state_id' => $user->state_id,
                    'city_id' => $user->city_id,
                ];
                if ($oldAddressData !== $newAddressData) {
                     $delhService = app(\App\Services\DelhiveryService::class);
                         $resp = $delhService->createOrUpdateWarehouseAddress($user);
                  
                    if (!$resp["success"]) {
                        return redirect()
                            ->back()
                            ->withError($resp["message"]);
                        
                    }
                    else{
                    //    $shiprocketService = app(\App\Services\ShiprocketService::class);
                    //    $resp = $shiprocketService->addPickupLocationOfVendor($user);
                    //    if (!$resp["success"]) {
                    //         return redirect()
                    //             ->back()
                    //             ->withError($resp["message"]);
                        
                    //       }
                    }
                }
            
            return redirect()
                ->back()
                ->withSuccess("Profile Updated successfully");
        } catch (\Exception $ex) {
            \Sentry\captureException($ex);
            \DB::table("system_errors")->insert([
                "error" =>
                    $ex->getMessage() .
                    "==Line==" .
                    $ex->getLine() .
                    "==File==" .
                    $ex->getFile(),
                "created_at" => Carbon::now(),
                "which_function" => "Update Profile function",
            ]);
            return redirect()
                ->back()
                ->withError("Some error occured,please try again later");
        }
    }
    // public function updateAddress(Request $request,$id=null)
    // {
    //     $user = \Auth::user();
    //     $request->validate([
    //         "billing_fname" => "required|string|max:255",
    //         "billing_address1" => "required",
    //         "billing_email" => "nullable|valid_email",
    //         "billing_city" => "required|string|max:255",
    //         "billing_phone" => "required|numeric|digits_between:10,12",
    //         "shipping_fname" => "required|string|max:255",
    //         "shipping_address1" => "required",
    //         "shipping_city" => "required|string|max:255",
    //         "shipping_phone" => "required|numeric|digits_between:10,12",
    //         "shipping_email" => "nullable|valid_email",
    //     ]);

    //     try {
    //         $post = [
    //             "billing_fname" => $request->billing_fname,
    //             "billing_lname" => $request->billing_lname,
    //             "billing_address1" => $request->billing_address1,
    //             "billing_address2" => $request->billing_address2,
    //             "billing_city" => $request->billing_city,
    //             "billing_pincode" => $request->billing_pincode,
    //             "billing_phone" => $request->billing_phone,
    //             "billing_email" => $request->billing_email,
    //             "shipping_fname" => $request->shipping_fname,
    //             "shipping_lname" => $request->shipping_lname,
    //             "shipping_address1" => $request->shipping_address1,
    //             "shipping_address2" => $request->shipping_address2,
    //             "shipping_city" => $request->shipping_city,
    //             "shipping_pincode" => $request->shipping_pincode,
    //             "shipping_phone" => $request->shipping_phone,
    //             "shipping_email" => $request->shipping_email,
    //         ];
    //         \DB::table("user_address")
    //             ->whereUserId(auth()->id())
    //             ->update($post);
    //         return response()->json(["success" => true], 200);
    //     } catch (\Exception $ex) {
    //         \Sentry\captureException($ex);
    //         \DB::table("system_errors")->insert([
    //             "error" =>
    //                 $ex->getMessage() .
    //                 "==Line==" .
    //                 $ex->getLine() .
    //                 "==File==" .
    //                 $ex->getFile(),
    //             "created_at" => Carbon::now(),
    //             "which_function" => "Update Profile function",
    //         ]);
    //         return response()->json(
    //             [
    //                 "success" => false,
    //                 "message" => "Some Error occurred,Please try later ",
    //             ],
    //             400
    //         );
    //     }
    // }
    /**
     * Handle account registration request
     *
     * @param RegisterRequest $request
     *
     * @return \Illuminate\Http\Response
     */

    public function registerValidationAndOtp(Request $request)
    {
        // Validate the request data
        $validator = Validator::make(
            $request->all(),
            [
                "business_name" => "required|string|max:455",
                "email" => "required|string|email|max:255|unique:vendors,email",
                "phone" =>
                    "required|numeric|digits_between:10,12|unique:vendors,phone",

                // "address" => "required|string|min:10|max:80",
                // "address" => "required|string|min:10|max:80",
                // "city_id" => "required|numeric",
                // "state_id" => "required|numeric",
                // "pincode" => "required|numeric|digits_between:4,6",
                "password" => [
                    "required",
                    "confirmed",
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
            ],
            [
                "business_name.required" => "Please enter your business name",
                "email.required" => "Please enter your email address",
                "email.email" => "Please enter a valid email address",
                "email.unique" => "This email is already registered",
                "phone.required" => "Please enter your contact number",

                // "address.required" => "Please enter your  1",
                // "address2.required" => "Please enter address2",
                // "city_id.required" => "Please enter your city",
                // "state_id.required" => "Please enter your state/province",
                // "pincode.required" => "Please enter your postal/ZIP code",

                "password.required" => "Please create a password",
                "password.confirmed" => "Passwords do not match",
                "password.min" => "Password must be at least 8 characters",
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        // Generate OTP

        $phone_otp = rand(100000, 999999);
        $email_otp = rand(100000, 999999);
        $expires_at = \Carbon\Carbon::now()->addMinutes(10);
        $now = now();
        $post = $request->all();
        DB::table("vendor_otps")
            ->where("phone", $post["phone"])
            ->orWhere("email", $post["email"])
            ->delete();
        DB::insert(
            "INSERT INTO vendor_otps (phone, otp, expires_at, created_at) VALUES (?, ?, ?, ?)",
            [$post["phone"], $phone_otp, $expires_at, $now]
        );
        DB::insert(
            "INSERT INTO vendor_otps (email, otp, expires_at, created_at) VALUES (?, ?, ?, ?)",
            [$post["email"], $email_otp, $expires_at, $now]
        );

        // Store OTP in session
        session([
            "seller_registration" => [
                "business_name" => $request->business_name,
                "email" => $request->email,
                "phone" => $request->phone,
                // "address" => $request->address,
                // "address2" => $request->address2,
                // "city_id" => $request->city_id,
                // "state_id" => $request->state_id,
                // "pincode" => $request->pincode,
                "password" => $request->password,
                "phone_otp" => $phone_otp,
                "email_otp" => $email_otp,
                "otp_expires_at" => now()->addMinutes(10),
            ],
        ]);
        $str = view("mails.otp", ["otp" => $email_otp])->render();
        // Send OTP email
        // In a real application, you would send an actual email here
        // Mail::to($request->email)->send(new SellerVerificationMail($otp));

        $resp = $this->mail($request->email, "OTP Verification", $str);
        $this->sendSmsOtp($request->phone, $phone_otp);
        return response()->json([
            "success" => true,
            "message" =>
                "Verification code sent to your email and phone number ",
        ]);
    }
    public function register(Request $request)
    {
        $registrationData = session("seller_registration");
        //    dd($registrationData);
        // Check if registration data exists and is verified
        if (!$registrationData) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Registration session expired. Please start again.",
                ],
                422
            );
        }

        // Check if both email and phone are verified
        if (
            !$registrationData["email_verified"] ||
            !$registrationData["phone_verified"]
        ) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Both email and phone must be verified before completing registration.",
                ],
                422
            );
        }
        \DB::beginTransaction();
        try {
            // Create user account
            $user = \App\Models\Vendor::create([
                "name" => $registrationData["business_name"],
                "email" => $registrationData["email"],
                "password" => $registrationData["password"],
                "phone" => $registrationData["phone"],
                // "address" => $registrationData["address"],
                // "address2" => $registrationData["address2"],
                // "city_id" => $registrationData["city_id"],
                // "state_id" => $registrationData["state_id"],
                // "pincode" => $registrationData["pincode"],
                "status" => "Active",
                "email_verified" => "Yes",
                "phone_verified" => "Yes",
            ]);

            // Create seller profile
            $user->assignRole("Vendor");
            // $shiprocketService = app(\App\Services\ShiprocketService::class);
            // $resp = $shiprocketService->addPickupLocationOfVendor($user);
            // if (!$resp["success"]) {
            //     return response()->json([
            //         "success" => false,
            //         "message" => $resp["message"],
            //     ]);
            // }
            // Clear registration session
            session()->forget("seller_registration");
            \DB::commit();
            $str = view("mails.vendor_registration", [
                "vendor" => $user,
            ])->render();
            $resp = $this->mail(
                $request->email,
                "Seller Registration at Colourindigo",
                $str
            );
            return response()->json([
                "success" => true,
                "message" => "Registration completed successfully",
                "redirect" => route("vendor.login"),
            ]);
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            \DB::rollback();
            \DB::table("system_errors")->insert([
                "error" => $e->getMessage(),
                "which_function" =>
                    "Vendor Registration register function line " .
                    $e->getLine(),
            ]);
            return response()->json(
                [
                    "success" => false,
                    "message" => $e->getMessage(),
                ],
                500
            );
        }
    }
    public function customer_register(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "phone" =>
                "required|numeric|unique:users,phone|digits_between:10,12",
            "password" => "required|min:6|confirmed",
        ]);

        \DB::beginTransaction();

        try {
            $newData = [
                "name" => $request->name,
                "phone" => $request->phone,
                "password" => $request->password,
            ];

            $user = User::create($newData);
            $user->assignRole("Customer");
            \DB::commit();
            return createResponse(
                true,
                "Account created successfully,Please Login"
            );
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            \DB::rollback();
            return createResponse(false, $e->getMessage());
        }
    }

    public function verify_email($_vX00, $_tX00)
    {
        $email = $_vX00;
        $token = $_tX00;
        if (!$token && !$email) {
            abort(404);
        }
        $token = \Crypt::decrypt($token);
        $update = \DB::table("password_resets")
            ->where(["email" => $email, "token" => $token])
            ->first();

        if (!$update) {
            //\Session::flash('error', 'Changes Saved.' );
            return redirect()
                ->route("login")
                ->with("error", "Account verification failed");
        }

        $user = User::where("email", $email)->update([
            "email_verified_at" => \Carbon\Carbon::now(),
        ]);

        // Delete password_resets record
        \DB::table("password_resets")
            ->where(["email" => $email])
            ->delete();

        return redirect(route("login"))->withSuccess(
            "Email verified successfully"
        );
    }
    public function resendOtp(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            "verification_method" => "required|string|in:email,phone",
            "email" => "required_if:verification_method,email|email",
            "phone" => "required_if:verification_method,phone|string",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        // Get registration data from session
        $registrationData = session("seller_registration");

        // Check if registration data exists
        if (!$registrationData) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Registration session expired. Please start again.and refresh the page",
                ],
                422
            );
        }

        // Generate new OTP
        $newOtp1 = sprintf("%06d", mt_rand(100000, 999999));
        $newOtp2 = sprintf("%06d", mt_rand(100000, 999999));
        $post = $request->all();
        $expires_at = \Carbon\Carbon::now()->addMinutes(10);
        $now = now();
        if ($request->verification_method === "email") {
            // Check if email matches
            if ($registrationData["email"] !== $request->email) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Email does not match registration email.",
                    ],
                    422
                );
            }

            // Update email OTP in session
            session()->put("seller_registration.email_otp", $newOtp1);
            session()->put(
                "seller_registration.email_otp_expires_at",
                now()->addMinutes(10)
            );
            DB::table("vendor_otps")
                ->where("email", $post["email"])
                ->delete();

            DB::insert(
                "INSERT INTO vendor_otps (email, otp, expires_at, created_at) VALUES (?, ?, ?, ?)",
                [$post["email"], $newOtp1, $expires_at, $now]
            );
            // Send email OTP
            // Mail::to($request->email)->send(new SellerVerificationMail($newOtp));
            $str = view("mails.otp", ["otp" => $newOtp1])->render();

            $resp = $this->mail($post["email"], "OTP Verification", $str);
            // For demonstration, log the OTP (remove in production)
            \Log::info("New Email OTP for {$request->email}: $newOtp");
        } else {
            // Check if phone matches
            if ($registrationData["phone"] !== $request->phone) {
                return response()->json(
                    [
                        "success" => false,
                        "message" =>
                            "Phone number does not match registration phone.",
                    ],
                    422
                );
            }

            // Update phone OTP in session
            session()->put("seller_registration.phone_otp", $newOtp2);
            session()->put(
                "seller_registration.phone_otp_expires_at",
                now()->addMinutes(10)
            );
            DB::table("vendor_otps")
                ->where("phone", $post["phone"])
                ->delete();

            DB::insert(
                "INSERT INTO vendor_otps (phone, otp, expires_at, created_at) VALUES (?, ?, ?, ?)",
                [$post["phone"], $newOtp2, $expires_at, $now]
            );
            // Send SMS OTP
            $this->sendSmsOtp($post["phone"], $newOtp2);

            // For demonstration, log the OTP (remove in production)
            \Log::info("New Phone OTP for {$request->phone}: $newOtp2");
        }

        return response()->json([
            "success" => true,
            "message" => "Verification code resent successfully",
        ]);
    }
    public function verifyOtp(Request $request)
    {
        $phone = $request->phone;
        $otp = $request->otp;
        $email = $request->email;
        $registrationData = session("seller_registration");
        if (!$registrationData) {
            return response()->json(
                [
                    "success" => false,
                    "errors" => [
                        "otp" => [
                            "Registration session expired. Please start again.",
                        ],
                    ],
                ],
                422
            );
        }
        $verification_method = $request->verification_method;
        if ($verification_method == "email") {
            $record = DB::table("vendor_otps")
                ->where("email", $email)
                ->where("otp", $otp)
                ->where("expires_at", ">", now())
                ->first();

            if ($record) {
                session()->put("seller_registration.email_verified", true);
                DB::table("vendor_otps")
                    ->where("email", $email)
                    ->delete();

                return createResponse(true, "OTP verified");
            }
            session()->put("seller_registration.email_verified", false);

            return createResponse(false, "Invalid or expired OTP");
        } else {
            $record = DB::table("vendor_otps")
                ->where("phone", $phone)
                ->where("otp", $otp)
                ->where("expires_at", ">", now())
                ->first();

            if ($record) {
                session()->put("seller_registration.phone_verified", true);
                DB::table("vendor_otps")
                    ->where("phone", $phone)
                    ->delete();

                return createResponse(true, "OTP verified");
            }
            session()->put("seller_registration.phone_verified", false);
            return createResponse(false, "Invalid or expired OTP");
        }
    }
    protected function sendSmsOtp($phone, $otp)
    {
        SendSms($phone, $otp);
    }
    protected function saveVendorLocation()
    {
        $vendor = auth()
            ->guard("vendor")
            ->user();
        $shiprocketService = app(\App\Services\ShiprocketService::class);
        $shiprocketService->addPickupLocationOfVendor($vendor);
    }
}
