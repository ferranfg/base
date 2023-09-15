@extends('base::blog.post')

@section('post-breadcrumb')
    <div class="page-next text-center">
        <nav class="d-inline-block">
            <ul class="breadcrumb bg-white rounded shadow mb-0">
                <li class="breadcrumb-item"><a href="/guides">Guides</a></li>
                <li class="breadcrumb-item active">{{ $post->name }}</li>
            </ul>
        </nav>
    </div>
@endsection

@section('post-footer')
    @if ($embeddings->count())
        <div class="post">
            <h2>Related Guides:</h2>
            <ul class="list-unstyled mt-4 mb-0">
                @foreach ($embeddings as $question)
                    <li class="mt-2">
                        <a href="{{ $question->canonical_url }}" class="text-muted"><span class="fa fa-arrow-right text-primary"></span> {{ $question->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection