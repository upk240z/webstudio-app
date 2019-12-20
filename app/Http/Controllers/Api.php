<?php

namespace App\Http\Controllers;

use App\Util;
use Cache;
use Illuminate\Http\Request;

class Api extends Controller
{
    public function base64(Request $request)
    {
        $content = null;
        if ($request->post('action') == 'encode') {
            $content = base64_encode($request->post('data'));
        } else if ($request->post('action') == 'decode') {
            $content = base64_decode($request->post('data'));
        }

        return response($content)->withHeaders([
            'Content-Type' => 'text/plain',
            'Content-Length' => strlen($content),
        ]);
    }

    public function translation(Request $request)
    {
        $content = Util::translate(
            $request->post('text'),
            $request->post('from'),
            $request->post('to')
        );

        return response($content)->withHeaders([
            'Content-Type' => 'text/plain',
            'Content-Length' => strlen($content),
        ]);
    }

    public function speak(Request $request)
    {
        $content = Util::speak(
            $request->post('text'),
            $request->post('lang')
        );

        return response($content)->withHeaders([
            'Content-Type' => 'audio/x-wav',
            'Content-Length' => strlen($content),
        ]);
    }

    public function editorImage(Request $request)
    {
        $file = $request->file('upload');

        $image = [
            'data' => file_get_contents($file->path()),
            'type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
        $key = hash('sha256', $image['data']);

        Cache::set($key, $image);

        $content = json_encode([
            'url' => '../image/' . $key,
            'uploaded' => true,
        ]);

        return response($content)->withHeaders([
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($content),
        ]);
    }
}
