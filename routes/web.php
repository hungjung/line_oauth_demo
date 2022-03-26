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
    // 取得 state (暫時用不到)
    $state = $request->state;

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
    dd($payload);

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
