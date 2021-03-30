<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    HTML escape
@endsection

@section('contents')
    <h1>HTML escape</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form name="form1" method="post" action="">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">input</div>
            <div class="card-body">
                <textarea class="form-control" rows="5" name="input">{{ $input }}</textarea>
            </div>
        </div>

        <div class="text-center mt-3">
            <input type="submit" value="show" class="btn btn-sm btn-primary">
        </div>

       @if ($escaped)
        <div class="card mt-3">
            <div class="card-header">output</div>
            <div class="card-body">
                <pre>{!! nl2br(e($escaped)) !!}</pre>
            </div>
        </div>
        @endif

    </form>
@endsection
