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

    // 取消訂閱頁面
    public function unsubscribe() {

        $get_subsribes = DB::select('select user_name,user_access_token,created_at from subscribe where user_name=?', [session("user_id")]);
        $cnt = count($get_subsribes);

        return view('unsubscribe', ["cnt" => $cnt]);
    }

    // 送出取消訂閱的請求
    public function notifyrevoke(GuzzleClient $http) {

        $select_sql = 'select user_name,user_access_token,created_at from subscribe where user_name=?';
        $get_subsribes = DB::select($select_sql, [session("user_id")]);
        $cnt = count($get_subsribes);

        if ($cnt<=0) {
            return response("無效操作", 403);
        }

        $user_access_token = $get_subsribes[0]->user_access_token;

        // 實作註銷 line login access token 的流程
        $revoke_url = 'https://notify-api.line.me/api/revoke';
        $form_data = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$user_access_token,
            ],
            'http_errors' => false
        ];

        $revoke_response = $http->post($revoke_url, $form_data);
        $revoke = json_decode((string)$revoke_response->getBody(), true);

        if ($revoke['status']==200) {
            DB::delete('delete from subscribe where user_name=?', [session('user_id')]);
            session()->flash("delete", 1);
            return redirect('unsubscribe');
        } else {
            session()->flash("delete", 2);
            return redirect('unsubscribe');
        }
    }

    public function sendout() {
        return view('sendout');
    }

}
