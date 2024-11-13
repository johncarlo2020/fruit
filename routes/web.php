<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/game', function () {
    return view('game');
});

Route::get('/finished', function () {
    return view('finished');
});
