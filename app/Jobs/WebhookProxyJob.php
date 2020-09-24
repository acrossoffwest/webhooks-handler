<?php

namespace App\Jobs;

use App\Models\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class WebhookProxyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Webhook $webhook;
    public array $headers;
    public array $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Webhook $webhook, array $payload = [], array $headers = [])
    {
        $this->webhook = $webhook;
        $this->payload = $payload;
        $this->headers = $headers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Http::withHeaders(array_merge($this->decode($this->webhook->default_headers), $this->decode($this->headers)))
                ->post($this->webhook->out, array_merge($this->decode($this->webhook->default_payload), $this->decode($this->payload)));
        } catch (\Exception $e) {
            logs()->error($e->getMessage());
        }
    }

    public function decode($response = null): array
    {
        if (empty($response)) {
            return [];
        }

        if (is_array($response)) {
            return $response;
        }

        if (is_string($response)) {
            try {
                return json_decode($response, true);
            } catch (\Exception $ex) {}
        }

        return [];
    }
}
