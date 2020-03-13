<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    Google auth
@endsection

@section('contents')
    <h1>Google auth</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form method="post" action="">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">QR code</div>
                    <div class="card-body text-center">
                        <img src="../img/gaqrcode.png" alt="">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Password check</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" class="form-control" name="password" maxlength="6" value="{{ $password }}">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-small btn-primary">check</button>
                        </div>
                        @if ($result !== null)
                        <?php if ($result !== null) { ?>
                        <div class="form-group">
                            @if ($result)
                            <span class="badge badge-success">OK</span>
                            @else
                            <span class="badge badge-danger">NG</span>
                            @endif
                        </div>
                        <?php } ?>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
