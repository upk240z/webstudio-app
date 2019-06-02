<?php
use App\Util;
?>
@extends('layout')

@section('head')
    @component('parts.head')
        @slot('title')
            IP address
        @endslot
    @endcomponent
@endsection

@section('contents')
    <h1>IP address</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form name="form1" method="post" action="" class="" role="form">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">帯域チェック</div>
            <div class="card-body form-inline">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>帯域</th>
                        <td>
                            <input type="text" name="range_ip" size="20" value="{{ $range_ip }}" class="form-control" style="width:150px">
                            /
                            <input type="text" name="range_mask" size="4" value="{{ $range_mask }}" class="form-control" style="width:100px">
                            <input type="submit" name="range" value="→" class="btn btn-sm btn-success">
                            @if ($range_from)
                            {{ $range_from }} ～ {{ $range_to }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">比較</div>
            <div class="card-body form-inline">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>帯域</th>
                        <td>
                            <input type="text" name="check_range_ip" size="20" value="{{ $check_range_ip }}" class="form-control" style="width:150px">
                            /
                            <input type="text" name="check_mask" size="4" value="{{ $check_mask }}" class="form-control" style="width:100px">
                        </td>
                    </tr>
                    <tr>
                        <th>検索IPアドレス</th>
                        <td>
                            <input type="text" name="check_ip" size="20" value="{{ $check_ip }}" class="form-control" style="width:150px">
                            <input type="submit" name="check" value="→" class="btn btn-sm btn-success">
                            @if ($check_result)
                            {{ $check_result }}
                            @endif
                        </td>
                    </tr>
                </table>
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
