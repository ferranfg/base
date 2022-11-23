import Vue from '$vue';

Vue.component('startup-modal', {

    props: ['modalId', 'offset', 'expiration'],

    data() {
        return {
            dismissedAt: localStorage.getItem(`${this.modalId}-dismissed-at`)
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
            localStorage.setItem(`${this.modalId}-dismissed-at`, moment().format('YYYY-MM-DD HH:mm:ss'));
        }
    }
});