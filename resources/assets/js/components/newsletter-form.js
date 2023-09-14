import Vue from '$vue';
import { SparkForm } from '../forms/form';

Vue.component('newsletter-form', {

    data() {
        return {
            subscribeForm: new SparkForm({
                email: '',
                terms: false,
            })
        };
    },

    methods: {
        submit() {
            Spark.post('/newsletter/subscribe', this.subscribeForm).then(() => {
                Swal.fire({
                    title: __('Got It!'),
                    text: __('We have received your message and will respond soon!'),
                    type: 'success',
                    showConfirmButton: false,
                    timer: 2000
                });

                $('#subscribe-modal').modal('hide');

                this.subscribeForm.reset();
                this.mounted();
            });
        }
    }
});