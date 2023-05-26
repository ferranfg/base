@extends(config('base.assistance_template', 'layouts.web'))

@section('content')

    <section class="bg-half d-table w-100" style="background-image:url({{ $header }});padding:36px">
        <div class="bg-overlay"></div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="footer p-3 rounded mb-5">
                        @include('base::components.chat-form')
                    </div>
                    @if (config('base.assistance_docs_view'))
                        <div class="post">
                            @include(config('base.assistance_docs_view'))
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection