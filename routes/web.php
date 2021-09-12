<?php

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Route;
use SDU\MFA\Controllers\AuthenticationController;
use SDU\MFA\Controllers\SetupController;


Route::group(['prefix' => 'mfa', 'as' => 'sdu.mfa.', 'middleware' => 'web'], function ()
{
    Route::get('callback', [AuthenticationController::class, 'callback'])->name('callback');

    Route::get('forbidden', [AuthenticationController::class, 'forbidden'])->name('forbidden');

    //Route::get('setup', [SetupController::class, 'showSetup'])->name('setup');
});