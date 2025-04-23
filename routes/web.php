<?php

use App\Http\Controllers\IdentifierController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IdentifierController::class, 'index']);
Route::get('/identifiers', [IdentifierController::class, 'results']);
