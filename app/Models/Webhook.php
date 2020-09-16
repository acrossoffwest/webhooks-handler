<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class Webhook extends Model
{
    use HasFactory, LaravelVueDatatableTrait;

    protected $dataTableColumns = [
        'id' => [
            'searchable' => false,
        ],
        'name' => [
            'searchable' => true,
        ],
        'in' => [
            'searchable' => true,
        ],
        'out' => [
            'searchable' => true,
        ]
    ];

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEndpointAttribute()
    {
        return route('endpoints', [
            'user_id' => '',
            'slug' => $this->in
        ]);
    }
}
