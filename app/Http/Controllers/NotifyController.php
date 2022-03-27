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
        $authorize_url = "https://notify-bot.line.me/oauth/authorize";
        $query = http_build_query([
            'response_type' => "code",
            'client_id' => env("NOTIFY_CLIENT_ID"),
            'redirect_uri' => env("NOTIFY_CALLBACK"),
            'state' => csrf_token(),
            'scope' => 'notify',
        ]);

        return redirect($authorize_url.'?'.$query);
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
