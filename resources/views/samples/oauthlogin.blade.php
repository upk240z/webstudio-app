<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    oAuth2.0
@endsection

@section('contents')
    <h1>oAuth2.0</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <div class="card">
        <div class="card-header">Login</div>
        <div class="card-body">
            <form method="post" class="form-inline"
                  action="{{ URL::current() }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Provider</div>
                        </div>
                        <select class="form-control" name="provider">
                            <option value="github">github</option>
                            <option value="facebook">facebook</option>
                        </select>
                    </div>
                </div>
                <div class="form-group ml-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        send
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
