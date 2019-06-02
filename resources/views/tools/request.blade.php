@extends('layout')

@section('head')
    @component('parts.head')
        @slot('title')
            Request
        @endslot
        <style>
            div.parambox, div.headerbox {
                border: 1px dotted #999999;
                padding: 5px;
                background: #ffffcc;
                margin-top: -1px;
            }

            div.parambox:first-child, div.headerbox:first-child {
                margin-top: 0px;
            }

            input.form-control, textarea.form-control {
                font-size: 100%;
            }
        </style>
    @endcomponent
@endsection

@section('contents')
    <h1>HTTPリクエストTool</h1>

    <div class="card">
        <div class="card-header">
            Request
            <a href="#" class="collapse-panel @if ($response) collapse @endif">[-]</a>
            <a href="#" class="expand-panel @if (!$response) collapse @endif">[+]</a>
        </div>

        <div class="card-body @if ($response) collapse @endif">

            <form id="requestform" method="post" action="{{ URL::current() }}" role="form">
                {{ csrf_field() }}

                <div class="form-group">
                    <label>Type</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="type" value="server" @if ($type == 'server') checked @endif>
                                サーバー通信
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="type" value="browser" @if ($type == 'browser') checked @endif>
                                ブラウザ通信 (レスポンスヘッダーが必要 → Access-Control-Allow-Origin: {{ url('/') }})
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>URL</label>
                    <input type="text" class="form-control" name="url" value="{{ $url }}">
                </div>

                <div class="form-group">
                    <label>User Agent</label>
                    <input type="text" class="form-control" name="ua" value="{{ $ua }}">
                </div>

                <div class="form-group">
                    <label>Method</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" name="method" value="GET" @if ($method == 'GET') checked @endif>
                                GET
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" name="method" value="POST" @if ($method == 'POST') checked @endif>
                                POST
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" name="method" value="PUT" @if ($method == 'PUT') checked @endif>
                                PUT
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" name="method" value="DELETE" @if ($method == 'DELETE')  @endif>
                                DELETE
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Basic認証</label>
                    <div class="form-inline">
                        <input type="text" class="form-control" name="bauser" value="{{ $bauser }}">
                        <span>:</span>
                        <input type="text" class="form-control" name="bapw" value="{{ $bapw }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        Header
                        <button type="button" name="headeraddbtn" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i>
                        </button>
                    </label>
                    <div id="headers">
                        @foreach ($header_names as $header_name)
                        <div class="form-inline headerbox">
                            <input type="text" class="form-control" name="header_names[]" value="{{ $header_name }}">
                            :
                            <input type="text" class="form-control" name="header_values[]" value="{{ $header_values[$loop->index] }}">
                            <button type="button" name="deletebtn" class="btn btn-danger btn-sm">削除</button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        Parameter
                        <button type="button" name="paramaddbtn" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i>
                        </button>
                    </label>
                    <div id="params">
                        @foreach ($names as $name)
                        <div class="form-inline parambox">
                            <input type="text" class="form-control" name="names[]" value="{{ $name }}">
                            :
                            <textarea class="form-control" name="values[]" rows="2">{{ $values[$loop->index] }}</textarea>
                            <button type="button" name="deletebtn" class="btn btn-danger btn-sm">削除</button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-sm btn-primary">送信</button>
                    <button type="button" class="btn btn-sm btn-success" name="savebtn">保存</button>
                    <button type="button" class="btn btn-sm btn-info" name="openjsonbtn">JSONダイアログ</button>
                </div>

            </form>

        </div>

    </div>

    @if ($response)
    <div id="responsepanel" class="card mt-3 @if (!$response) collapse @endif">
        <div class="card-header">
            Response
            <a href="#" class="collapse-panel">[-]</a>
            <a href="#" class="expand-panel collapse">[+]</a>
        </div>
        <div class="card-body">
        <pre id="responseblock">
            {{ $response }}
        </pre>
        </div>
    </div>
    @endif

    @if ($error)
    <div class="card">
        <div class="card-header">Error</div>
        <div class="card-body">
            <fieldset>
                <legend>Error message</legend>
                {!! nl2br(e($error)) !!}
            </fieldset>
        </div>
    </div>
    @endif

@endsection

@section('footer')
    @component('parts.footer')
        <div id="parts" class="d-none">
            <div class="form-inline parambox">
                <input type="text" class="form-control" name="names[]" value="">
                :
                <textarea class="form-control" name="values[]" value="" rows="2"></textarea>
                <button type="button" name="deletebtn" class="btn btn-danger btn-sm ml-2">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            <div class="form-inline headerbox">
                <input type="text" class="form-control" name="header_names[]" value="">
                :
                <input type="text" class="form-control" name="header_values[]" value="">
                <button type="button" name="deletebtn" class="btn btn-danger btn-sm ml-2">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            <form id="saveform" method="post" action="{{ URL::current() }}">
                {{ csrf_field() }}
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="json" value="">
            </form>
        </div>

        <div id="jsondialog" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">JSONデータ</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" name="jsontext" rows="10"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" name="appyjsonbtn">適用</button>
                    </div>
                </div>
            </div>
        </div>

        <lscript src="{{ asset('js/jquery.base64.js') }}"></lscript>
        <script>
            $(function () {
                var addParamBox = function()
                {
                    var clone = $("#parts > div.parambox:first").clone();
                    clone.find("input,textarea").val("");
                    $("#params").append(clone);
                    return clone;
                };

                var addHeaderBox = function()
                {
                    var clone = $("#parts > div.headerbox:first").clone();
                    clone.find("input,textarea").val("");
                    $("#headers").append(clone);
                    return clone;
                };

                $("button[name=paramaddbtn]").on("click", addParamBox);
                $("button[name=headeraddbtn]").on("click", addHeaderBox);

                $("#params,#headers").on("click", "button[name=deletebtn]", function(e)
                {
                    $(this).parent("div").remove();
                });

                $("#requestform").on("submit", function(e)
                {
                    if ($("input[name=type]:checked").val() == "browser") {
                        e.preventDefault();

                        var url = $("input[name=url]").val();
                        if (url.length == 0) return;

                        $("#responsepanel").show();
                        $("#responseblock").empty();

                        var params = {};
                        var names = [];
                        $("#requestform input[name^=names]").each(function(index)
                        {
                            names.push($(this).val());
                        });
                        var values = [];
                        $("#requestform textarea[name^=values]").each(function(index)
                        {
                            values.push($(this).val());
                        });
                        for (var i=0;i<names.length;i++) {
                            if (names[i].length == 0) continue;
                            if (typeof(params[names[i]]) != "undefined") {
                                params[names[i]] = [ params[names[i]] ];
                            }
                            if ($.isArray(params[names[i]])) {
                                params[names[i]].push(values[i]);
                            } else {
                                params[names[i]] = values[i];
                            }
                        }

                        var headers = {};
                        names = [];
                        $("#requestform input[name^=header_names]").each(function(index)
                        {
                            names.push($(this).val());
                        });
                        values = [];
                        $("#requestform input[name^=header_values]").each(function(index)
                        {
                            values.push($(this).val());
                        });
                        for (var i=0;i<names.length;i++) {
                            if (names[i].length == 0) continue;
                            headers[names[i]] = values[i];
                        }

                        if ($("input[name=bauser]").val().length > 0) {
                            headers["Authorization"] = "Basic " + $.base64.encode(
                                $("input[name=bauser]").val() + ":" + $("input[name=bapw]").val()
                            );
                        }

                        $.ajax(
                            url,
                            {
                                type: $("input[name=method]").val(),
                                data: params,
                                headers: headers,
                                success: function(response, status, request)
                                {
                                    $("#responseblock").text(response);
                                },
                                error: function(request, status, error)
                                {
                                    alert(status);
                                }
                            }
                        );
                    }
                });

                var makeJson = function()
                {
                    var headers = new Array();
                    var params = new Array();

                    $("#headers div.headerbox").each(function()
                    {
                        headers.push({
                            name: $(this).find("input[name^=header_names]").val(),
                            value: $(this).find("input[name^=header_values]").val()
                        });
                    });

                    $("#params div.parambox").each(function()
                    {
                        params.push({
                            name: $(this).find("input[name^=names]").val(),
                            value: $(this).find("textarea[name^=values]").val()
                        });
                    });

                    var formdata = {
                        url: $("input[name=url]").val(),
                        type: $("input[name=type]:checked").val(),
                        ua: $("input[name=ua]").val(),
                        method: $("input[name=method]:checked").val(),
                        bauser: $("input[name=bauser]").val(),
                        bapw: $("input[name=bapw]").val(),
                        headers: headers,
                        params: params
                    };

                    return JSON.stringify(formdata);
                };

                $("[name=openjsonbtn]").on("click", function(e)
                {
                    $("textarea[name=jsontext]").val(makeJson());
                    $("#jsondialog").modal();
                });

                var setJsonValues = function(jsonText)
                {
                    var formdata = JSON.parse(jsonText);
                    $("input[name=type][value=" + formdata.type + "]").prop("checked", true);
                    $("input[name=url]").val(formdata.url);
                    $("input[name=ua]").val(formdata.ua);
                    $("input[name=method][value=" + formdata.method + "]").prop("checked", true);
                    $("input[name=bauser]").val(formdata.bauser);
                    $("input[name=bapw]").val(formdata.bapw);

                    $("#headers").empty();
                    for (var key in formdata.headers) {
                        var form = formdata.headers[key];
                        var box = addHeaderBox();
                        box.find("input[name^=header_names]").val(form.name);
                        box.find("input[name^=header_values]").val(form.value);
                    }

                    $("#params").empty();
                    for (var key in formdata.params) {
                        var form = formdata.params[key];
                        var box = addParamBox();
                        box.find("input[name^=names]").val(form.name);
                        box.find("textarea[name^=values]").val(form.value);
                    }
                };

                $("button[name=appyjsonbtn]").on("click", function(e)
                {
                    setJsonValues($("textarea[name=jsontext]").val());
                    $("#jsondialog").modal("hide");
                });

                $("button[name=savebtn]").on("click", function(e)
                {
                    $("#saveform").find("input[name=json]").val(makeJson());
                    $("#saveform").submit();
                });

                $("body").on("dragover dragenter", function(e)
                {
                    if (e.preventDefault) { e.preventDefault(); }
                    return false;
                });

                var reader = new FileReader();
                $(reader).on("load", function(e)
                {
                    setJsonValues(reader.result);
                });

                $("body").on("drop", function(e)
                {
                    e.preventDefault();
                    var file = e.originalEvent.dataTransfer.files[0];
                    reader.readAsText(file, "utf-8");
                });

                $(".collapse-panel").on("click", function(e)
                {
                    $(this).hide().parent("div").siblings(".card-body").hide();
                    $(this).siblings(".expand-panel").show();
                    return false;
                });

                $(".expand-panel").on("click", function(e)
                {
                    $(this).hide().parent("div").siblings(".card-body").show();
                    $(this).siblings(".collapse-panel").show();
                    return false;
                });
            });
        </script>
    @endcomponent
@endsection
