@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create webhook') }}</div>

                <div class="card-body">
                    <webhook-form></webhook-form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
