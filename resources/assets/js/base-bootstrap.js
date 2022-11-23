import Vue from '$vue';

import './components/request-access';
import './components/newsletter-form';
import './components/startup-modal';

Vue.filter('translate', function (value, path) {
    let translation = value[Spark.locale];
    return path == undefined ? translation : `/${path}/${translation}`;
});

// Inits twemoji
if (typeof window.twemoji == 'object') {
    window.twemoji.parse(document);
}

// Plausible events
window.plausible = window.plausible || function() { (window.plausible.q = window.plausible.q || []).push(arguments) };