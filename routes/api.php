<?php

use App\Http\Controllers\IdentifierController;
use Illuminate\Support\Facades\Route;

Route::controller(IdentifierController::class)->prefix('identifier')->group(function () {
    Route::get('/', 'show')->name('identifier.show');
    Route::post('/', 'find')->name('identifier.find');
});
