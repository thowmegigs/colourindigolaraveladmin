<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Services\DelhiveryService;
class FrontendController extends Controller
{
   public function checkPincode(Request $request)
    {
        $delhivery=app('DelhiveryService');
        $request->validate([
            'pincode' => 'required|string|size:6',
        ]);

        $pincode = $request->input('pincode');

        $response = $delhivery->checkPincode($pincode);

        return response()->json($response);
    }
   public function needAndroidUpdate(Request $request)
    { 
        
        return response()->json(['need'=>true,'version'=>'0.0.1']);
    }
    public function index(Request $r)
    {
    //    return Hash::make('Tree@20252024');
      
     
       return redirect(domain_route('login'));
    
     }
    
    public function clear_cache()
    {

        \Artisan::call('storage:link');
       
        
        return 'done';
    }
    public function cache()
    {

     

        \Artisan::call('optimize');
         \Artisan::call('config:cache');
       
        \Artisan::call('route:cache');
            \Artisan::call('storage:link');
        return 'cleared cached';
    }
    /*****Redirect user id logged in on accessing login route  */
    public function redirect()
    {

        if (auth()->user()->hasRole('Admin')) {
            return redirect(route('admin.dashboard'));
        } else {
            return redirect(route('user.dashboard'));
        }

    }
 public function privacy()
    {
        $data['setting']=\DB::table('settings')->first();
        return view('privacy_policy',with($data));
    }
    public function aboutus()
    {
        $data['setting']=\DB::table('settings')->first();
        return view('aboutus',with($data));
    }
   
     public function shipping_policy()
    {
        $data['setting']=\DB::table('settings')->first();
        return view('shipping_policy',with($data));
    }
    public function return_policy()
    {
        $data['setting']=\DB::table('settings')->first();
        return view('returnandrefund_policy',with($data));
    }
      public function term()
    {
        $data['setting']=\DB::table('settings')->first();
        return view('terms_conditions',with($data));
    }
     public function contactus()
    {
       
        $data['setting']=\DB::table('settings')->first();
        return view('contactus',with($data));
    }
    public function sendMail(Request $r)
    {
       \Mail::raw('This is a test email via Brevo SMTP', function ($message) {
            $message->to('throwmegigs@gmail.com')->subject('Test Email from Laravel using Brevo SMTP');
         });
         return "sent";

    }
    public function getHtml(Request $r)
    {
        $cat = $r->category;
        $labels = \App\Models\Label::with('label_values')->whereCategoryId($cat)->get();
        $t = view('partial', compact('labels'))->render();
        return $t;
    }
    public function getOrderDetail(Request $r)
    {
        $data['label_values'] = array_values(json_decode($r->val_ar, true));
        $data['user_info'] = json_decode($r->user, true);
        $data['pincode'] = $r->pincode;
        $data['chosen_date'] = $r->chosen_date;
        $amount = 0;
        foreach ($data['label_values'] as $t) {
            $amount += floatVal($t['price']);
        }
        $data['amount'] = $amount;
        $t = view('order_detail', $data)->render();
        return $t;
    }
    public function submitForm(Request $r)
    {
        $data['label_values'] = array_values(json_decode($r->val_ar, true));
        $amount = 0;
        foreach ($data['label_values'] as $t) {
            $amount += floatVal($t['price']);
        }
        $data['user_info'] = json_decode($r->user, true);
        $data['pincode'] = $r->pincode;
        $data['chosen_date'] = $r->chosen_date;
        $info = $data['user_info'];
        $password = Hash::make('12345678');
        $user = \App\Models\User::create(['name' => $info['name'], 'password' => $password, 'email' => $info['email'], 'phone' => $info['email'],
            'address' => $info['address']]);
        Auth::login($user);
        $r->session()->regenerate();
        \App\Models\OrderDetail::create(['user_id' => auth()->id(), 'label_details' => json_encode($data['label_values']), 'amount' => $amount,
            'pincode' => $data['pincode'], 'chosen_date' => $data['chosen_date']]);

    }
    public function orders()
    {
        $list = \DB::table('order_details')->whereUserId(auth()->id())->get();
        // dd($list->toArray());
        return view('orders', compact('list'));
    }
    public function deleteRequest(Request $r)
    {
        if (count($r->all()) > 0) {
            $post = $r->validate([
                'email' => 'required|email',
                'phone' => 'required|numeric|digits:10',
            ]);

            \DB::table('info_delete_requests')->upsert([
                'email' => $post['email'],
                'phone' => $post['phone'],
            ], 'phone');
            \Session::flash('success', 'Account and related data Delete requests placed successfully,We will soon delete all you data ');
        }
        return view('data_delete_form');

    }
    public function order_success()
    {
        return view('frontend.order_success');
    }
    public function order_failed()
    {
        return view('frontend.order_failed');
    }
   
}
