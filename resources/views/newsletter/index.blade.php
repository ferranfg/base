@extends(config('base.newsletter_template', 'layouts.web'))

@section('content')
    <section class="bg-half d-table w-100" style="background-image:url({{ $header }});padding:36px">
        <div class="bg-overlay"></div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h3>{{ config('base.newsletter_title') }}</h3>
                    <p>{{ config('base.newsletter_description') }}</p>
                    <div class="bg-light border rounded py-4 p-3">
                        @include('base::components.newsletter-form')
                    </div>
                </div>
                <div class="col-lg-4">
                    <img class="img-fluid" src="https://ik.imagekit.io/ferranfigueredo/contact_QdrD2zezSv.svg?updatedAt=1710761529194" />
                </div>
            </div>
        </div>
    </section>
@endsection