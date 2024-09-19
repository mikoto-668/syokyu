<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;
use App\Models\Time;
use App\Models\User;
use App\Models\BreakTime;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $this->updatePunchOutAndCreateNewRecord();
        })->dailyAt('23:59:59');
    }
    protected function updatePunchOutAndCreateNewRecord()
    {
        // 現在の日時を取得
        $now = Carbon::now();

        // punch_out が null の Time レコードを持つ全ユーザーを取得
        $users = User::whereHas('times', function($query) {
            $query->whereNull('punch_out');
        })->get();
    
        foreach ($users as $user) {
            // ユーザーのpunch_outがnullのTimeレコードを取得
            $timeRecord = $user->times()->whereNull('punch_out')->first();
    
            if ($timeRecord) {
                // ブレイクタイムの合計時間を計算
                $breaks = BreakTime::where('time_id', $timeRecord->id)->get();
                $totalBreakTime = 0;
                foreach ($breaks as $break) {
                    $breaktimeValue = $this->timeStringToSeconds($break->breaktime);
                    $totalBreakTime += $breaktimeValue;
                }
    
                $hours = floor($totalBreakTime / 3600);
                $minutes = floor(($totalBreakTime % 3600) / 60);
                $seconds = $totalBreakTime % 60;
                $breaktime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    
                // 勤務時間を計算
                $timeIn = new Carbon($timeRecord->punch_in);
                $diffInSeconds = $timeIn->diffInSeconds($now);
                $worktime = $diffInSeconds - $totalBreakTime;
    
                $hours = floor($worktime / 3600);
                $minutes = floor(($worktime % 3600) / 60);
                $seconds = $worktime % 60;
                $totalWorktime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    
                // punch_out に 23:59:59 を設定し、worktime と breaktotal を更新
                $timeRecord->update([
                    'punch_out' => $now->copy()->setTime(23, 59, 59),
                    'breaktotal' => $breaktime,
                    'worktime' => $totalWorktime,
                ]);
    
                // 次の日の 0:00:00 に新しいレコードを作成
                Time::create([
                    'user_id' => $user->id,
                    'punch_in' => $now->copy()->addDay()->startOfDay(),
                ]);
            }
        }
    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
