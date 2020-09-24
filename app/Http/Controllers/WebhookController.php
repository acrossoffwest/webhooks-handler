<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('webhooks.index')->with('webhooks', auth()->user()->webhooks()->paginate());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('webhooks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'in' => 'required|string',
            'out' => 'nullable|string',
            'default_headers' => 'nullable|json',
            'is_active' => 'nullable|boolean',
            'default_payload' => 'nullable|json',
        ]);

        $data = $validator->validated();
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }
        $data['user_id'] = auth()->user()->id;
        Webhook::query()->create($data);

        return redirect()
            ->route('webhooks.index')
            ->withErrors($validator)
            ->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Webhook $webhook)
    {
        return view('webhooks.create')->with('webhook', $webhook);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Webhook $webhook)
    {
        $data = $request->all();
        unset($data['_method']);
        unset($data['_token']);
        $webhook->update($data);

        return view('webhooks.create')->with('webhook', $webhook->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
