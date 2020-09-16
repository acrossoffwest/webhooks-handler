<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebhookCollection;
use Illuminate\Http\Request;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');

        $query = auth()->user()->webhooks()->eloquentQuery($sortBy, $orderBy, $searchValue);

        $data = $query->paginate($length);

        return new DataTableCollectionResource($data);
    }
}
