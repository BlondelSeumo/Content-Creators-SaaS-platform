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
        'App\Console\Commands\NpmInstall',
        'App\Console\Commands\CronClearCache',
        'App\Console\Commands\CronEmailUpcomingRenewals',
        'App\Console\Commands\CronEmailExpiringSubs',
        'App\Console\Commands\CronRenewSubscriptions',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Deleting cached views & cache once in a while, so shared hosting file quota won't go crazy
        $schedule->command('cron:clear_cache_files')->weekly();
        $schedule->command('cron:email_upcoming_renewals')->daily();
        $schedule->command('cron:email_expiring_subs')->daily();
        $schedule->command('cron:renew_subscriptions')->hourly();
        $schedule->command('cron:resetOffers')->daily();
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
