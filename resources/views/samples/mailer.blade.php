<?php
use App\Util;
?>
@extends('layout')

@section('head')
    @component('parts.head')
        @slot('title')
            メール送信
        @endslot
    @endcomponent
@endsection

@section('contents')
    <h1>メール送信</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form method="post" action="" role="form">
        {{ csrf_field() }}
        <div class="card card-default">
            <div class="card-body">

                <div class="form-group">
                    <label class="control-label">送信元</label>
                    <input type="text" class="form-control" name="sender" placeholder="hoge@hoge.com"
                           value="{{ $sender }}">
                </div>
                <div class="form-group">
                    <label class="control-label">宛先</label>
                    <input type="text" class="form-control" name="receiptto" placeholder="hoge@hoge.com"
                           value="{{ $receiptto }}">
                </div>
                <div class="form-group">
                    <label class="control-label">件名</label>
                    <input type="text" class="form-control" name="subject" placeholder="件名"
                           value="{{ $subject }}">
                </div>
                <div class="form-group">
                    <label for="encode" class="control-label">本文</label>
                    <textarea class="form-control" rows="5" name="body"
                              placeholder="本文">{{ $body }}</textarea>
                </div>
                <div class="form-group center">
                    <button type="submit" class="btn btn-sm btn-primary">送信</button>
                </div>

            </div>
        </div>
    </form>

@endsection

@section('footer')
    @component('parts.footer')
        <script>
            $(function () {

            });
        </script>
    @endcomponent
@endsection
