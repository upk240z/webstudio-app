<?php

namespace App\Http\Controllers;

use App\GeoMemo;
use Illuminate\Http\Request;

class GeoApi extends Controller
{
    public function places(Request $request)
    {
        try {
            $content = json_encode([
                'success' => true,
                'places' => GeoMemo::getPlaces(
                    $request->get('lat'),
                    $request->get('lng')
                ),
            ]);

            return response($content)->withHeaders([
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($content),
            ]);
        } catch (\Exception $e) {
            $content = json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ]);

            return response($content, 500)->withHeaders([
                'Content-Type' => 'text/plain',
                'Content-Length' => strlen($content),
            ]);
        }
    }

    public function distance(Request $request)
    {
        try {
            $content = json_encode([
                'success' => true,
                'distance' => GeoMemo::getDistance(
                    $request->get('lng1'),
                    $request->get('lng2')
                ),
            ]);

            return response($content)->withHeaders([
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($content),
            ]);
        } catch (\Exception $e) {
            $content = json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ]);

            return response($content, 500)->withHeaders([
                'Content-Type' => 'text/plain',
                'Content-Length' => strlen($content),
            ]);
        }
    }

    public function cookie(Request $request)
    {
        try {
            GeoMemo::saveCookie([
                'lat' => $request->post('lat'),
                'lng' => $request->post('lng'),
                'zoom' => intval($request->post('zoom')),
                'time' => time(),
            ]);

            $content = json_encode(['success' => true]);
            return response($content)->withHeaders([
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($content),
            ]);
        } catch (\Exception $e) {
            $content = json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ]);

            return response($content, 500)->withHeaders([
                'Content-Type' => 'text/plain',
                'Content-Length' => strlen($content),
            ]);
        }
    }

    public function place(Request $request)
    {
        $place_id = $request->post('place_id');

        try {
            if ($place_id) {
                GeoMemo::updatePlace(
                    $place_id,
                    $request->post('lat'),
                    $request->post('lng'),
                    $request->post('label'),
                    $request->post('note')
                );
            } else {
                GeoMemo::addPlace(
                    $request->post('lat'),
                    $request->post('lng'),
                    $request->post('label'),
                    $request->post('note')
                );
            }

            $content = json_encode(['success' => true]);
            return response($content)->withHeaders([
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($content),
            ]);
        } catch (\Exception $e) {
            $content = json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ]);

            return response($content, 500)->withHeaders([
                'Content-Type' => 'text/plain',
                'Content-Length' => strlen($content),
            ]);
        }
    }

    public function remove(Request $request)
    {
        try {
            GeoMemo::removePlace($request->post('place_id'));

            $content = json_encode(['success' => true]);
            return response($content)->withHeaders([
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($content),
            ]);

        } catch (\Exception $e) {
            $content = json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ]);

            return response($content, 500)->withHeaders([
                'Content-Type' => 'text/plain',
                'Content-Length' => strlen($content),
            ]);
        }
    }
}
