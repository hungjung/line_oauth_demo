<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifyController extends Controller
{
    //
    public function subscribe() {
        return view('subscribe');
    }

    public function unsubscribe() {
        return view('unsubscribe');
    }

    public function sendout() {
        return view('sendout');
    }

}
