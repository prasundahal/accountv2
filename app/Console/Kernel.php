<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        Commands\SpinnerResetForm::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //  $schedule->command('colabUpdate:cron')->everyMinute();
         $schedule->command('colabUpdate:cron')
         ->everyMinute();
         $schedule->command('DailyReport:cron')
         ->everyMinute();
         $schedule->command('SpinnerResetForm:cron')
         ->everyMinute();
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
