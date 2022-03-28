<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \GuzzleHttp\Client as GuzzleClient;
use Firebase\JWT\JWT;
use Firebase\JWT\KEY;

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

        $now = Carbon::now()->timestamp;
        $key = env("JWT_SECRET");
        $payload = [
            "iss" => env("APP_URL"),
            "name" => session('user_id'),
            "iat" => $now,
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        $authorize_url = "https://notify-bot.line.me/oauth/authorize";
        $query = http_build_query([
            'response_type' => "code",
            'client_id' => env("NOTIFY_CLIENT_ID"),
            'redirect_uri' => env("NOTIFY_CALLBACK"),
            'state' => $jwt,
            'scope' => 'notify',
            'response_mode' => 'form_post'
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
        // $user_id 取得自 state
        $key = env("JWT_SECRET");
        $decoded = JWT::decode($request->state, new Key($key, 'HS256'));
        $user_id = $decoded->name;

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
            return response($response->getBody()["error_description"], $response->getStatusCode());
        }

        // 成功取得access token資訊
        /**
         * 回傳以下資訊：
         * acces_token 永久有效
         */

        $payload = json_decode((string)$response->getBody(), true);

        DB::insert('insert into subscribe (user_name, user_access_token, created_at) values (?, ?, ?)', [$user_id, $payload['access_token'], Carbon::now("Asia/Taipei")]);

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

    // 發佈訊息的頁面
    public function sendout() {

        $select_sql = 'select user_name,user_access_token,created_at from subscribe';
        $get_subsribes = DB::select($select_sql);
        $cnt = count($get_subsribes);

        return view('sendout', ["cnt"=>$cnt]);
    }

    // 發佈訊息
    public function message(Request $request, GuzzleClient $http) {
        // 收到的的訊息是 $request->msg_text;

        // 先找訂閱戶
        $select_sql = 'select user_name,user_access_token,created_at from subscribe';
        $get_subscribes = DB::select($select_sql);
        $cnt = count($get_subscribes);

        // 實作送訊息
        $done = 0;
        foreach ($get_subscribes as $value) {
            $user_access_token = $value->user_access_token;
            $post_url = 'https://notify-api.line.me/api/notify';
            $post_data = [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$user_access_token,
                ],
                'form_params' => [
                    'message' => $request->msg_text
                ],
                'http_errors' => false
            ];

            $post_response = $http->post($post_url, $post_data);
            $post_result = json_decode((string)$post_response->getBody(), true);

            // dump($post_result);
            // 成功者計數+1
            if ($post_result['status'] == 200)
                $done++;

            // TODO: 可做稽核紀錄
        }

        if ($done>0) {
            session()->flash("sended", 1);
        } else {
            session()->flash("sended", 2);
        }
        session()->flash("cnt", $cnt);
        session()->flash("done", $done);

        return redirect("/sendout");

    }

}
