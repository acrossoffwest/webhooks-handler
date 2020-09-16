@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Webhooks') }} <b-link href="/webhooks/create" class="btn btn-sm btn-success">Add</b-link></div>
                <div class="card-body">
                    <webhooks></webhooks>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
