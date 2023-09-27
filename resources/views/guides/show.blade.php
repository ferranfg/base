@extends('base::blog.post')

@section('post-breadcrumb')
    <div class="page-next text-center">
        <nav class="d-inline-block">
            <ul class="breadcrumb bg-white rounded shadow mb-0">
                <li class="breadcrumb-item"><a href="/guides">{{ __('Guides') }}</a></li>
                <li class="breadcrumb-item active">{{ $post->name }}</li>
            </ul>
        </nav>
    </div>
@endsection
