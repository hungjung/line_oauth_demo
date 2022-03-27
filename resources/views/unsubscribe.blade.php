@extends('layouts.app')

@section('title', '取消訂閱')

@section('content')

<div class="row">

    <div class="col-lg-6">

        <!-- Circle Buttons -->
        <div class="card shadow mb-4">
            <div class="card-body">

            @if (session('delete') && session('delete')==1)
                <h4 class="text-warning">取消訂閱成功！</h4>
            @elseif ($cnt<=0)
                <h4 class="text-danger">您尚未訂閱本服務通知！</h4>
            @else
                <p>是否取消訂閱本服務通知？</p>
                <div class="mt-4 mb-2">
                    <code>若想要取消，請點選下方按鈕連結！</code>
                </div>
                <a href="/notifyrevoke" class="btn btn-danger btn-circle btn-lg">
                    <i class="fas fa-trash"></i>
                </a>

                @if (session('delete') && session('delete')==2)
                    <h4 class="text-warning">取消訂閱失敗，請重試！</h4>
                @endif
            @endif

            </div>
        </div>
    </div>
</div>

@endsection
