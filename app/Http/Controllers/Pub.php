<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class Pub extends Controller
{
    public function echo(Request $request)
    {
        $output = [
            'server' => $request->server(),
            'parameters' => $request->all(),
        ];
        $content = json_encode($output);

        return response($content)->withHeaders([
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($content),
        ]);
    }

    public function receiver(Request $request)
    {
        Log::info(print_r($request->all(), true));

        $output = [
            'result' => true,
        ];
        $content = json_encode($output);

        return response($content)->withHeaders([
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($content),
        ]);
    }
}
