<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\Webhook;

class WebhookChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @return array|bool
     */
    public function join(User $user, Webhook $webhook)
    {
        return $user->id === $webhook->user_id;
    }
}
