@extends('layouts.web')

@section('content')
    <section class="bg-half d-table w-100 lazy" data-bg="url({{ $photo_url }})">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="page-next-level">
                        <div class="title-heading text-center">
                            <h1 class="text-white title-dark mb-0">{{ config('base.guides_title') }}</h1>
                        </div>
                        <div class="page-next text-center">
                            <nav class="d-inline-block">
                                <ul class="breadcrumb bg-white rounded shadow mb-0">
                                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                                    <li class="breadcrumb-item active">Guides</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="position-relative">
        <div class="shape overflow-hidden text-white">
            <svg viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z" fill="currentColor"></path>
            </svg>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="row">
                @foreach ($tags as $tag)
                    <div class="col-md-6 col-12 mt-4 pt-2">
                        <h5>{{ $tag->name }}</h5>
                        <ul class="list-unstyled mt-4 mb-0">
                            @foreach ($tag->posts as $question)
                                <li class="mt-2">
                                    <a href="{{ $question->canonical_url }}" class="text-muted"><span class="fa fa-arrow-right text-primary"></span> {{ $question->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection