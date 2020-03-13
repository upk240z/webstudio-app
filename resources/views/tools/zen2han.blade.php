@extends('layout.main')

@section('title')
    Base64
@endsection

@section('contents')
    <h1>Zen2Han</h1>

    <form method="post" action="">
        {{ csrf_field() }}
        <div class="card mt-3">
            <div class="card-header">Zen</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="target" class="control-label">Zen text</label>
                    <textarea class="form-control" name="zen" rows="5">{{ $zen }}</textarea>
                </div>
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-sm btn-primary">↓</button>
                </div>
                <div class="form-group">
                    <label for="encoded" class="control-label">
                        Han
                        <button type="button" class="btn btn-sm btn-info" name="copy-btn">
                            <i class="fas fa-copy"></i>
                        </button>
                    </label>
                    <textarea id="han" class="form-control" name="encoded" readonly="readonly" rows="5">{{ $han }}</textarea>
                </div>
            </div>
        </div>

    </form>

@endsection

@section('script')
    <script>
        $(function () {
            $('[name=copy-btn]').on('click', function() {
                Util.copy($('#han').text());
                return false;
            });
        });
    </script>
@endsection
