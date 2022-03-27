@extends('layouts.app')

@section('title', '主頁')

@section('content')
    <p>歡迎登入服務！</p>
    <hr>
    <p>目前本服務已訂閱通知人數有 {{ count($subscribe_list) }} 人。</p>
@endsection
