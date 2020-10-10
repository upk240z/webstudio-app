<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    CSR Generator
@endsection

@section('contents')
    <h1>CSR Generator</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form id="input-form" method="get" action="" role="form">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">input</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Common Name</label>
                            <input type="text" class="form-control" name="cn" value="" validation="require">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Country Name</label>
                            <input type="text" class="form-control" name="c" value="" validation="require">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">State Or ProvinceName</label>
                            <input type="text" class="form-control" name="st" value="" validation="require">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Locality Name</label>
                            <input type="text" class="form-control" name="l" value="" validation="require">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Organization Name</label>
                            <input type="text" class="form-control" name="o" value="" validation="require">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Organization Unit Name</label>
                            <input type="text" class="form-control" name="ou" value="">
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-sm" id="create-btn">生成</button>
                    <button class="btn btn-primary btn-sm" type="button" disabled id="loading-btn">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        生成中...
                    </button>
                </div>
            </div>
        </div>

    </form>

    <div class="row mt-3" id="created">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Private Key
                    <button class="btn btn-sm btn-info" name="copy-key-btn">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="card-body">
                    <pre id="key-text"></pre>
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
                    <pre id="csr-text"></pre>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
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

            $('#created, #loading-btn').hide();

            firebase.initializeApp({
            });
            let generateCsr = firebase.functions().httpsCallable('csr');

            Validation.init('#input-form', function () {
                if (!Validation.existError()) {
                    $('#create-btn').hide();
                    $('#loading-btn').show();
                    $('#created').hide();
                    generateCsr({
                        commonName: $('input[name=cn]').val(),
                        countryName: $('input[name=c]').val(),
                        stateOrProvinceName: $('input[name=st]').val(),
                        localityName: $('input[name=l]').val(),
                        organizationName: $('input[name=o]').val(),
                        organizationalUnitName: $('input[name=ou]').val()
                    }).then(function (result) {
                        $('#key-text').text(result.data.key);
                        $('#csr-text').text(result.data.csr);
                        $('#created').show();
                    }).catch(function (err) {
                        alert('生成に失敗');
                        console.log(err);
                    }).finally(function () {
                        $('#loading-btn').hide();
                        $('#create-btn').show();
                    });
                }
                return false;
            });
        });
    </script>
@endsection
