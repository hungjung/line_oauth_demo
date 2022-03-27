<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \GuzzleHttp\Client as GuzzleClient;

class NotifyController extends Controller
{
    // 申請訂閱頁面
    public function subscribe() {
        return view('subscribe');
    }

    // 送出notify訂閱認證請求
    public function notifyapp() {

    }

    // notify callback url
    public function notifycallback() {

    }

    public function unsubscribe() {
        return view('unsubscribe');
    }

    public function sendout() {
        return view('sendout');
    }

}
