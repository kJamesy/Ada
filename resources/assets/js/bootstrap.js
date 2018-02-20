
window._ = require('lodash');

import Popper from 'popper.js';
try {
    window.jQuery = window.$ = require('jquery');
    window.Popper = Popper;

    require('bootstrap');
} catch (e) {}


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

require('sweetalert');

window.moment = require('moment');

window.Vue = require('vue');
require('vue-resource');

Vue.http.interceptors.push((request, next) => {
    request.headers.set('X-CSRF-TOKEN', Laravel.csrfToken);

    next();
});

//Amanda
require('../vendor/amanda-admin/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min');
require('../vendor/amanda-admin/lib/jquery-toggles/toggles.min');
// require('../vendor/amanda-admin/lib/d3/d3');
// require('../vendor/amanda-admin/lib/rickshaw/rickshaw.min');
require('../vendor/amanda-admin/js/amanda');
require('../vendor/amanda-admin/js/ResizeSensor');
// require('../vendor/amanda-admin/js/dashboard');