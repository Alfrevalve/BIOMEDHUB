<?php

namespace App\Console;

use App\Console\Commands\EnviarDigestOperativo;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(EnviarDigestOperativo::class)
            ->dailyAt('08:00')
            ->timezone('America/Lima')
            ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
