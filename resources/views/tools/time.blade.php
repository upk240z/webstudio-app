<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    Calc time
@endsection

@section('contents')
    <h1>Calc time</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form name="form1" method="post" action="{{ URL::current() }}" class="form-inline" role="form">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">加減算</div>
            <div class="card-body">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">日付</span>
                        </div>
                        <input type="text" class="form-control dateform" name="input_date" size="10" placeHolder="YYYY-MM-DD" value="{{ $input_date }}">
                    </div>
                    ＋
                    <div class="input-group">
                        <input type="text" class="form-control" size="5" name="days" value="{{ $days }}">
                        <div class="input-group-append">
                            <span class="input-group-text">加算日数</span>
                        </div>
                    </div>
                    ＝
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">加算後日付</span>
                        </div>
                        <input type="text" class="form-control" value="{{ $result_date }}" size="10">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">差分</div>
            <div class="card-body">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">日付1</span>
                        </div>
                        <input type="text" class="form-control dateform" name="diff1_date" size="10" placeHolder="YYYY-MM-DD" value="{{ $diff1_date }}">
                    </div>
                    －
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">日付2</span>
                        </div>
                        <input type="text" class="form-control dateform" name="diff2_date" size="10" placeHolder="YYYY-MM-DD" value="{{ $diff2_date }}">
                    </div>
                    ＝
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">差分日数</span>
                        </div>
                        <input type="text" class="form-control" value="{{ $diffdays }}" size="5">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">変換</div>
            <div class="card-body">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">タイムスタンプ</span>
                        </div>
                        <input type="text" class="form-control" name="int" value="{{ $int }}" size="10">
                    </div>
                    →
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">時刻文字列(JST)</span>
                        </div>
                        <input type="text" class="form-control" value="{{ $int2time }}" size="20">
                    </div>
                </div>
                <p></p>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">時刻文字列(JST)</span>
                        </div>
                        <input type="text" class="form-control" name="str" value="{{ $str }}" size="20">
                    </div>
                    →
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">タイムスタンプ</span>
                        </div>
                        <input type="text" class="form-control" value="{{ $str2time_int }}" size="10">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-sm btn-primary">計算</button>
        </div>

    </form>

@endsection
