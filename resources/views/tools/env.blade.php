<?php
use App\Util;
?>
@extends('layout')

@section('head')
    @component('parts.head')
        @slot('title')
            Environment variables
        @endslot
    @endcomponent
@endsection

@section('contents')
    <h1>Environment variables</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <div class="card">
        <div class="card-header">Server</div>
        <div class="card-body">
            <pre>{{ print_r($variables) }}</pre>
        </div>
    </div>

@endsection

@section('footer')
    @component('parts.footer')
        <script>
            $(function () {

            });
        </script>
    @endcomponent
@endsection
