<?php

use App\Console\Commands\ImportOuis;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ImportOuis::class)->dailyAt('03:00');
