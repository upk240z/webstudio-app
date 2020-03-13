<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    Translation
@endsection

@section('contents')
    <h1>Translation</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <div class="card">
        <div class="card-body">
            <form id="transform" method="get" action="">
                {{ csrf_field() }}
                <div class="form-group">
                    <textarea class="form-control" name="text" rows="5"></textarea>
                </div>

                <div class="form-group text-center">
                    <button type="button" class="btn btn-info" data-trans="en2ja">Japanese</button>
                    <button type="button" class="btn btn-danger" data-trans="ja2en">English</button>
                    <button type="button" class="btn btn-warning" name="speak-btn" for="text">
                        <i class="fa fa-volume-up"></i>
                    </button>
                </div>

                <div class="form-group">
                    <textarea class="form-control" name="transed" rows="5"></textarea>
                </div>

                <div class="form-group text-center">
                    <button type="button" class="btn btn-warning" name="speak-btn" for="transed">
                        <i class="fa fa-volume-up"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <audio id="sound-box" class="mt-3" preload="none" controls autoplay>
    </audio>

@endsection

@section('script')
    <script>
        $(function () {
            $('button[data-trans]').on('click', function(e)
            {
                var text = $('#transform textarea[name=text]').val();
                if (text.length == 0) { return false; }
                var langs = $(this).attr("data-trans").split("2");
                $.post(
                    '../api/translation',
                    {
                        "text": text,
                        "from": langs[0],
                        "to": langs[1]
                    }
                ).done(function(response){
                    $('#transform textarea[name=transed]').val(response);
                });
            });

            $('button[name=speak-btn]').on('click', function(){
                var text = $('#transform textarea[name=' + $(this).attr('for') + ']').val();
                if (text.length == 0) { return false; }

                var soundUrl = '../api/speak?action=speak&lang=en&text=' + text;
                $('#sound-box').attr('src', soundUrl);
            });

            $(this).on("keydown", function(e)
            {
                if ($("#transform").length == 0) return true;

                if (e.which == 69 && e.ctrlKey) {
                    $("button[data-trans=ja2en]").trigger('click');
                    return false;
                }

                if (e.which == 74 && e.ctrlKey) {
                    $("button[data-trans=en2ja]").trigger('click');
                    return false;
                }

                if (e.which == 83 && e.ctrlKey == true && e.shiftKey) {
                    $("button[name=speak-btn]:first").trigger('click');
                    return false;
                }
            });
        });
    </script>
@endsection
