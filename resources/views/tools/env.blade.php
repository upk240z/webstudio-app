<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    Environment variables
@endsection

@section('contents')
    <h1>Environment variables</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <div class="card">
        <div class="card-header">Server</div>
        <div class="card-body">
            <pre>{{ print_r($variables, true) }}</pre>
        </div>
    </div>
@endsection
