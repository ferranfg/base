<newsletter-subscribe inline-template>
    <div class="subscribe-newsletter">
        <h5 class="text-light footer-head">Newsletter</h5>
        <p class="mt-4">Sign up and receive the latest tips via email.</p>
        <form>
            <div class="row">
                <div class="col-lg-12">
                    <div class="foot-subscribe form-group">
                        <label>Write your email <span class="text-danger">*</span></label>
                        <div class="position-relative" :class="{'is-invalid': subscribeForm.errors.has('email')}">
                            <i class="fa fa-envelope icon-sm icons"></i>
                            <input type="text" class="form-control pl-5 rounded" v-model="subscribeForm.email" placeholder="{{__('Your Email Address')}}" :disabled="subscribeForm.busy">
                        </div>
                        <span class="invalid-feedback" v-show="subscribeForm.errors.has('email')">
                            @{{ subscribeForm.errors.get('email') }}
                        </span>
                        <div class="custom-control custom-checkbox mt-2 mb-0">
                            <input class="custom-control-input" :class="{'is-invalid': subscribeForm.errors.has('terms')}" type="checkbox" value="checked" id="terms" v-model="subscribeForm.terms" :disabled="subscribeForm.busy">
                            <label class="custom-control-label font-weight-normal" for="terms">I understand and agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a>.</label>
                            <span class="invalid-feedback" v-show="subscribeForm.errors.has('terms')">
                                @{{ subscribeForm.errors.get('terms') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <button type="button" class="btn btn-soft-primary btn-block" @click.prevent="submit" :disabled="subscribeForm.busy">
                        {{__('Subscribe')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</newsletter-subscribe>