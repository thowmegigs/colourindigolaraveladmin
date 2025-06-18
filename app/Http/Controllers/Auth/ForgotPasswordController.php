<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Validation\Rules\Password;
// use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */

    // use SendsPasswordResetEmails;

    public function ForgetPassword()
    {
        $data['title']='Forget Password';
        return view('auth.forget_password',with($data));
    }

    public function ForgetPasswordStore(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(64);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

         $resetUrl = url(domain_route('ResetPasswordGet', $token, false));
         $str=view('mails.forget_password_mail',['resetUrl' => $resetUrl])->render();
         $resp=$this->mail($request->email,'Forgot Password? ',$str);
        return redirect()->back()->with('success','We have emailed your password reset link!');
    }

    public function ResetPassword($token)
    {
        
        return view('auth.reset-password', ['token' => $token,'title'=>'Reset Password']);
    }

    public function ResetPasswordStore(Request $request)
    {
       $request->validate([
                    'token' => 'required',
                    'password' => [
                        'required',
                        'string',
                        'confirmed',
                        Password::min(8)
                            ->mixedCase()
                            ->letters()
                            ->numbers()
                            ->symbols()
                            
                    ],
                    'password_confirmation' => 'required',
                ]);
//dd($request->token);
        $update = DB::table('password_resets')->where(['email' => $request->email, 'token' => $request->token])->latest();

        if (!$update) {
            return redirect()->back()->withError('Invalid token!');
        }

        $user = User::where('email', $request->email)->update(['password' => Hash::make($request->password),'plain_password'=>$request->password]);

        // Delete password_resets record
        \DB::table('password_resets')->where(['email' => $request->email])->delete();

        return  redirect(domain_route('login'))->withSuccess('Your password has been successfully changed!');
    }
}
