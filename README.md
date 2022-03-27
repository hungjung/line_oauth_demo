## 實作情境

* 實作使用者登入 (LINE Login)
* 實作使用者訂閱通知功能 (LINE Notify) (取得 Access Token 並儲存)
* 實作使用者訂閱成功頁面 (LINE Notify)
* 實作使用者取消訂閱功能 (LINE Notify) (撤銷 Access Token 才行)
* 實作後台發送推播訊息功能 (可以發送訊息給所有訂閱的人) (LINE Notify)

## 參考文件

* [Laravel Document (For 8.x)](https://laravel.com/docs/8.x)
* [Guzzle, PHP HTTP client](https://docs.guzzlephp.org/en/stable/index.html)
    > 此套件Laravel框架有內建，不需要額外安裝
* [Integrating LINE Login with your web app](https://developers.line.biz/en/docs/line-login/integrate-line-login/)
* [LINE Login v2.1 API reference](https://developers.line.biz/en/reference/line-login/)
* [LINE Notify API Document](https://notify-bot.line.me/doc/en/)

## 執行環境

* Web伺服器： Apache 2.4
* 程式語言： PHP 7.4
* MVC框架： Laravel 8

## 專案說明

* 程式進入點： `routes/web.php`
* view資料夾： `resource/views/`
* controller資料夾： `app/Http/controllers/`
* 使用 `guzzlehttp/guzzle` 套件實作請求呼叫的動作 (此套件Laravel框架有內建)

## 專案使用

* 先在專案目錄內
  * 指令 `composer install` 安裝所需要的套件庫。
  * 指令 `copy .env.example .env`，並在.env檔內編輯重要參數值。

* .env檔以下參數設定
  * APP_URL： 專案的主要url
  * LOGIN_CLIENT_ID： 實作line login的client id (channel id)
  * LOGIN_CLIENT_SECRET： 實作line login的client secret
  * LOGIN_CALLBACK： 實作line login要用到的redirect url
  * NOTIFY_CLIENT_ID： 實作line notify的client id
  * NOTIFY_CLIENT_SECRET： 實作line notify的client secret
  * NOTIFY_CALLBACK： 實作line notify要用到的redirect url

## 其他資源

* [上手 LINE Notify 不求人：一行代碼都不用寫的推播通知方法](https://blog.miniasp.com/post/2020/02/17/Go-Through-LINE-Notify-Without-Any-Code)
* [[PHP]簡易串接Line Notify](https://kira5033.github.io/2019/06/php%E7%B0%A1%E6%98%93%E4%B8%B2%E6%8E%A5line-notify/)
* [外國人實作的套件庫之一 phattarachai/line-notify](https://github.com/phattarachai/line-notify)
