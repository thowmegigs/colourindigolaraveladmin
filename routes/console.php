<?php


use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CompleteVendorOrders;

Schedule::command(CompleteVendorOrders::class)
    ->dailyAt('12:00')
    ->timezone('Asia/Kolkata'); // Optional – specify your timezone