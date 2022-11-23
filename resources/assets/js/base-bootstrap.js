import Vue from '$vue';

await import('./components/request-access');
await import('./components/newsletter-form');
await import('./components/startup-modal');

Vue.filter('translate', function (value, path) {
    let translation = value[Spark.locale];
    return path == undefined ? translation : `/${path}/${translation}`;
});

// Inits twemoji
if (typeof twemoji == 'object') {
    twemoji.parse(document);
}

// Plausible events
window.plausible = window.plausible || function() { (window.plausible.q = window.plausible.q || []).push(arguments) };