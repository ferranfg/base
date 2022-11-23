/*
 * Load various JavaScript modules that assist Spark.
 */

import axios from 'axios';
import _ from 'lodash';
import moment from 'moment';
import Vue from '$vue';
import SparkHttp from './forms/http';

window.axios = axios;
window._ = _;
window.moment = moment;
window.__ = (key, replace) => {
    var translation = Spark.translations[key] ? Spark.translations[key] : key;

    _.forEach(replace, (value, key) => {
        translation = translation.replace(':'+key, value);
    });

    return translation;
};

/**
 * Add additional HTTP / form helpers to the Spark object.
 */
$.extend(window.Spark, SparkHttp);

/*
 * Load Vue, the JavaScript framework used by Spark.
 */
window.Bus = new Vue();

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': Spark.csrfToken
};

/**
 * Intercept the incoming responses.
 *
 * Handle any unexpected HTTP errors and pop up modals, etc.
 */
window.axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error.response === undefined) {
        return Promise.reject(error);
    }

    switch (error.response.status) {
        case 401:
            window.axios.get('/logout');
            $('#modal-session-expired').modal('show');
            break;

        case 402:
            window.location = '/settings#/subscription';
            break;
    }

    return Promise.reject(error);
});
