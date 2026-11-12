<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'iRACK IT Solution RESTful API Service',
        'health' => '/api/v1/health',
    ]);
});
