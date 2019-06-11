<?php

namespace App\Http\Controllers;

use App\Util;
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
}
