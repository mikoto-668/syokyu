<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Breaktime;
use App\Models\Time;
use Carbon\Carbon;

class BreaktimeController extends Controller
{
   //休憩開始アクション
    public function breakIn() {
    $user = Auth::user();
    $time = Time::where('user_id',$user->id)->latest()->first();
    $breakIn = Breaktime::create([
        'breakIn' => Carbon::now(),
        'time_id' => $time->id
    ]);
    return redirect('/');
    }
//休憩終了アクション
    public function breakOut() {
    $user = Auth::user();
    $time = Time::where('user_id',$user->id)->latest()->first();
    $breaktime =BreakTime::where('time_id',$time->id)->latest()->first();
    $breakIn=new Carbon($breaktime->breakIn);
    $breakOut=Carbon::now();
    $diffInSeconds=$breakIn->diffInSeconds($breakOut);
    $hours = floor($diffInSeconds / 3600);
    $minutes = floor(($diffInSeconds % 3600) / 60);
    $seconds = $diffInSeconds % 60;

 // 時間:分:秒 の形式に変換
    $breaktimes = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    $breaktime->update(['breakOut'=> $breakOut,
                        'breaktime'=>$breaktimes]);
    return redirect('/');
}

// public function timeOut() {
//     $user = Auth::user();
//     $timeOut = Time::where('user_id',$user->id)->latest()->first();
//     $timeOut->update([
//         'punchOut' => Carbon::now(),
//     ]);
//     return redirect('/');
// }

// latest()->








}


