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
@extends('layout.main')

@section('title')
    Memo
@endsection

@section('contents')

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form id="delete-form" method="post" action="delete">
        {{ csrf_field() }}
        <input type="hidden" name="memo_id" value="{{ $memoId }}">
        <input type="hidden" name="folder_id" value="{{ $folderId }}">
        <div class="card shadow-lg">
            <div class="card-header clearfix">
                <h5 class="float-left">
                    <?php $first = true; ?>
                    @foreach($folders as $folder)
                    @if(!$first)
                    &gt;
                    @endif
                    <a href="{{ url('/') }}/?folder_id={{ $folder['id'] }}">{{ $folder['name'] }}</a>
                    <?php $first = false; ?>
                    @endforeach
                </h5>
                <a class="float-right btn btn-success" href="form?id={{ $memoId }}&folder_id={{ $folderId }}">
                    <i class="fas fa-pencil-alt"></i>
                </a>
                <span class="float-right">&nbsp;</span>
                <a class="float-right btn btn-success" href="form?folder_id={{ $folderId }}">
                    <span class="fas fa-plus"></span>
                </a>
                <span class="float-right">&nbsp;</span>
                <button type="button" class="float-right btn btn-info" name="move-btn">
                    <i class="fas fa-sitemap" aria-hidden="true"></i>
                </button>
                <span class="float-right">&nbsp;</span>
                <button type="submit" class="float-right btn btn-danger" name="delete-btn">
                    <span class="fas fa-trash"></span>
                </button>
                <span class="float-right">&nbsp;</span>
                <a class="float-right btn btn-warning" href="{{ url('/') }}/?folder_id={{ $folderId }}">
                    <span class="fas fa-list"></span>
                </a>
            </div>
            <div class="card-body markdown">
                <h3 class="title">{{ $title }}</h3>
                {!! $body !!}
            </div>
            <div class="card-footer text-right">last updated at {{ substr($updated_at, 0, 16) }}</div>
        </div>
    </form>

    <form id="move-form" method="post" action="movememo">
        {{ csrf_field() }}
        <input type="hidden" name="memo_id" value="{{ $memoId }}">
        <input type="hidden" name="folder_id" value="">
        <div class="modal fade" tabindex="-1" role="dialog" id="folder-selector">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">select target folder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ul class="tree-menu">
                            <li>
                                <a href="#" name="folder-name" data-id="0">TOP</a>
                                <?php printTree($tree); ?>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('script')
    <script>
        $(function () {
            $('#delete-form').on('submit', function() {
                if (confirm('are you sure?') == false) {
                    return false;
                }
            });

            $('button[name=move-btn]').on('click', function() {
                $('#folder-selector').modal();
            });

            $('a[name=folder-name]').on('click', function() {
                $('#move-form input[name=folder_id]').val($(this).attr('data-id'));
                $('#move-form').submit();
                return false;
            });
        });
    </script>
@endsection
