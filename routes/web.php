<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use \GuzzleHttp\Client as GuzzleClient;
use App\Http\Controllers\LoginController;

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
Route::get('/login', [LoginController::class, 'login']);
// line登入請求
Route::get('/linelogin', [LoginController::class, 'redirect']);
// line callback動作
Route::get('/callback', [LoginController::class, 'callback']);


Route::middleware(['userAuth'])->group(function(){
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

    // 登出動作
    Route::get('logout', [LoginController::class, 'logout']);
});
