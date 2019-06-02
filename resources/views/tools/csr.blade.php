<?php
use App\Util;
?>
@extends('layout')

@section('head')
    @component('parts.head')
        @slot('title')
            CSR Generator
        @endslot
    @endcomponent
@endsection

@section('contents')
    <h1>CSR Generator</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form id="input-form" method="post" action="" role="form">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">input</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Common Name</label>
                            <input type="text" class="form-control" name="cn" value="{{ $cn }}" validation="require">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Country Name</label>
                            <input type="text" class="form-control" name="c" value="{{ $c }}" validation="require">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">State Or ProvinceName</label>
                            <input type="text" class="form-control" name="st" value="{{ $st }}" validation="require">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Locality Name</label>
                            <input type="text" class="form-control" name="l" value="{{ $l  }}" validation="require">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Organization Name</label>
                            <input type="text" class="form-control" name="o" value="{{ $o }}" validation="require">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Organization Unit Name</label>
                            <input type="text" class="form-control" name="ou" value="{{ $ou }}">
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-sm">生成</button>
                </div>
            </div>
        </div>

    </form>

    @if ($created['csr'])
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Private Key
                    <button class="btn btn-sm btn-info" name="copy-key-btn">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="card-body">
                    <pre id="key-text">{{ $created['key'] }}</pre>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    CSR
                    <button class="btn btn-sm btn-info" name="copy-csr-btn">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="card-body">
                    <pre id="csr-text">{{ $created['csr'] }}</pre>
                </div>
            </div>
        </div>
    </div>
    @endif

@endsection

@section('footer')
    @component('parts.footer')
        <script>
            $(function () {
                $('[name=copy-key-btn]').on('click', function() {
                    Util.copy($('#key-text').text());
                    return false;
                });

                $('[name=copy-csr-btn]').on('click', function() {
                    Util.copy($('#csr-text').text());
                    return false;
                });

                Validation.init('#input-form');
            });
        </script>
    @endcomponent
@endsection
