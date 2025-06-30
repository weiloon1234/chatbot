<?php

use Illuminate\Support\Facades\Route;

Route::any('{path}', [\App\Http\Controllers\AppController::class, 'getApp'])
    ->where('path', '^(?!(api|build|img|audio|icons|fonts|demo|scripts|sdk|vendor|test|broadcasting|telescope|cb)).*$');

Route::group(['prefix' => 'cb', 'namespace' => 'CallBack'], function () {
    Route::any('wwebjs', [\App\Http\Controllers\CallBack\WWebJsController::class, 'callBack']);
});
