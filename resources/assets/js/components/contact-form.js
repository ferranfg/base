Vue.component('contact-form', {

    data() {
        return {
            supportForm: new SparkForm({
                from: '',
                subject: '',
                message: '',
            }),
        };
    },

    methods: {
        /**
         * Send a customer support request.
         */
        sendSupportRequest() {
            Spark.post('/support/email', this.supportForm)
                .then(() => {
                    $('#modal-support').modal('hide');

                    this.showSupportRequestSuccessMessage();

                    this.supportForm.subject = '';
                    this.supportForm.message = '';
                });
        },

        /**
         * Show an alert informing the user their support request was sent.
         */
        showSupportRequestSuccessMessage() {
            Swal.fire({
                title: __('Got It!'),
                text: __('We have received your message and will respond soon!'),
                icon: 'success',
                showConfirmButton: false,
                timer: 2000
            });
        }
    },

    template: `
        <form role="form">
            <!-- From -->
            <div class="form-group">
                <input id="support-from" type="text" class="form-control" v-model="supportForm.from" :class="{'is-invalid': supportForm.errors.has('from')}" :placeholder="__('Your Email Address')">

                <span class="invalid-feedback" v-show="supportForm.errors.has('from')">
                    {{ supportForm.errors.get('from') }}
                </span>
            </div>

            <!-- Subject -->
            <div class="form-group">
                <input id="support-subject" type="text" class="form-control" v-model="supportForm.subject" :class="{'is-invalid': supportForm.errors.has('subject')}" :placeholder="__('Subject')">

                <span class="invalid-feedback" v-show="supportForm.errors.has('subject')">
                    {{ supportForm.errors.get('subject') }}
                </span>
            </div>

            <!-- Message -->
            <div class="form-group">
                <textarea class="form-control" rows="7" v-model="supportForm.message" :class="{'is-invalid': supportForm.errors.has('message')}" :placeholder="__('Message')"></textarea>

                <span class="invalid-feedback" v-show="supportForm.errors.has('message')">
                    {{ supportForm.errors.get('message') }}
                </span>
            </div>

            <button type="button" class="btn btn-primary" @click.prevent="sendSupportRequest" :disabled="supportForm.busy">
                <i class="fa fa-btn fa-paper-plane"></i> {{ __('Send') }}
            </button>
        </form>
    `,
});