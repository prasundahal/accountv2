<?php

namespace App\Console;

use App\Models\GeneralSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ColabUpdate::class,
        Commands\DailyReport::class,
        Commands\SpinnerResetForm::class,
        Commands\MonthlyTasks::class,
        Commands\SendMailToBetween::class,
        Commands\SpinnerMailToAboveLimit::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $setting = GeneralSetting::first();      
        
        //  $schedule->command('colabUpdate:cron')->everyMinute();
        //  $schedule->command('DailyReport:cron')->everyMinute();
        // Log::channel('cronLog')->info(date('H:i'));
         $schedule->command('colabUpdate:cron')
         ->daily();
         $schedule->command('DailyReport:cron')
         ->daily();
         $schedule->command('SpinnerResetForm:cron')
         ->monthlyOn(26, '00:00');
         $schedule->command('sendMailToBetween:cron')
         ->monthlyOn(20, '00:00');
         $schedule->command('SpinnerMailToAboveLimit:cron')
         ->monthlyOn(5, '00:00');
         $schedule->command('MonthlyTasks:cron')
         ->monthlyOn(26, '00:00');
         $schedule->command('SpinnerWinnerCron:cron')
         ->monthlyOn($setting->spinner_winner_day,date('H:i',strtotime($setting->spinner_time_cron)));
        
         $type = $setting->inactive_mail_type;
         if($setting->inactive_mail_type == 'dailyAt' || $setting->inactive_mail_type == 'lastDayOfMonth'){
            $schedule->command('InactiveMail:cron')->$type($setting->inactive_mail_time);            
         }elseif($setting->inactive_mail_type == 'everyMinute'){
            $schedule->command('InactiveMail:cron')->everyMinute();
        }else{
            $schedule->command('InactiveMail:cron')->$type($setting->inactive_mail_day,date('H:i',strtotime($setting->inactive_mail_time)));
         }
        // $schedule->command('MonthlyTasks:cron')
        // ->everyMinute();
        //  daily
        //  ->appendOutputTo($filePath)
        //  ->withoutOverlapping();
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
