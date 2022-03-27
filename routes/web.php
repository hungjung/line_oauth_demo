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

    // 訂閱通知頁面
    Route::get('/subscribe', [NotifyController::class, 'subscribe']);
    // 送出申請訂閱的請求
    Route::get('/notifyapp', [NotifyController::class, 'notifyapp']);
    // notifycallback
    Route::get('/notifycallback', [NotifyController::class, 'notifycallback']);

    // 取消訂閱
    Route::get('/unsubscribe', [NotifyController::class, 'unsubscribe']);
    // 送出取消訂閱的請求
    Route::get('/notifyrevoke', [NotifyController::class, 'notifyrevoke']);

    // 發佈訊息
    Route::get('/sendout', [NotifyController::class, 'sendout']);

    // 登出動作
    Route::get('logout', [LoginController::class, 'logout']);
});
