@extends('layouts.app')

@section('title', '訂閱通知')

@section('content')

<div class="row">

    <div class="col-lg-6">

        <!-- Circle Buttons -->
        <div class="card shadow mb-4">
            <div class="card-body">

            @if ($cnt)
                <h4 class="text-success">您已訂閱本服務通知！</h4>
            @else
                <p>是否申請訂閱本服務通知？</p>
                <div class="mt-4 mb-2">
                    <code>若想要申請，請點選下方按鈕連結！</code>
                </div>
                <a href="/notifyapp" class="btn btn-success btn-circle btn-lg">
                    <i class="fas fa-check"></i>
                </a>
            @endif

            </div>
        </div>
    </div>
</div>


@endsection
