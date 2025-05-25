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
Route::get('layouts/chitiet', function () {
    return view('layouts/chitiet');
});
Route::get('/dangnhap', function () {
    return view('layouts.dangnhap');
});
Route::get('/dangky', function () {
    return view('layouts.dangky');
});
Route::get('/quenmatkhau', function () {
    return view('layouts.quenmatkhau');
});
