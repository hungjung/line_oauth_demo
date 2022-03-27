@extends('layouts.app')

@section('title', '發送訊息')

@section('content')

<div class="row">

    <div class="col-lg-6">

        <!-- Circle Buttons -->
        <div class="card shadow mb-4">
            <div class="card-body">

            @if (session('sended') && session('sended')==1)
                <h4 class="text-warning">送訊成功 (總用戶數 {{ session('cnt') }} 人，共送出 {{ session('done') }} 人) </h4>
            @elseif ($cnt<=0)
                <h4 class="text-danger">目前沒有人訂閱，無法發送訊息！</h4>
            @else
            <form action="/sendout" method="post">
                @csrf
                <p>(目前總用戶數 {{ $cnt }} 人)</p>
                <p>請填入要發送的訊息內容</p>
                <div class="mt-4 mb-2">
                    <textarea class="form-control" id="msg_text" name="msg_text" rows="3"></textarea>
                </div>
                <button href="/notifyrevoke" class="btn btn-success btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="fas fa-check"></i>
                    </span>
                    <span class="text">發送訊息</span>
                </button>
            </form>
                @if (session('sended') && session('sended')==2)
                    <p>&nbsp;</p>
                    <h4 class="text-warning">送訊失敗，請重試！</h4>
                    <p>(總用戶數 {{ session('cnt') }} 人，共送出 {{ session('done') }} 人)</p>
                @endif
            @endif

            </div>
        </div>
    </div>
</div>

@endsection
