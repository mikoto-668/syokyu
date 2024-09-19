<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Time;
use App\Models\User;
use Carbon\Carbon;

class TimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // ランダムな出勤時間を設定
        $punchIn = Carbon::now()->subHours(rand(1, 8));
        // ランダムな退勤時間を設定
        $punchOut = (clone $punchIn)->addHours(rand(1, 8));
        // 休憩時間を30分から2時間の間でランダムに設定
        $breakTotalMinutes = rand(30, 120);
        // 勤務時間から休憩時間を引く
        $worktimeMinutes = $punchOut->diffInMinutes($punchIn) - $breakTotalMinutes;

        // マイナスにならないように調整
        if ($worktimeMinutes < 0) {
            $worktimeMinutes = 0;
        }

        // 'worktime'を時間:分:秒の形式に変換
        $worktime = sprintf('%02d:%02d:%02d', floor($worktimeMinutes / 60), $worktimeMinutes % 60, 0);
        // 'breaktotal'を時間:分:秒の形式に変換
        $breakTotal = sprintf('%02d:%02d:%02d', floor($breakTotalMinutes / 60), $breakTotalMinutes % 60, 0);
        return [
            'user_id' => User::factory(),  // ユーザーを関連付ける
            'punchIn' => $punchIn,
            'breaktotal' => $breakTotal,  // 時間:分:秒の形式で保存
            'worktime' => $worktime,  // 時間:分:秒の形式で保存
            'punchOut' => $punchOut,
        ];
    }
}
