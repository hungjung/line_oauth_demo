<?php

namespace App\Http\Middleware;

use Closure;
use \GuzzleHttp\Client as GuzzleClient;

class UserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 本機session確認
        if(!session('user_name')){
            return redirect('/login');
        }

        // 確認access_token是否失效
        $verify_url = 'https://api.line.me/oauth2/v2.1/verify';
        $query = [
            'query' => [
                'access_token'=> session("access_token")
            ],
            'http_errors' => false
        ];

        $http = new GuzzleClient();
        $response = $http->get($verify_url, $query);

        if ($response->getStatusCode() != 200) {
            session()->flush();
            session()->regenerate();
            return view("/login", ['msg'=>"登入逾時，請重新登入！"]);
        }

        return $next($request);
    }
}
