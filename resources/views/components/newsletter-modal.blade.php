<startup-modal modal-id="subscribe-modal" offset="700" expiration="1" inline-template>
    <div id="subscribe-modal" class="modal fullscreen-modal fade bg-primary text-white text-center lazy" data-bg="url('https://ik.imagekit.io/ferranfigueredo/home-shape__5LbPJ8lyU.png?updatedAt=1639247286245')">
        <div class="bg-overlay"></div>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h3>{{ config('base.newsletter_title') }}</h3>
                    <p>{{ config('base.newsletter_description') }}</p>
                    @include('base::components.newsletter-form')
                    <button type="button" class="btn btn-lg btn-link text-white" data-dismiss="modal" v-on:click="dismiss">
                        <span>{{ __('Let me read it first') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</startup-modal>