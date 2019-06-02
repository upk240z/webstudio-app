<?php
use App\Util;
?>
@extends('layout')

@section('head')
    @component('parts.head')
        @slot('title')
            Random
        @endslot
    @endcomponent
@endsection

@section('contents')
    <h1>Random</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form name="form1" method="post" action="{{ URL::current() }}" role="form">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-body">

                <div class="form-group">
                    <div class="form-inline">
                        <input type="text" class="form-control" name="length" placeholder="文字数" value="{{ $length }}">
                        <button type="submit" class="btn btn-sm btn-primary ml-2">show</button>
                    </div>
                </div>

                @if ($random)
                <div class="form-group">
                    <input type="text" class="form-control" value="{{ $random }}">
                </div>
                @endif

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
