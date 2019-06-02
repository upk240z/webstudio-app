<?php
use App\Util;
?>
@extends('layout')

@section('head')
    @component('parts.head')
        @slot('title')
            JSON
        @endslot
    @endcomponent
@endsection

@section('contents')
    <h1>JSON parser</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form name="form1" method="post" action="{{ URL::current() }}">
        {{ csrf_field() }}
        <div class="card">
            <div class="card-header">Input</div>
            <div class="card-body">
                <div class="form-group">
                    <textarea class="form-control" rows="5" name="input" placeholder="JSON string">{{ $input }}</textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-sm btn-primary">Parse</button>
                </div>
            </div>
        </div>

        @if ($parsed)
        <div class="card mt-3">
            <div class="card-header">
                Parsed
                <button type="button" class="btn btn-sm btn-success" name="toggle-btn">
                    <i class="far fa-folder-open"></i>
                </button>
            </div>
            <div class="card-body">
                <pre>{{ print_r($parsed) }}</pre>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                YAML
                <button type="button" class="btn btn-sm btn-info" name="copy-btn">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
            <div class="card-body">
                <pre id="yaml-text">{{ trim(trim(substr($yaml, 3)), '.') }}</pre>
            </div>
        </div>
        @endif

    </form>

@endsection

@section('footer')
    @component('parts.footer')
        <script>
            $(function () {
                $('[name=copy-btn]').on('click', function() {
                    Util.copy($('#yaml-text').text());
                    return false;
                });
                $('[name=toggle-btn]').on('click', function() {
                    $(this).closest('div.card').find('div.card-body').toggle();
                    return false;
                });
            });
        </script>
    @endcomponent
@endsection
