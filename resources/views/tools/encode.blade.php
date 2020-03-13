<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    URL Encode/Decode
@endsection

@section('contents')
    <h1>URL Encode/Decode</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form name="form1" method="post" action="{{ URL::current() }}" class="form-horizontal" role="form">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">Input</div>
            <div class="card-body">

                <div class="form-group">
                    <label for="encoding" class="control-label">Encoding</label>
                    <input type="text" class="form-control" name="encoding" placeholder="エンコーディング" value="{{ $encoding }}">
                </div>
                <div class="form-group">
                    <label for="input_plain" class="control-label">Plain text</label>
                    <textarea class="form-control" rows="5" name="input_plain" placeholder="エンコード対象文字列">{{ $input_plain }}</textarea>
                </div>
                <div class="form-group">
                    <label for="input_encoded" class="control-label">Encoded</label>
                    <textarea class="form-control" rows="5" name="input_encoded" placeholder="デコード対象文字列">{{ $input_encoded }}</textarea>
                </div>
                <div class="form-group center">
                    <button type="submit" class="btn btn-sm btn-primary">表示</button>
                </div>

            </div>
        </div>

        @if ($encoded)
        <div class="card mt-3">
            <div class="card-header">Encoded</div>
            <div class="card-body">
                {!! nl2br(e($encoded)) !!}
            </div>
        </div>
        @endif

        @if ($decoded)
            <div class="card mt-3">
                <div class="card-header">Decoded</div>
                <div class="card-body">
                    {!! nl2br(e($decoded)) !!}
                </div>
            </div>
        @endif

    </form>
@endsection
