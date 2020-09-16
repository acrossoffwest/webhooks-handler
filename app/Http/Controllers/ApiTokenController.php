<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    /**
     * Update the authenticated user's API token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $key = $request->user()->id.'::api_token';
        if (Cache::has($key)) {
            return response()->json([
                'data' => Cache::get($key)
            ]);
        }

        $token = Str::random(60);

        $request->user()->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        Cache::add($key, $token, 3600);

        return response()->json([
            'data' => $token
        ]);
    }
}
