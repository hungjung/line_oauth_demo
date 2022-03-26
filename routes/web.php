<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 登入頁
Route::get('/login', function () {
    return view('login');
});

// 登出動作
Route::redirect('/logout', '/login');

// line登入請求
Route::redirect('/linelogin', '/');
// line callback動作
Route::get('/callback', function () {
    echo "callback";
});

// 主頁
Route::get('/', function () {
    return view('blank');
});

// 訂閱通知
Route::get('/scribe', function () {
    return view('scribe');
});

// 取消訂閱
Route::get('/unscribe', function () {
    return view('unscribe');
});

// 發佈訊息
Route::get('/unscribe', function () {
    return view('unscribe');
});
