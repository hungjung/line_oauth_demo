<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use \GuzzleHttp\Client as GuzzleClient;

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
    // 本機session確認
    if(session('user_name')){
        return redirect('/');
    }
    return view('login');
});

// line登入請求
Route::get('/linelogin', function(){
    // Ref https://developers.line.biz/en/docs/line-login/integrate-line-login/#making-an-authorization-request
    $authorize_url = "https://access.line.me/oauth2/v2.1/authorize";
    $query = http_build_query([
        'response_type' => "code",
        'client_id' => env("LOGIN_CLIENT_ID"),
        'redirect_uri' => env("LOGIN_CALLBACK"),
        'state' => csrf_token(),
        'scope' => 'profile openid',
    ]);

    return redirect($authorize_url.'?'.$query);
});

// line callback動作
Route::get('/callback', function (Request $request, GuzzleClient $http) {
    // Ref https://developers.line.biz/en/docs/line-login/integrate-line-login/#receiving-the-authorization-code-or-error-response-with-a-web-app
    /**
     * 回傳以下資料
     * code 所取得的code效期10分鐘
     * state
     */
    // dd($request);
    // 取得 authorization code
    $code = $request->code;
    // 取得 state 和 csrf_token 比對
    if ($request->state !== csrf_token()) {
        return response('無效的操作！', 403);
    }

    // dd($code);
    // 透過所取得的code去取access token
    $token_url = "https://api.line.me/oauth2/v2.1/token";
    $form_data = [
        'form_params' => [
            'grant_type' => "authorization_code",
            'code' => $code,
            'redirect_uri' => env("LOGIN_CALLBACK"),
            'client_id' => env("LOGIN_CLIENT_ID"),
            'client_secret' => env("LOGIN_CLIENT_SECRET")
        ],
        'http_errors' => false
    ];

    $response = $http->post($token_url, $form_data);

    if ($response->getStatusCode() != 200) {
        return view("/login", ['msg'=>$response->getBody()]);
    }

    // 成功取得access token資訊
    // Ref https://developers.line.biz/en/docs/line-login/integrate-line-login/#response
    /**
     * 回傳以下資訊：
     * acces_token 有效期30天
     * token_type => "Bearer"
     * refresh_token
     * expires_in => 2592000
     * scope
     * id_token
     */

    $payload = json_decode((string)$response->getBody(), true);
    // dd($payload);

    // 取使用者Data
    // Ref https://developers.line.biz/en/reference/line-login/#get-user-profile
    /**
     * 回傳以下資訊：
     * userId 使用者ID
     * displayName 名字
     * pictureUrl 大頭貼
     * statusMessage 狀態
     */
    $verify_url = 'https://api.line.me/v2/profile';
    $post_data = [
        'headers' => [
            'Authorization' => $payload['token_type'].' '.$payload['access_token'],
        ],
        'http_errors' => false
    ];
    $response_verify = $http->post($verify_url, $post_data);

    if ($response_verify->getStatusCode() != 200) {
        return redirect("/login")->with(["msg" => $response_verify->getBody()]);
    }

    $profile = json_decode((string)$response_verify->getBody(), true);

    // 存使用者資訊
    session([
        'token'=>$payload['access_token'],
        'refresh_token'=>$payload['refresh_token'],
        'user_id'=>$profile['userId'],
        'user_name'=>$profile['displayName'],
        'user_pic'=>$profile['pictureUrl']
    ]);

    return redirect('/');

});

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
    Route::get('logout', function(Request $request) {
        // 刪本機session
        session()->flush();
        session()->regenerate();
        // 尚缺註銷 access token 的流程
        return redirect("/");
    });
});
