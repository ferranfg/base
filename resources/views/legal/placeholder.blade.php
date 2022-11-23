@extends('base::layouts.web')

@section('main')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="mt-2 mb-4">{{ $title }}</h1>
                {!! $content !!}
            </div>
        </div>
    </div>
@endsection