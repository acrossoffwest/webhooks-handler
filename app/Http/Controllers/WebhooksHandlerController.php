<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Webhook;
use Illuminate\Http\Request;
use App\Jobs\WebhookProxyJob;
use App\Events\WebhookCallEvent;

class WebhooksHandlerController extends Controller
{
    public function __invoke(Request $request, string $broadcastingToken, string $slug)
    {
        $user = User::query()->where('broadcasting_token', $broadcastingToken)->firstOrFail();
        /** @var Webhook $webhook */
        $webhook = Webhook::query()
            ->where('user_id', $user->id)
            ->where('in', $slug)
            ->firstOrFail();

        $headers = $request->header();
        $payload = $request->all();
        broadcast(new WebhookCallEvent(
            $webhook,
            $request->fullUrl(),
            $request->method(),
            $request->header(),
            $request->all()
        ));
        dispatch(new WebhookProxyJob($webhook, $payload, $headers));


        $response = $webhook->default_payload;
        $code = $webhook->default_response_code ?? 200;

        if ($webhook->default_response_type === 'json' || $this->isJson($response)) {
            return response()->json($this->decodeResponse($response), $code);
        }
        return response($response, $code);
    }

    private function isJson($string) {
        $obj = json_decode($string);
        return json_last_error() === JSON_ERROR_NONE && gettype($obj ) == "object";
    }

    public function decodeResponse($response = null): array
    {
        if (empty($response)) {
            return [];
        }

        if (is_array($response)) {
            return $response;
        }

        if (is_string($response)) {
            try {
                $data = json_decode($response, true);
                logs()->info("Response was decoded as null: ".$response);
                if (empty($data)) {
                    return [];
                }
                return $data;
            } catch (\Exception $ex) {}
        }

        return [];
    }
}
