<?php


use Illuminate\Support\Facades\Schedule;

Schedule::command('app:harvest-journals')->everyTenMinutes();

