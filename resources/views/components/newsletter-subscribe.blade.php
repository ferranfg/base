<section id="newsletter-subscribe" class="section bg-primary lazy" data-bg="url('https://ik.imagekit.io/ferranfigueredo/home-shape__5LbPJ8lyU.png?updatedAt=1639247286245')">
    <div class="bg-overlay"></div>
    <div class="container position-relative">
        <div class="row">
            <div class="col-md-7 mt-4 pt-2 mt-sm-0 pt-sm-0">
                <div class="section-title text-center text-md-left">
                    <h5 class="title text-white title-dark mb-2">📨 {{__('Subscribe to our newsletter')}}</h5>
                    <p class="text-light para-dark">{{__('Sign up and receive the latest tips via email.')}}</p>
                </div>
                <div class="mt-4 pt-2">
                    @include('base::components.newsletter-form')
                </div>
            </div>
        </div>
    </div>
</section>