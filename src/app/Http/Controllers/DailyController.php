<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Time;
use Carbon\Carbon;

class DailyController extends Controller
{
    /**
     * 日付ごとの勤務データを表示するメソッド
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        // 指定された日付、またはデフォルトで現在の日付を取得
        $date = $request->input('date', Carbon::now()->toDateString());
    
        // 指定された日付のデータを1ページに5件表示し、ページネーションリンクにdateを追加
        $times = Time::whereDate('punchIn', $date)
            ->with('user')
            ->paginate(5)
            ->appends(['date' => $date]); // これにより、リンクに日付パラメータが追加されます
    
        return view('daily', compact('times', 'date'));
    }
}