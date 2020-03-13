<?php
use App\Util;

$TYPES = array(
    "png" => "PNG",
    "jpeg" => "JPEG"
);
?>
@extends('layout.main')

@section('title')
    QR Code
@endsection

@section('contents')
    <h1>QR Code</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form name="form1" method="get" action="{{ URL::current() }}" role="form">
        {{ csrf_field() }}
        <input type="hidden" name="action" value="url">
        <input type="hidden" name="size" value="{{ $size }}">

        <div class="card">
            <div class="card-header">input</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>type</label>
                            <div class="radio">
                                @foreach ($TYPES as $code => $name)
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" name="type" value="{{ $code }}" @if ($type == $code) checked @endif>
                                        {{ $name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label class="control-label">size:<span id="slidersize">{{ $size }}</span></label>
                            <div id="slider"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>text</label>
                            <textarea class="form-control" name="text" rows="5">{{ $text }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">生成</button>
                </div>
            </div>
        </div>

    </form>

@endsection

@section('script')
    <script>
        $(function () {
            $("#slider").slider({
                min: 1,
                max: 19,
                value: "{{ $size }}",
                slide: function(e, ui)
                {
                    $("input[name=size]:first").val(ui.value);
                    $("#slidersize").text(ui.value);
                }
            });
        });
    </script>
@endsection
