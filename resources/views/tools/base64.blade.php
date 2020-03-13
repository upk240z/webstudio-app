@extends('layout.main')

@section('title')
    Base64
@endsection

@section('contents')
    <h1>BASE64</h1>

    <form>
        <div class="card mt-3">
            <div class="card-header">encode text</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="target" class="control-label">input text</label>
                    <textarea class="form-control" name="target" rows="5"></textarea>
                </div>
                <div class="form-group text-center">
                    <button type="button" class="btn btn-sm btn-warning" name="encode-btn">↓</button>
                </div>
                <div class="form-group">
                    <label for="encoded" class="control-label">encoded</label>
                    <textarea class="form-control" name="encoded" readonly="readonly" rows="5"></textarea>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">encode file</div>
            <div class="card-body">
                <div class="form-group">
                    <button type="button" class="btn btn-sm btn-info" name="file-btn">input file</button>
                    <input type="file" name="target_file" hidden>
                </div>
                <div class="form-group">
                    <label for="encoded_file" class="control-label">encoded file</label>
                    <textarea class="form-control" name="encoded_file" readonly="readonly" rows="5"></textarea>
                </div>
                <div class="form-group">
                    <a id="encoded-link" href="" target="_blank">Link</a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">decode</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="target_encoded" class="control-label">input</label>
                    <textarea class="form-control" name="target_encoded" rows="5"></textarea>
                </div>
                <div class="form-group text-center">
                    <button type="button" class="btn btn-sm btn-success" name="decode-btn">↓</button>
                </div>
                <div class="form-group">
                    <label for="decoded" class="control-label">decoded</label>
                    <textarea class="form-control" name="decoded" readonly="readonly" rows="5"></textarea>
                </div>
                <div class="form-group">
                    <a id="decoded-link" href="" target="_blank">Link</a>
                </div>
            </div>
        </div>

    </form>

@endsection

@section('script')
    <script>
        $(function () {
            var uri = '{{ url('/') . '/api/base64' }}';
            var reader = new FileReader();

            $('#encoded-link, #decoded-link').hide();

            $(reader).on('load', function()
            {
                var splited = reader.result.split(',');
                $('textarea[name=encoded_file]').val(splited[1]);
                $('#encoded-link').attr('href', reader.result).show();
            });

            $('button[name=encode-btn]').on('click', function()
            {
                $.post(
                    uri,
                    {
                        action : 'encode',
                        data : $('textarea[name=target]').val()
                    }
                ).done(function(response)
                {
                    $('textarea[name=encoded]').val(response);
                });
            });

            $('button[name=file-btn]').on('click', function()
            {
                $('input[name=target_file]').trigger('click');
            });

            $('input[name=target_file]').on('change', function()
            {
                if (this.files.length > 0)
                {
                    reader.readAsDataURL(this.files[0]);
                }
            });

            $('button[name=decode-btn]').on('click', function()
            {
                $.post(
                    uri,
                    {
                        action : 'decode',
                        data : $('textarea[name=target_encoded]').val()
                    }
                ).done(function(response)
                {
                    $('textarea[name=decoded]').val(response);
                    $('#decoded-link').attr('href', 'data:application/octet-stream;base64,' + $('textarea[name=target_encoded]').val()).show();
                });
            });
        });
    </script>
@endsection
