<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignalementController;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware('auth')->post('/signalements/suggestion-ia', [SignalementController::class, 'suggestionIA'])
    ->name('signalements.suggestion-ia');