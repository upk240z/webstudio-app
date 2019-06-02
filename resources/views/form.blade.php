<?php
use App\Util;

function printTree($tree)
{
?>
<ul class="tree-menu">
    <?php foreach ($tree as $folder) { ?>
    <li>
        <a href="#" name="folder-name" data-id="<?php echo htmlspecialchars($folder['id']) ?>"><?php echo htmlspecialchars($folder['name']) ?></a>
        <?php
        if (count($folder['children']) > 0) {
            printTree($folder['children']);
        }
        ?>
    </li>
    <?php } ?>
</ul>
<?php
}
?>
@extends('layout')

@section('head')
    @component('parts.head')
        @slot('title')
            Memo
        @endslot
    @endcomponent
@endsection

@section('contents')

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <div class="card shadow-lg">
        <div class="card-header">
            <h5>
                <?php $first = true; ?>
                @foreach($folders as $folder)
                    @if(!$first)
                        &gt;
                    @endif
                    <a href="{{ url('/') }}/?folder_id={{ $folder['id'] }}">{{ $folder['name'] }}</a>
                    <?php $first = false; ?>
                @endforeach
            </h5>
        </div>
        <div class="card-body">
            <form id="memoform" method="post" action="{{ URL::current() }}">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $memoId }}">
                <input type="hidden" name="folder_id" value="{{ $folderId }}">

                <div class="form-group">
                    <label for="title">title</label>
                    <input type="text" class="form-control" name="title" value="{{ $title }}">
                </div>

                <div class="form-group">
                    <label for="body">body</label>
                    <textarea name="body" class="form-control" rows="10">{{ $body }}</textarea>
                </div>

                <div class="text-center">
                    @if($memoId)
                    <a class="btn btn-warning" href="{{ url('/') }}/memo?id={{ $memoId }}&folder_id={{ $folderId }}">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        back
                    </a>
                    @else
                    <a class="btn btn-warning" href="{{ url('/') }}/memo?folder_id={{ $folderId }}">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        back
                    </a>
                    @endif
                    <button type="submit" class="btn btn-primary" name="save-btn">
                        <i class="fas fa-pencil-alt"></i>
                        save
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('footer')
    @component('parts.footer')
        <script>
            $(function () {
                $('textarea').on('keydown', function(e) {
                    if (e.keyCode == 9) {
                        e.preventDefault();
                        var target = $(this);
                        var pos = target.get(0).selectionStart;
                        var text = target.val();
                        target.val(
                            text.substr(0, pos) + "    " + text.substr(pos)
                        );
                        target.get(0).setSelectionRange(pos + 4, pos + 4);
                        return false;
                    }
                });

                $(this).on('keydown', function(e) {
                    if ($('#memoform').length == 0) return true;

                    if (e.which == 83 && e.ctrlKey == true) {
                        e.preventDefault();
                        $('button[name=save-btn]').trigger('click');
                        return false;
                    }
                });
            });
        </script>
    @endcomponent
@endsection
