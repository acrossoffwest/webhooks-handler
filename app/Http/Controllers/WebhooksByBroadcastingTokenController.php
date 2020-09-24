<?php

namespace App\Http\Controllers;

use App\Http\Resources\PublicWebhookCollection;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhooksByBroadcastingTokenController extends Controller
{
    public function __invoke(Request $request, string $broadcastingToken)
    {
        $user = User::query()->where('broadcasting_token', $broadcastingToken)->firstOrFail();
        /** @var Webhook $webhook */
        $webhooks = Webhook::query()
            ->where('user_id', $user->id)
            ->get();

        return new PublicWebhookCollection($webhooks);
    }
}
