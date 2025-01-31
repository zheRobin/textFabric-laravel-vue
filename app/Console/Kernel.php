<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('queue:prune-failed --hours=120')->daily();
        $schedule->command('queue:prune-batches --hours=120')->daily();

        /*
         * TODO we don't need this pruning of exports table
         *   @see For discussion see https://3.basecamp.com/4934500/buckets/32019090/card_tables/cards/6513753007#__recording_6556643195
         */
        // $schedule->command('export:prune-batches')->cron('* * * * *');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
