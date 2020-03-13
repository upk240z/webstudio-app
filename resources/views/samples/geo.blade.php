<?php
use App\Util;
?>
@extends('layout.main')

@section('title')
    Geo Location API
@endsection

@section('head')
    <style>
        #map {
            height: 500px;
        }
    </style>
@endsection

@section('contents')
    <h1>Geo Location API</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <div class="card">
        <div class="card-header">位置情報指定</div>
        <div class="card-body">
            <form method="post" action="">
                {{ csrf_field() }}
                <div class="row">
                    <div class="form-group col">
                        <label>緯度</label>
                        <input type="text" name="lat" value="{{ $lat }}" size="12" class="form-control">
                    </div>
                    <div class="form-group col">
                        <label>経度</label>
                        <input type="text" name="lon" value="{{ $lon }}" size="12" class="form-control">
                    </div>
                    <div class="form-group col">
                        <label>Zoom</label>
                        <input type="text" name="zoom" value="{{ $zoom }}" size="3" readonly class="form-control">
                    </div>
                    <div class="form-group col">
                        <label>&nbsp;</label>
                        <div class="form-inline">
                            <button type="button" id="geobtn" class="btn btn-small btn-info">位置情報を取得</button>
                            <button type="submit" class="btn btn-small btn-primary ml-2">SAVE</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="map"></div>

@endsection

@section('script')
    <script>
        var map;
        var initMap = function()
        {
            map = new google.maps.Map(document.getElementById("map"), {
                center : {
                    lat : parseFloat($("input[name=lat]:first").val()),
                    lng : parseFloat($("input[name=lon]:first").val())
                },
                zoom : parseInt($("input[name=zoom]:first").val())
            });

            map.addListener('center_changed', function() {
                var center = map.getCenter();
                $("input[name=lat]").val(center.lat());
                $("input[name=lon]").val(center.lng());
            });

            map.addListener('zoom_changed', function() {
                $("input[name=zoom]").val(map.getZoom());
            });
        };

        $(function() {
            $("#geobtn").on("click", function() {
                if (navigator.geolocation === false) {
                    alert("対応ブラウザではありません");
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        $("input[name=lat]").val(pos.coords.latitude);
                        $("input[name=lon]").val(pos.coords.longitude);

                        map.setCenter({
                            lat : pos.coords.latitude,
                            lng : pos.coords.longitude
                        });
                    },
                    function(error) {
                        switch (error.code) {
                            case error.POSITION_UNAVAILABLE:
                                alert("位置情報の取得ができませんでした");
                                break;
                            case error.PERMISSION_DENIED:
                                alert("位置情報取得の使用許可がされませんでした");
                                break;
                            case error.PERMISSION_DENIED_TIMEOUT:
                                alert("位置情報取得中にタイムアウトしました");
                                break;
                        }
                    }
                );
            });
        });
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap">
    </script>
@endsection
