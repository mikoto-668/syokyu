<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Breaktime;
use App\Models\Time;
use Carbon\Carbon;

class TimeController extends Controller
{
    public function index(Request $request)
    {
        $times = Time::all();
        return view('daily', ['times' => $times]);
    }
    public function daily(UserRequest $request)
    {
        $users = $request->all();
        $time= Time::find($request->user_id);
        return view('user', compact('users', 'time'));
    }
    //出勤アクション
    public function timein()
    {
    $user = Auth::user();
    $timeOut = Time::where('user_id',$user->id)->latest()->first();
     // ユーザーの最初の出勤記録が存在しない場合
    if(Time::where('user_id',$user->id)->doesntExist()){
    $timeIn = Time::create(['user_id'=>$user->id,
    'punchIn' => Carbon::now()]);
    return redirect('/');
    }
     // 最新の打刻があり、punchOutがまだ記録されていない場合
    elseif(is_null($timeOut->punchOut)){
    return redirect()->back()->with('error', '出勤退勤打刻にエラーが発生しています');
    }
    // 最新の出勤記録と現在の日付が異なる場合、もしくは退勤済みの場合
    else{
        $lastPunchInDate = Carbon::parse($timeOut->punchIn)->toDateString(); // 最新の出勤日
        $today = Carbon::now()->toDateString(); // 今日の日付

        // 日を跨いだかどうかを確認
        if ($lastPunchInDate !== $today) {
            // 日を跨いでいるので新しい出勤記録を作成
            $timeIn = Time::create([
                'user_id' => $user->id,
                'punchIn' => Carbon::now()
            ]);
    return redirect('/');
    }
    else {
    return redirect()->back()->with('error', 'すでに本日の出勤記録があります。');
    }
    }
    }

    private function timeStringToSeconds($breaktime) {
        $parts = explode(':', $breaktime);

        if (count($parts) == 3) {
            list($hours, $minutes, $seconds) = $parts;
            return $hours * 3600 + $minutes * 60 + $seconds;
        } elseif (count($parts) == 2) {
            list($minutes, $seconds) = $parts;
            return $minutes * 60 + $seconds;
        }

        return 0; // 無効な形式の場合
    }

    //退勤アクション
    public function timeOut() {
        $user = Auth::user();
        $time = Time::where('user_id',$user->id)->latest()->first();
        $breaks=BreakTime::where('time_id',$time->id)->get();
        $totalBreakTime = 0;
        foreach ($breaks as $break) {
        $breaktimeValue = $this->timeStringToSeconds($break->breaktime);
        $totalBreakTime += $breaktimeValue;};

        $hours = floor($totalBreakTime/ 3600);
        $minutes = floor(($totalBreakTime % 3600) / 60);
        $seconds = $totalBreakTime % 60;
        $breaktime = Carbon::now();
        $breaktime= sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        $timein=new Carbon($time->punchIn);
        $diffInSeconds= $timein->diffInSeconds(Carbon::now());
        $worktime=$diffInSeconds-$totalBreakTime;

        $hours = floor($worktime/ 3600);
        $minutes = floor(($worktime % 3600) / 60);
        $seconds = $worktime % 60;
        $totalworktime = Carbon::now();
        $totalworktime= sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        $time->update([
            'punchOut' => Carbon::now(),
            'breaktotal'=>$breaktime,
            'worktime'=>$totalworktime,
        ]);
        return redirect('/');
    }

}
    // //勤怠実績
    // public function performance() {
    //     $items = [];
    //     return view('time.performance',['items'=>$items]);
    // }
    // public function result(Request $request) {
    //     $user = Auth::user();
    //     $items = Time::where('user_id',$user->id)->where('year',$request->year)->where('month',$request->month)->get();
    //     return view('time.performance',['items'=>$items]);
    // }

    //日次勤怠
//     public function daily() {
//         $items = [];
//         return view('daily',['items'=>$items]);
//     }
//     // public function dailyResult(Request $request) {
//     //     $items = Time::where('year',$request->year)->where('month',$request->month)->where('day',$request->day)->get();
//     //     return view('time.daily',['items'=> $items]);
//     // }
// }