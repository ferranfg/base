@extends('spark::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-default">
            <div class="card-header"><a href="/">{{ config('app.name') }}</a> &raquo; {{ $title }}</div>
                <div class="card-body">
                    <h1 class="mt-2 mb-4">{{ $title }}</h1>
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection