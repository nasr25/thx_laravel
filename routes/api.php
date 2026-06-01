<?php

use App\Http\Controllers\API\PublicController;
use Illuminate\Support\Facades\Route;

// This application is server-rendered with Blade (see routes/web.php).
// The JSON API surface has been retired; only a health check remains.
Route::get('/ping', [PublicController::class, 'ping']);
