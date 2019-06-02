@extends('layout')

@section('head')
    @component('parts.head')
        @slot('title')
            Regex
        @endslot
    @endcomponent
@endsection

@section('contents')
    <h1>PHP正規表現(Perl準拠)</h1>

    <form name="form1" method="post" action="" class="form-horizontal" role="form">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">入力</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="pattern" class="control-label">パターン</label>
                    <input type="text" class="form-control" id="length" name="pattern" placeholder="パターン" value="{{ $pattern }}">
                </div>
                <div class="form-group">
                    <label for="str" class="control-label">検査文字列</label>
                    <textarea class="form-control" rows="5" name="str" placeholder="検査文字列">{{ $str }}</textarea>
                </div>
                <div class="form-group">
                    <label for="replace" class="control-label">置換文字列</label>
                    <textarea class="form-control" rows="5" name="replace" placeholder="置換文字列">{{ $replace }}</textarea>
                </div>
                <div class="form-group center">
                    <button type="submit" class="btn btn-sm btn-primary">表示</button>
                </div>
            </div>
        </div>

        @if ($posted)
        <div class="card mt-3">
            <div class="card-header">マッチング結果</div>
            <div class="card-body">

                @if ($matched)
                <ul class="list-group">
                    @foreach($matches as $match)
                        <li class="list-group-item">[{{ $loop->index }}] {!! nl2br(e($match)) !!}</li>
                    @endforeach
                </ul>
                @else
                <span class="badge badge-danger">un matched</span>
                @endif

            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">置換結果</div>
            <div class="card-body">
                {!! nl2br(e($replaced)) !!}
            </div>
        </div>
        @endif

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
