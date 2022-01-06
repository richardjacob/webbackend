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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call('App\Http\Controllers\Api\CronController@requestCars')->everyMinute();
        $schedule->call('App\Http\Controllers\Api\CronController@updateCurrency')->daily();
        $schedule->call('App\Http\Controllers\Api\CronController@updateReferralStatus')->daily();
        $schedule->call('App\Http\Controllers\Api\CronController@updateOfflineUsers')->everyFifteenMinutes();
        //$schedule->call('App\Http\Controllers\Api\CronController@updatePaypalPayouts')->twiceDaily();
        $schedule->command('queue:work --tries=3 --once')->cron('* * * * *');
        $schedule->command('backup:run')->monthly();
        $schedule->command('backup:run --only-db')->daily();
        $schedule->command('backup:clean')->monthly();
        $schedule->command('telescope:prune')->daily();

        $schedule->call('App\Http\Controllers\Admin\SendmessageController@cron_jobs')->hourly();
        $schedule->call('App\Http\Controllers\Admin\EmailController@cron_jobs')->everyMinute();

        //daily at 12:01  
        //$schedule->call('App\Http\Controllers\Api\CronController@set_driver_online_time')->cron('1 0 * * *'); //->everyMinute();
        //daily at 12:02
        //$schedule->call('App\Http\Controllers\Api\CronController@set_driver_weekly_bonus')->cron('2 0 * * *'); //->everyMinute();

        $schedule->call('App\Http\Controllers\Api\CronController@adjust_driver_joining_bonus')->twiceDaily(1, 2);
        $schedule->call('App\Http\Controllers\Api\CronController@adjust_driver_referral_bonus')->twiceDaily(3, 4);
        $schedule->call('App\Http\Controllers\Api\CronController@set_driver_weekly_bonus_v2')->twiceDaily(5, 6);
        $schedule->call('App\Http\Controllers\Api\CronController@set_driver_online_time_v2')->cron('1 0 * * *'); // daily

        $schedule->call('App\Http\Controllers\Api\CronController@setOnlineUsersActivity')->cron('0,30 * * * *');
        
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
