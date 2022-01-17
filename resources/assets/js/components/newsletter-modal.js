let moment = require('moment');

Vue.component('newsletter-modal', {

    data() {
        return {
            dismissedAt: localStorage.getItem('mpl_newsletter_dismissed_at')
        }
    },

    mounted() {
        if ( ! this.dismissedAt || moment(this.dismissedAt).add(1, 'days').isBefore(moment())) {
            $('#subscribe-modal').modal('show');
        }
    },

    methods: {
        dismiss() {
            localStorage.setItem('mpl_newsletter_dismissed_at', moment().format('YYYY-MM-DD HH:mm:ss'));
        }
    }
});