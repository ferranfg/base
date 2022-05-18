let moment = require('moment');

Vue.component('startup-modal', {

    props: ['modalId', 'offset', 'expiration'],

    data() {
        return {
            dismissedAt: localStorage.getItem(`${this.modalId}_dismissed_at`)
        }
    },

    mounted() {
        if ( ! this.dismissedAt || moment(this.dismissedAt).add(this.expiration, 'days').isBefore(moment())) {
            $(window).on('scroll', () => {
                if (window.pageYOffset > this.offset) {
                    $(`#${this.modalId}`).modal('show');

                    $(window).off('scroll');
                }
            });
        }
    },

    methods: {
        dismiss() {
            localStorage.setItem(`${this.modalId}_dismissed_at`, moment().format('YYYY-MM-DD HH:mm:ss'));
        }
    }
});