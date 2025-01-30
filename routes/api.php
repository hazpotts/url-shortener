<?php

use App\Http\Controllers\Api\UrlController;
use Illuminate\Support\Facades\Route;

Route::post('/encode', [UrlController::class, 'encode'])
    ->name('api.urls.encode');

Route::post('/decode', [UrlController::class, 'decode'])
    ->name('api.urls.decode');
