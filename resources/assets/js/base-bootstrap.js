require('./components/request-access');
require('./components/newsletter-form');
require('./components/startup-modal');
require('./components/chat-form');

Vue.filter('translate', function (value, path) {
    let translation = value[Spark.locale];
    return path == undefined ? translation : `/${path}/${translation}`;
});

// Plausible events
window.plausible = window.plausible || function() { (window.plausible.q = window.plausible.q || []).push(arguments) };