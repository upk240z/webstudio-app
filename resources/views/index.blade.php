<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    Memo
@endsection

@section('contents')

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <form method="post" action="deletefolder">
        {{ csrf_field() }}
        <input type="hidden" name="folder_id" value="{{ $folderId }}">
        <div class="card shadow-lg">
            <div class="card-header bg-light">
                <?php $first = true; ?>
                @foreach($folders as $folder)
                    @if(!$first)
                    &gt;
                    @endif
                    <a class="folder-link" href="?folder_id={{ $folder['id'] }}">{{ $folder['name'] }}</a>
                <?php $first = false; ?>
                @endforeach
                @if($folderId != 0)
                    <button type="button" class="btn btn-info" name="folder-edit-btn">
                        <i class="fas fa-edit"></i>
                    </button>
                @endif
                @if(count($rows) == 0)
                    <button type="submit" class="btn btn-danger" name="folder-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                @endif
                <button type="button" class="float-right btn btn-info" name="folder-btn">
                    <i class="fas fa-plus"></i>
                    <i class="fas fa-folder-open"></i>
                </button>
                <span class="float-right">&nbsp;</span>
                <a class="float-right btn btn-success" href="form?folder_id={{ $folderId }}">
                    <i class="fas fa-plus"></i>
                    <i class="fas fa-file"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($rows as $row)
                        @if($row['is_folder'])
                            <a class="list-group-item list-group-item-success" href="?folder_id={{ $row['folder_id'] }}">
                                <i class="fas fa-folder"></i>
                                {{ $row['folder_name'] }}
                            </a>
                            @else
                            <a class="list-group-item list-group-item-warning" href="memo?id={{ $row['memo_id'] }}&folder_id={{ $folderId }}">
                                <i class="fas fa-file"></i>
                                {{ $row['title'] }}
                                <span class="badge badge-success float-right">{{ $row['updated_at'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </form>

    <form method="post" action="">
        {{ csrf_field() }}
        <div class="modal fade" tabindex="-1" role="dialog" id="folder-form">
            <input type="hidden" name="folder_id" value="">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">folder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" class="form-control" name="folder_name" value="" placeholder="フォルダ名">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="save-btn">
                            <span class="fa fa-pencil"></span>
                            Save
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </form>

@endsection

@section('script')
    <script>
        $(function () {
            $('button[name=folder-btn]').on('click', function() {
                $('#folder-form input').val('');
                $('#folder-form').modal();
            });
            $('button[name=folder-edit-btn]').on('click', function() {
                $('#folder-form input[name=folder_id]').val({{ $folderId }});
                $('#folder-form input[name=folder_name]').val($('.folder-link:last').text());
                $('#folder-form').modal();
            });
        });
    </script>
@endsection
