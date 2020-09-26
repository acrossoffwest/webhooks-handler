<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicWebhookResrource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'in' => $this->in,
            'endpoint' => $this->endpoint,
            'outcoming' => $this->out,
            'outcoming_local' => $this->out_local,
        ];
    }
}
