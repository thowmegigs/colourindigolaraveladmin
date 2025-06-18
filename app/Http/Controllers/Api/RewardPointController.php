<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;
class RewardPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $user_id=\Auth::guard('api')->user()->id;
        $list = \DB::table('points_history')->whereUserId($user_id)->whereStatus('Completed')
        ->get();
       return response()->json(['data' => $list], 200);

    }
    public function total_point()
    {
       $user_id=\Auth::guard('api')->user()->id;
        $credited_amount = \DB::table('points_history')->whereUserId($user_id)->whereStatus('Completed')->whereMode('Credit')
        ->sum('points');
        $debited_amount = \DB::table('points_history')->whereUserId($user_id)->whereMode('Debit')
        ->sum('points');
        $amount=$credited_amount-$debited_amount;
        
       return response()->json(['data' =>$amount>0?$amount:0.0], 200);

    }

}
