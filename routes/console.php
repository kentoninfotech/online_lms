<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*|--------------------------------------------------------------------------
| Schedule Commands
|--------------------------------------------------------------------------
|
| Here you may define the scheduled commands for your application. These
| commands will be run on the schedule you specify in your console
| kernel. You may use the 'schedule' method to define them.
|
*/
// Called in bootstrap/app.php
// Schedule::command('lessons:generate-occurrences')->daily();
// Schedule::command('lessons:create-zoom-sessions')->daily();
// Schedule::command('zoom:sync-participants')->dailyAt('02:00');
