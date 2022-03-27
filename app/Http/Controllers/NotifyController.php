<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \GuzzleHttp\Client as GuzzleClient;

class NotifyController extends Controller
{
    // 申請訂閱頁面
    public function subscribe() {

        $get_subsribes = DB::select('select user_name,user_access_token,created_at from subscribe where user_name=?', [session("user_id")]);
        $cnt = count($get_subsribes);

        return view('subscribe', ["cnt" => $cnt]);
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
    public function notifycallback(Request $request, GuzzleClient $http) {
        // 取得 authorization code
        /**
         * 回傳以下資料
         * code
         * state
         */
        $code = $request->code;
        // 取得 state 和 csrf_token 比對
        if ($request->state !== csrf_token()) {
            return response('無效的操作！', 403);
        }

        // 透過所取得的code去取access token
        $token_url = "https://notify-bot.line.me/oauth/token";
        $form_data = [
            'form_params' => [
                'grant_type' => "authorization_code",
                'code' => $code,
                'redirect_uri' => env("NOTIFY_CALLBACK"),
                'client_id' => env("NOTIFY_CLIENT_ID"),
                'client_secret' => env("NOTIFY_CLIENT_SECRET")
            ],
            'http_errors' => false
        ];

        $response = $http->post($token_url, $form_data);

        if ($response->getStatusCode() != 200) {
            return view("/login", ['msg'=>$response->getBody()]);
        }

        // 成功取得access token資訊
        /**
         * 回傳以下資訊：
         * acces_token 永久有效
         */

        $payload = json_decode((string)$response->getBody(), true);

        DB::insert('insert into subscribe (user_name, user_access_token, created_at) values (?, ?, ?)', [session('user_id'), $payload['access_token'], Carbon::now()]);

        return redirect("/subscribe");

    }

    public function unsubscribe() {
        return view('unsubscribe');
    }

    public function sendout() {
        return view('sendout');
    }

}
