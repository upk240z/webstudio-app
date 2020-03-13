<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    XPath
@endsection

@section('contents')
    <h1>XPath</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form name="form1" method="post" action="">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">input</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Query</label>
                    <input type="text" name="query" maxlength="255" value="{{ $query }}" class="form-control">
                </div>

                <div class="form-group">
                    <label>XML</label>
                    <textarea rows="10" name="xml" class="form-control">{{ $xml }}</textarea>
                </div>

                <div class="text-center">
                    <input type="submit" value="実行" class="btn btn-primary">
                </div>
            </div>
        </div>

        @if ($nodeList)
            <button type="button" class="btn btn-success mt-3">
                結果
                <span class="badge badge-light">{{ $nodeList->length }}</span>
            </button>

            @foreach ($nodeList as $node)
            <div class="card mt-3">
                <div class="card-body">
                    <pre>{{ $doc->saveXML($node) }}</pre>
                </div>
            </div>
            @endforeach
        @endif

    </form>

@endsection
