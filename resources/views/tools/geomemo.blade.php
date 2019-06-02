<?php
use App\Util;
?>
@extends('layout-wide')

@section('head')
    @component('parts.head')
        @slot('title')
            Geo Memo
        @endslot
    @endcomponent
@endsection

@section('contents')
    <h1>Geo Memo</h1>

    <?php Util::showMessage('error') ?>
    <?php Util::showMessage('success') ?>

    <div class="card">
        <div class="card-header">
            <div id="buttons" class="btn-group" role="group">
                <button type="button" class="btn btn-primary" name="geolocation-btn">
                    <i class="fa fa-flag"></i>
                </button>
                <button type="button" class="btn btn-warning" name="list-btn">
                    <i class="fa fa-th-list"></i>
                </button>
            </div>
        </div>
        <div class="card-body" id="map">
        </div>
    </div>

    <div class="modal fade" role="dialog" id="place-form">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form>
                        <input type="hidden" name="place_id">
                        <div class="form-group">
                            <label for="label">label</label>
                            <input type="text" class="form-control" name="label">
                        </div>
                        <div class="form-group">
                            <label for="note">note</label>
                            <textarea class="form-control" name="note"></textarea>
                        </div>
                        <div class="form-group">
                            <a href="#" id="detail-btn">[+]</a>
                        </div>
                        <div class="details">
                            <div class="form-group">
                                <label for="latitude">latitude</label>
                                <input type="text" class="form-control" name="lat" readonly>
                            </div>
                            <div class="form-group">
                                <label for="longitude">longitude</label>
                                <input type="text" class="form-control" name="lng" readonly>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                    <button type="button" class="btn btn-danger" name="remove-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button type="button" class="btn btn-primary" name="save-btn">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="places">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="gridSystemModalLabel">Places</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    @component('parts.footer')
        <script>
            var Geo = {
                map: null,
                markers: [],
                selfMarker: null,
                taskId: null,
                loadCurrentPosition: function(callback) {
                    if (navigator.geolocation === false) {
                        console.log('no geolocation')
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            if (callback != undefined) {
                                callback(pos.coords.latitude, pos.coords.longitude);
                            }
                            if (Geo.selfMarker == null) {
                                Geo.selfMarker = new google.maps.Marker({
                                    map: Geo.map,
                                    position: new google.maps.LatLng(
                                        pos.coords.latitude, pos.coords.longitude
                                    ),
                                    icon: 'https://maps.google.com/mapfiles/ms/micons/man.png',
                                    draggable: false
                                });
                            } else {
                                Geo.selfMarker.setPosition(
                                    new google.maps.LatLng(
                                        pos.coords.latitude, pos.coords.longitude
                                    )
                                );
                            }

                            if (Geo.taskId == null) {
                                Geo.taskId = setInterval(function() {
                                    Geo.loadCurrentPosition();
                                }, 500);
                            }
                        },
                        function (error) {
                            if (Geo.taskId != null) {
                                clearInterval(Geo.taskId);
                                console.log('stop task:' + Geo.taskId);
                                Geo.taskId = null;
                            }
                            switch (error.code) {
                                case error.POSITION_UNAVAILABLE:
                                    console.log('POSITION_UNAVAILABLE');
                                    break;
                                case error.PERMISSION_DENIED:
                                    console.log('PERMISSION_DENIED');
                                    break;
                                case error.PERMISSION_DENIED_TIMEOUT:
                                    console.log('PERMISSION_DENIED_TIMEOUT');
                                    break;
                                default:
                                    console.log('error code: ' + error.code);
                            }
                        }
                    );
                },
                drawMarkers: function() {
                    $.each(Geo.markers, function(index, marker) {
                        marker.setMap(null);
                    });
                    Geo.markers = [];
                    var center = Geo.map.getCenter();
                    $.get(
                        '../geoapi/places',
                        {
                            lat: center.lat(),
                            lng: center.lng()
                        }
                    ).done(function(response) {
                        $.each(response.places, function(index, place) {
                            var marker = new google.maps.Marker({
                                map: Geo.map,
                                title: place.label,
                                label: place.label,
                                position: new google.maps.LatLng(
                                    parseFloat(place.lat), parseFloat(place.lng)
                                ),
                                attribution: { source: JSON.stringify(place) },
                                icon: 'https://maps.google.co.jp/mapfiles/ms/icons/ylw-pushpin.png',
                                draggable: true
                            });

                            marker.addListener('click', function() {
                                var place = JSON.parse(marker.getAttribution().source);
                                $('#place-form [name=place_id]').val(place.place_id);
                                $('#place-form [name=label]').val(place.label);
                                $('#place-form [name=note]').val(place.note);
                                $('#place-form [name=lat]').val(place.lat);
                                $('#place-form [name=lng]').val(place.lng);
                                $('#place-form .details').hide();
                                $('#place-form').modal();
                            });

                            marker.addListener('dragend', function(e) {
                                var place = JSON.parse(marker.getAttribution().source);
                                place.lat = e.latLng.lat();
                                place.lng = e.latLng.lng();
                                $.post(
                                    '../geoapi/place',
                                    {
                                        place_id: place.place_id,
                                        lat: e.latLng.lat(),
                                        lng: e.latLng.lng(),
                                        label: place.label,
                                        note: place.note
                                    }
                                ).done(function(response) {
                                    marker.setAttribution({ source: JSON.stringify(place) });
                                }).fail(function(response) {
                                    var result = JSON.parse(response.responseText);
                                    alert(result.error);
                                });
                            });

                            Geo.markers.push(marker);
                        });
                    }).fail(function(response) {
                        var result = JSON.parse(response.responseText);
                        alert(result.error);
                    });
                },
                saveState: function() {
                    var center = Geo.map.getCenter();
                    $.post(
                        '../geoapi/cookie',
                        {
                            lat: center.lat(),
                            lng: center.lng(),
                            zoom: Geo.map.getZoom()
                        }
                    ).fail(function(response) {
                        var result = JSON.parse(response.responseText);
                        alert(result.error);
                    });
                },
                init: function() {
                    Geo.map = new google.maps.Map(document.getElementById('map'), {
                        center: {
                            lat: {{ $cookie['lat'] }},
                            lng: {{ $cookie['lng'] }}
                        },
                        zoom: {{ $cookie['zoom'] }},
                        clickableIcons: false
                    });

                    Geo.map.addListener('dragend', Geo.saveState);
                    Geo.map.addListener('zoom_changed', Geo.saveState);
                    Geo.map.addListener('click', function(e) {
                        $('#place-form input, #place-form textarea').val('');
                        $('#place-form input[name=lat]').val(e.latLng.lat());
                        $('#place-form input[name=lng]').val(e.latLng.lng());
                        $('#place-form .details').hide();
                        $('#place-form').modal();
                    });

                    Geo.drawMarkers();
                    Geo.loadCurrentPosition();
                }
            };

            $(function () {
                $('button[name=geolocation-btn]').on('click', function() {
                    Geo.loadCurrentPosition(function(lat, lng) {
                        Geo.map.setCenter({
                            lat: lat,
                            lng: lng
                        });
                    });
                });

                $('#place-form button[name=save-btn]').on('click', function() {
                    var label = $('#place-form input[name=label]').val();
                    var note = $('#place-form textarea[name=note]').val();
                    if (label.length == 0) {
                        return false;
                    }

                    $.post(
                        '../geoapi/place',
                        {
                            place_id: $('#place-form input[name=place_id]').val(),
                            lat: $('#place-form input[name=lat]').val(),
                            lng: $('#place-form input[name=lng]').val(),
                            label: label,
                            note: note
                        }
                    ).done(function(response) {
                        Geo.drawMarkers();
                    }).fail(function(response) {
                        var result = JSON.parse(response.responseText);
                        alert(result.error);
                    });

                    $('#place-form').modal('hide');
                });

                $('#place-form button[name=remove-btn]').on('click', function() {
                    var place_id = $('#place-form input[name=place_id]').val();
                    if (place_id.length == 0) { return false; }

                    $.post(
                        '../geoapi/remove',
                        {
                            place_id: place_id
                        }
                    ).done(function(response) {
                        Geo.drawMarkers();
                    }).fail(function(response) {
                        alert(response);
                        console.log(response);
                    });

                    $('#place-form').modal('hide');
                });

                $('#detail-btn').on('click', function() {
                    if ($(this).text() == '[+]') {
                        $('#place-form .details').show();
                        $(this).text('[-]');
                    } else {
                        $('#place-form .details').hide();
                        $(this).text('[+]');
                    }
                    return false;
                });

                $(window).on('resize', function() {
                    var height = $(window).height();
                    $('#map').css('height', height + 'px');
                }).trigger('resize');

                /*
                $('nav.navbar, h1').hide();
                $('body').css('padding-top', '0px');
                $('[name=menu-btn]').on('click', function() {
                    if ($('div[role=navigation]').is(':visible')) {
                        $('div[role=navigation], h1').hide();
                        $('body').css('padding-top', '0px');
                    } else {
                        $('nav.navbar, h1').show();
                        $('body').css('padding-top', '60px');
                    }
                });
                */

                $('#places .list-group:first').on('click', 'a.list-group-item', function() {
                    Geo.map.setCenter({
                        lat: parseFloat($(this).attr('data-lat')),
                        lng: parseFloat($(this).attr('data-lng'))
                    });
                    $('#places').modal('hide');
                    Geo.saveState();
                    return false;
                });

                $('[name=list-btn]').on('click', function() {
                    var center = Geo.map.getCenter();
                    $.get(
                        '../geoapi/places',
                        {
                            lat: center.lat(),
                            lng: center.lng()
                        }
                    ).done(function(response) {
                        var listGroup = $('#places .list-group:first');
                        listGroup.empty();
                        $.each(response.places, function(index, place) {
                            listGroup.append(
                                $('<a></a>')
                                    .addClass('list-group-item')
                                    .attr('data-lat', place.lat)
                                    .attr('data-lng', place.lng)
                                    .text(place.label + '(' + place.distance + 'km)')
                            );
                        });
                        $('#places').modal();
                    });
                });
            });
        </script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=Geo.init">
        </script>
    @endcomponent
@endsection
