<?php

namespace App\Http\Controllers;

use App\Events\WebhookCallEvent;
use App\Models\Webhook;
use App\Jobs\WebhookProxyJob;
use Illuminate\Http\Request;

class BroadcastingTokenController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'data' => $request->user()->safeBroadcastingToken
        ]);
    }
}
