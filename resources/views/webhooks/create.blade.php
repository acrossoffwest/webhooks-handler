@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create webhook') }}</div>

                <div class="card-body">
                    <webhook-form
                        @if(isset($webhook)):webhook="{{ json_encode($webhook) }}" @endif user-id="{{ auth()->user()->safeBroadcastingToken }}" base-url="{{ config('app.url') }}"
                    ></webhook-form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
