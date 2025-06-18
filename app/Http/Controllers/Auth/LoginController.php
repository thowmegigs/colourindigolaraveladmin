<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Carbon\Carbon;
use Algolia\AlgoliaSearch\SearchClient;
class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return Renderable
     */
    public function show()
    {
//         $client = SearchClient::create(
//     env('ALGOLIA_APP_ID'),
//     env('ALGOLIA_SECRET')
// );

//           $index = $client->initIndex('product_variants');

//          $index->clearObjects();
        $data['title'] = 'Login';
        return view('auth.login', with($data));
    }

    /**
     * Handle account login request
     *
     * @param LoginRequest $request
     *
     * @return \Illuminate\Http\Response
     */
     protected function determineGuard($host)
    {
        // Map subdomains to guards
        $subdomain = explode('.', $host)[0]; // e.g., "admin" from "admin.example.com"

        return match ($subdomain) {
            'admin' => 'web',
            'vendor' => 'vendor',
           
        };
    }
    public function login(LoginRequest $request)
    {
       
       $credentials = $request->only('email', 'password');
        $host = $request->getHost(); // e.g., admin.example.com

        $guard = $this->determineGuard($host);
      
        if (\Auth::guard($guard)->attempt($credentials)) {
            return createResponse(true,'Logged successfully',$guard=='vendor'?route('vendor.dashboard'):route('admin.dashboard'));
        }

        return createResponse(false,'Invalid credentials');

    }
   public function customer_login(Request $request)
    {
        $cr = ['phone' => $request->phone, 'password' => $request->password];

        if (Auth::attempt($cr)) {
           

            if (auth()->user()->hasRole(['Customer']) && auth()->user()->status == 'Active') {

                return createResponse(true, 'Logged In Successfully');
            } else {
                
                    return createResponse(false, 'Login credentials are invalid');
                

            }

        } else {
            // dd(auth()->id());
            return createResponse(false, 'Login credentials are invalid');

        }

    }
    public function resend_otp(Request $r)
    {
        try {
            $phone = $r->phone;
            if (!empty($phone)) {
                $otp = rand(100000, 999999);
                if (\DB::table('user_otps')->where(['phone' => $phone])->exists()) {
                    \DB::table('user_otps')->where(['phone' => $phone])->delete();
                }
                \DB::table('user_otps')->insert(['phone' => $phone, 'otp' => $otp, 'created_at' => date("Y-m-d H:i:s")]);
                try {
                    $setting = \DB::table('settings')->first();
                    // if ($setting->sms_live != 'Yes') {
                    //     $resp = $this->mail($user->email, 'OTP Verification', "Your Opt is -" . $otp);
                    // } else {
                    //      dlog('sendin','okok');
                    //     sendSms($phone, $otp,'No');
                    // }

                } catch (\Exception $ex) { \Sentry\captureException($ex);
                dlog('smss exp',$ex->getMessage());
                }
                return response()->json(['success'=>true,'message' => 'Otp send successfully'], 200);

            }
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '==Line==' . $ex->getLine() . '==File==' . $ex->getFile(),
                'created_at' => Carbon::now(),
                'which_function' => 'Resend Otp function',
            ]);
            return response()->json(['success'=>false,
                'message' => 'Some Error occurred,Please try later ',

            ], 400);
        }

    }
    public function verify_otp(Request $r)
    {
        $otp = $r->otp;
        $email = $r->has('email') && !empty($r->email) ? $r->email : null;
        $phone = $r->has('phone') && !empty($r->phone) ? $r->phone : null;
        try {
            if (\DB::table('user_otps')->when(!is_null($phone), function ($q) use ($phone) {
                return $q->wherePhone($phone);
            })->whereOtp($otp)->exists()) {
                \DB::table('users')->when(!is_null($phone), function ($q) use ($phone) {
                    return $q->wherePhone($phone);
                })->update(['phone_verified' => 'Yes']);
                $user = \App\Models\User::when(!is_null($email), function ($q) use ($email) {
                    return $q->whereEmail($email);
                })->when(!is_null($phone), function ($q) use ($phone) {
                    return $q->wherePhone($phone);
                })->first();
                Auth::login($user);
                 return response()->json(['success'=>true,'message' => 'Otp verified successfully,you\'re now logged in'], 200);
            } else {
                return response()->json(['success'=>false,'message' => 'Incorrect Otp entered'], 404);
            }
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::table('system_errors')->insert([
                'error' => $ex->getMessage() . '==Line==' . $ex->getLine() . '==File==' . $ex->getFile(),
                'created_at' => Carbon::now(),
                'which_function' => 'Otp verify function',
            ]);
            return response()->json(['success'=>false,
                'message' => 'Some Error occurred,Please try later ',

            ], 400);
        }

    }
    /**
     * Handle response after user authenticated
     *
     * @param Request $request
     * @param Auth $user
     *
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended();
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->to('/');
    }
}
