<?php

use Illuminate\Console\Scheduling\Schedule;

class Kernel extends Kernel
{
        protected function schedule(Schedule $schedule)
{
    $schedule->command('backup:run')->weekly();
    }
}



