<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    BlowFish暗号(CBC/PKCS7Padding)
@endsection

@section('contents')
    <h1>BlowFish暗号(CBC/PKCS7Padding)</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form name="form1" method="post" action=""  class="form-horizontal" role="form">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">秘密鍵</div>
            <div class="card-body">
                <input type="text" class="form-control" name="key_hex" placeholder="秘密鍵16進数文字列" value="{{ $key_hex }}">
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">暗号</div>
            <div class="card-body">

                <div class="form-group">
                    <label for="input" class="control-label">入力</label>
                    <input type="text" class="form-control" name="input" placeholder="暗号元を入力" value="{{ $input }}">
                </div>

                @if ($encryptedHex)
                <div class="card">
                    <div class="card-header">
                        結果
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="control-label">IV</label>
                            <input type="text" class="form-control" value="{{ $ivHex }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">結果(16進数文字列表記)</label>
                            <textarea class="form-control" rows="3">{{ $encryptedHex }}</textarea>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">復号</div>
            <div class="card-body">

                <div class="form-group">
                    <label for="iv4decode_hex" class="control-label">IV</label>
                    <input type="text" class="form-control" name="iv4decode_hex" placeholder="IVを入力" value="{{ $iv4decode_hex }}">
                </div>
                <div class="form-group">
                    <label for="input4decode_hex" class="control-label">暗号値</label>
                    <textarea class="form-control" name="input4decode_hex" rows="3">{{ $input4decode_hex }}</textarea>
                </div>

                @if ($decoded)
                <div class="card">
                    <div class="card-header">結果</div>
                    <div class="card-body">
                        {{ $decoded }}
                    </div>
                </div>
                @endif

            </div>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary">実行</button>
        </div>

    </form>

@endsection
