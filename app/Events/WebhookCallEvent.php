<?php

namespace App\Events;

use App\Models\Webhook;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookCallEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Webhook $webhook;
    public string $url = '';
    public string $method = '';
    public array $headers = [];
    public array $payload = [];


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Webhook $webhook, string $url = '', string $method = '', array $headers = [], array $payload = [])
    {
        $this->webhook = $webhook;
        $this->url = $url;
        $this->method = $method;
        $this->headers = $headers;
        $this->payload = $payload;
    }

    public function broadcastOn()
    {
        return new Channel('users.'.$this->webhook->user->safeBroadcastingToken.'.webhooks.'.$this->webhook->in.'.in.'.$this->webhook->id);
    }
}
