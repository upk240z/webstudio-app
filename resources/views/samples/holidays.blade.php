<?php
use App\Util;
?>
@extends('layout')

@section('head')
    @component('parts.head')
        @slot('title')
            Japan holidays
        @endslot
    @endcomponent
@endsection

@section('contents')
    <h1>Holidays</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <table class="table table-striped table-bordered wauto">
        <tr>
            <th>日付</th>
            <th>祝日名</th>
        </tr>
        @foreach($holidays as $date => $name)
        <tr>
            <td>{{ date('Y-m-d', strtotime($date)) }}</td>
            <td>{{ $name }}</td>
        </tr>
        @endforeach
    </table>

@endsection

@section('footer')
    @component('parts.footer')
        <script>
            $(function () {

            });
        </script>
    @endcomponent
@endsection
