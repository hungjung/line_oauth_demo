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
Route::get('/linelogin', function(){
    // Ref https://developers.line.biz/en/docs/line-login/integrate-line-login/#making-an-authorization-request
    $query = http_build_query([
        'response_type' => "code",
        'client_id' => env("LOGIN_CLIENT_ID"),
        'redirect_uri' => env("LOGIN_CALLBACK"),
        'state' => date('Ymd'),
        'scope' => 'profile openid',
    ]);

    return redirect('https://access.line.me/oauth2/v2.1/authorize?'.$query);
});

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
