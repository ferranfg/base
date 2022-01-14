<newsletter-form inline-template>
    <div class="subcribe-form">
        <form class="m-0">
            <div class="form-group mb-0" :class="{'is-invalid': subscribeForm.errors.has('email')}">
                <input type="email" name="email" class="rounded-pill" v-model="subscribeForm.email" placeholder="{{__('Your Email Address')}}" :disabled="subscribeForm.busy">
                <button type="button" class="btn btn-pills btn-primary" @click.prevent="submit" :disabled="subscribeForm.busy">
                    {{__('Subscribe')}}
                </button>
            </div>
            <span class="invalid-feedback" v-show="subscribeForm.errors.has('email')">
                @{{ subscribeForm.errors.get('email') }}
            </span>
            <div class="custom-control custom-checkbox mt-2 mb-0 ml-3 text-white">
                <input class="custom-control-input" :class="{'is-invalid': subscribeForm.errors.has('terms')}" type="checkbox" value="checked" id="terms" v-model="subscribeForm.terms" :disabled="subscribeForm.busy">
                <label class="custom-control-label small" for="terms">
                    {!! __('I understand and agree to the :termsOpen Terms of Service :termsClose and :privacyOpen Privacy Policy :privacyClose.', [
                        'termsOpen' => '<a href="/terms" target="_blank">',
                        'termsClose' => '</a>',
                        'privacyOpen' => '<a href="/privacy" target="_blank">',
                        'privacyClose' => '</a>',
                    ]) !!}
                <span class="invalid-feedback" v-show="subscribeForm.errors.has('terms')">
                    @{{ subscribeForm.errors.get('terms') }}
                </span>
            </div>
        </form>
    </div>
</newsletter-form>