<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    PHP Serialize
@endsection

@section('contents')
    <h1>PHP Serialize</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form name="form1" method="post" action="{{ URL::current() }}">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">入力</div>
            <div class="card-body">
                <div class="form-group">
                    <textarea class="form-control" rows="5" name="input" placeholder="シリアライズ文字列">{{ $input }}</textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-sm btn-primary">表示</button>
                </div>
            </div>
        </div>

        @if ($parsed)
        <div class="card mt-3">
            <div class="card-header">デコード結果</div>
            <div class="card-body">
                <pre>{{ print_r($parsed, true) }}</pre>
            </div>
        </div>
        @endif

        <div class="card mt-3">
            <div class="card-header">sample</div>
            <div class="card-body">
                <pre>{{ serialize($sample) }}</pre>
            </div>
        </div>

    </form>
@endsection
