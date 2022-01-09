<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\createxerotoken;
use App\Console\Commands\xerocreateinvoice;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\RefreshDb::class,
        createxerotoken::class,
        xerocreateinvoice::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('createxerotoken')->everyFifteenMinutes();
         $schedule->command('xerocreateinvoice')->everyFifteenMinutes();

    }
}
