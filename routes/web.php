<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use \GuzzleHttp\Client as GuzzleClient;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotifyController;

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
    Route::get('/', [LoginController::class, 'index']);

    // 訂閱通知
    Route::get('/subscribe', [NotifyController::class, 'subscribe']);
    // 取消訂閱
    Route::get('/unsubscribe', [NotifyController::class, 'unsubscribe']);
    // 發佈訊息
    Route::get('/sendout', [NotifyController::class, 'sendout']);

    // 登出動作
    Route::get('logout', [LoginController::class, 'logout']);
});
