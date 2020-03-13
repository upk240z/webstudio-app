<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    CKEditor
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('dp.SyntaxHighlighter/Styles/SyntaxHighlighter.css') }}">
@endsection

@section('contents')

    <h1><a href="http://docs.cksource.com/Main_Page">CKEditor</a></h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form method="post" action="{{ URL::current() }}">
        {{ csrf_field() }}
        <h2>概要</h2>

        <p>
            FCKeditorから名前変わった。<br/>
            デスクトップエディタの強力な機能の多くをWebで実現するためのオープンソースのHTMLエディタ。オープンソース(LGPL)だが、商用利用しやすいように別のライセンスも用意されている。
        </p>

        <div class="card">
            <div class="card-header">Form</div>
            <div class="card-body">
                <textarea name="editor" id="editor" class="form-control">{{ $savedata }}</textarea>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">saved data</div>
            <div class="card-body">
                <pre name="code" class="php">{{ $savedata }}</pre>
            </div>
        </div>
    </form>

@endsection

@section('script')
    <script src="{{ asset('dp.SyntaxHighlighter/Scripts/shCore.js') }}"></script>
    <script src="{{ asset('dp.SyntaxHighlighter/Scripts/shBrushJScript.js') }}"></script>
    <script src="{{ asset('dp.SyntaxHighlighter/Scripts/shBrushPhp.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/16.0.0/classic/ckeditor.js" integrity="sha384-+I4oPzaI7M3BCILdQVSUpksWyZVOvRqN6lYmWH/tT1FoHPbUKUfoJKV0yRcaNXwv" crossorigin="anonymous"></script>
    <script>
        $(function () {
            dp.SyntaxHighlighter.ClipboardSwf = '../dp.SyntaxHighlighter/Scripts/clipboard.swf';
            dp.SyntaxHighlighter.HighlightAll('code');

            ClassicEditor
                .create(
                    document.querySelector('#editor'),
                    {
                        ckfinder: {
                            uploadUrl: '../api/ckeimage'
                        }
                    }
                )
                .then( editor => {
                    console.log(editor);
                } )
                .catch( error => {
                    console.error(error);
                } );
        });
    </script>
@endsection
