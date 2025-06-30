<?php

use Illuminate\Support\Facades\Route;

Route::post('/settings', [\App\Http\Controllers\Api\User\SettingController::class, 'fetch']);

Route::post('/logout', [\App\Http\Controllers\Api\User\AccountController::class, 'logout']);
Route::post('/login', [\App\Http\Controllers\Api\User\AccountController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/me', [\App\Http\Controllers\Api\User\AccountController::class, 'fetch']);
    Route::post('/change_language', [\App\Http\Controllers\Api\User\AccountController::class, 'changeLanguage']);
    Route::post('/profile_submit', [\App\Http\Controllers\Api\User\AccountController::class, 'profileSubmit']);
});
