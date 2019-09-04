<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Free extends Controller
{
    public function params(Request $request)
    {
        $results = [
            'method' => $request->method(),
            'params' => $request->all(),
        ];

        $content = print_r($results, true);

        return response($content)->withHeaders([
            'Content-Type' => 'text/plain',
            'Content-Length' => strlen($content),
        ]);
    }
}
