<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaderboardController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/game', function () {
    return view('game');
});

Route::get('/finished', function () {
    return view('finished');
});

Route::get('/leaderboard', function () {
    return view('leaderboard');
});

Route::post('/leaderboard/save', [LeaderboardController::class, 'save'])->name('leaderboard.save');
Route::get('/leaderboard/list', [LeaderboardController::class, 'list'])->name('leaderboard.list');
Route::get('/location', [LeaderboardController::class, 'location'])->name('leaderboard.location');
