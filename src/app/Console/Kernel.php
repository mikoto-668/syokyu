<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use App\Models\Time;
use App\Models\User;
use App\Models\BreakTime;
use App\Http\Controllers;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
        public function timeStringToSeconds($timeString)
        {
            // デフォルト値を設定して、配列の要素を確実に作成
            $parts = array_pad(explode(':', $timeString), 3, 0); // 必要な数の要素がない場合は0で埋める
            $hours = (int) $parts[0];
            $minutes = (int) $parts[1];
            $seconds = (int) $parts[2];
            return ($hours * 3600) + ($minutes * 60) + $seconds; // 秒に変換
        }
            public function schedule(Schedule $schedule)
            {
                $schedule->call(function () {
                    $now = Carbon::now();
            
                    // punchOut が NULL の Time レコードを取得
                    $timeRecords = Time::whereNull('punchOut')->get();
            
                    // user_id を基に該当するユーザーを取得
                    $users = User::whereIn('id', $timeRecords->pluck('user_id'))->get();
            
                    // ユーザーごとに処理を行う
                    foreach ($users as $user) {
                        // 各ユーザーのpunch_outがNULLのTimeレコードを取得
                        $timeRecord = $user->times()->whereNull('punchOut')->first();
            
                        if ($timeRecord) {
                            // ブレイクタイムの合計時間を計算
                            $breaks = BreakTime::where('time_id', $timeRecord->id)->get();
                            $totalBreakTime = 0;
            
                            foreach ($breaks as $break) {
                                $breaktimeValue = $this->timeStringToSeconds($break->breaktime);
                                $totalBreakTime += $breaktimeValue;
                            }
            
                            // 合計ブレイク時間をフォーマット
                            $hours = floor($totalBreakTime / 3600);
                            $minutes = floor(($totalBreakTime % 3600) / 60);
                            $seconds = $totalBreakTime % 60;
                            $breaktime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            
                            // 勤務時間を計算
                            $timeIn = new Carbon($timeRecord->punchIn);
                            $diffInSeconds = $timeIn->diffInSeconds($now);
                            $worktime = $diffInSeconds - $totalBreakTime;
            
                            // 勤務時間をフォーマット
                            $hours = floor($worktime / 3600);
                            $minutes = floor(($worktime % 3600) / 60);
                            $seconds = $worktime % 60;
                            $totalWorktime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            
                            // punch_out に 23:59:59 を設定し、worktime と breaktotal を更新
                            $timeRecord->update([
                                'punchOut' => $now->copy()->setTime(23, 59, 59),
                                'breaktotal' => $breaktime,
                                'worktime' => $totalWorktime,
                            ]);
            
                            // 次の日の 0:00:00 に新しいレコードを作成
                            Time::create([
                                'user_id' => $user->id,
                                'punchIn' => $now->copy()->addDay()->startOfDay(),
                            ]);
            
                            // ログにユーザー情報を出力
                            Log::info('Updated time record for user', [
                                'user_id' => $user->id,
                                'punchOut' => $timeRecord->punchOut,
                                'breaktotal' => $breaktime,
                                'worktime' => $totalWorktime,
                            ]);
                        }
                    }
                })->dailyAt('23:59:59');
            }
}
