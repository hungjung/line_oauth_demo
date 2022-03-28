## 實作情境

* 實作使用者登入 (LINE Login)： 主要實作在 `app/Http/Controller/LoginController.php`
  <a href="https://imgur.com/s6dJZPs"><img src="https://i.imgur.com/s6dJZPs.png" title="source: imgur.com" /></a>
  
* 實作Notify訂閱服務 (LINE Notify)： 主要實作在 `app/Http/Controller/NotifyController.php`
  <a href="https://imgur.com/cYBPtpp"><img src="https://i.imgur.com/cYBPtpp.png" title="source: imgur.com" /></a>
  * 實作使用者訂閱通知功能 (取得 Access Token 並儲存)
  * 實作使用者訂閱成功頁面
  * 實作使用者取消訂閱功能 (撤銷 Access Token 才行)
  * 實作後台發送推播訊息功能 (可以發送訊息給所有訂閱的人)

## 參考文件

* [Laravel Document (For 8.x)](https://laravel.com/docs/8.x)
* [Guzzle, PHP HTTP client](https://docs.guzzlephp.org/en/stable/index.html)
    > 此套件Laravel框架有內建，不需要額外安裝<br>
    > 額外安裝的套件： [firebase/php-jwt](https://github.com/firebase/php-jwt)
* [Integrating LINE Login with your web app](https://developers.line.biz/en/docs/line-login/integrate-line-login/)
* [LINE Login v2.1 API reference](https://developers.line.biz/en/reference/line-login/)
    > 目前LINE官方提供LINE Login V2.1，基於OAuth2.0和OpenID Connect協定。 <br>
    > authorize code 有效期： 10分鐘<br>
    > access token 有效期： 30天<br>
    > refresh token 有效期： 90天
* [LINE Notify API Document](https://notify-bot.line.me/doc/en/)
    > 個人的 access token 永久有效<br>
    > 一小時內單一 server 單一 access token 呼叫 api 次數上限是1000次

## 執行環境

* Web伺服器： Apache 2.4
* 程式語言： PHP 7.4
* MVC框架： [Laravel 8](https://laravel.com/docs/8.x)
* 專案樣版取材自 [[Start Bootstrap - SB Admin 2](https://startbootstrap.com/theme/sb-admin-2)]

## 專案說明

* 程式進入點： `routes/web.php`
  > 為了接受 line notify api 回傳的 post 請求，將接收此請求的路由放置於 `routes/api.php` (為了避開Laravel內建的CSRF保護)。
* view資料夾： `resource/views/`
* controller資料夾： `app/Http/controllers/`
* 資料表schema： `databse/migrations/`
* 使用 `guzzlehttp/guzzle` 套件實作請求呼叫的動作 (此套件Laravel框架有內建)

## 專案使用

* 先在專案目錄內
  * 指令 `composer install` 安裝所需要的套件庫。
  * 先準備好mysql資料庫
    ```sql=
    CREATE USER '使用者帳號'@'%' IDENTIFIED BY '使用者密碼';
    CREATE DATABASE IF NOT EXISTS 資料庫名稱 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    GRANT SELECT, INSERT, CREATE, ALTER, DROP, LOCK TABLES, CREATE TEMPORARY TABLES, DELETE, UPDATE, EXECUTE ON 資料庫名稱 .* TO '使用者帳號'@'%';
    ```
  * 指令 `copy .env.example .env`，並在.env檔內編輯重要參數值。
  * 指令 `php artisan migrate` ，會依據 `database/migrations/` 的內容，把資料表建置起來 (或可以直接參考下列建表指令)。
    ```sql=
    CREATE TABLE `subscribe` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_name` VARCHAR(200) NOT NULL COLLATE 'utf8mb4_unicode_ci',
        `user_access_token` VARCHAR(500) NOT NULL COLLATE 'utf8mb4_unicode_ci',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    )
    COLLATE='utf8mb4_unicode_ci'
    ENGINE=InnoDB
    ;
    ```

* Laravel 網頁根目錄設定在 `public`。
    > [Laravel Directory Structure](https://laravel.com/docs/8.x/structure)

* .env檔以下參數設定
  * APP_URL： 專案的主要url
  * LOGIN_CLIENT_ID： 實作line login的client id (channel id)
  * LOGIN_CLIENT_SECRET： 實作line login的client secret
  * LOGIN_CALLBACK： 實作line login要用到的redirect url
  * NOTIFY_CLIENT_ID： 實作line notify的client id
  * NOTIFY_CLIENT_SECRET： 實作line notify的client secret
  * NOTIFY_CALLBACK： 實作line notify要用到的redirect url
  * DB_HOST： 資料庫主機位址
  * DB_PORT： 資料庫連結埠號(MySQL預設3306)
  * DB_DATABASE： 資料庫名稱
  * DB_USERNAME： 資料庫帳號
  * DB_PASSWORD： 資料庫密碼
  * JWT_SECRET： 傳Notify訂閱時，將使用者識別碼(user_id)加密為jwt token，再透過state變數傳遞資訊，JWT_SECRET是用來加解密用的key，本專案使用的HS256 Hashing演算法
    > [How to generate key pair for php-jwt?](https://stackoverflow.com/questions/51327584/how-to-generate-key-pair-for-php-jwt)<br>
    > [JWT](https://jwt.io/) 是基於 JSON 的開放標準 ([RFC 7519](https://datatracker.ietf.org/doc/html/rfc7519)) <br>
    > 若是額外安裝 [`tymon/jwt-auth`](https://packagist.org/packages/tymon/jwt-auth) 套件，可於專案內執行 `php artisan jwt:secret` 指令，以便產生實作jwt token所需要的key ([參照文件](https://jwt-auth.readthedocs.io/en/develop/laravel-installation/#generate-secret-key))

## 其他資源

* [上手 LINE Notify 不求人：一行代碼都不用寫的推播通知方法](https://blog.miniasp.com/post/2020/02/17/Go-Through-LINE-Notify-Without-Any-Code)
* [[PHP]簡易串接Line Notify](https://kira5033.github.io/2019/06/php%E7%B0%A1%E6%98%93%E4%B8%B2%E6%8E%A5line-notify/)
* [LINE Notify 初嚐心得](https://jackkuo-tw.medium.com/line-notify-%E5%88%9D%E5%9A%90%E5%BF%83%E5%BE%97-7ea0292907c6)
* [LINE Notify：用 Google Apps Script 建立簡易網站監測機器人](https://www.letswrite.tw/line-notify-gas/)
* [台中區網中心：Line Bot應用與分享](https://www.tcrc.edu.tw/set/new-list/linebot)
* [外國人實作的套件庫之一 phattarachai/line-notify](https://github.com/phattarachai/line-notify)
* [第 11 屆 iThome 鐵人賽：LINE bot 好好玩 30 天玩轉 LINE API 系列](https://ithelp.ithome.com.tw/users/20117701/ironman/2634)
* [HENNGE Taiwan 部落格 - OpenID Connect 是什麼？](https://hennge.com/tw/blog/what-is-openid-connect.html)
* [OKTA - The Client ID and Secret](https://www.oauth.com/oauth2-servers/client-registration/client-id-secret/)
* [各大網站 OAuth 2.0 實作差異](https://blog.yorkxin.org/posts/oauth2-implementation-differences-among-famous-sites.html)
