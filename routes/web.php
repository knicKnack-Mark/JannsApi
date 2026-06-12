<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/run-migrations-now', function () {
    Artisan::call('migrate', ['--force' => true]);

    return nl2br(Artisan::output());
});

Route::get('/', function () {
    return view('welcome');
});
