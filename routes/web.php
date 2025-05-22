<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home/trangchu');
});
Route::get('/', function () {
    return view('layouts/chinh');
});
Route::get('layouts/timkiemSP', function () {
    return view('layouts/timkiemSP');
});
