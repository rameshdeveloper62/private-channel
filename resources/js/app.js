
require('./bootstrap');

window.moment = require('moment');

window.Vue = require('vue');

import moment_timezone from 'moment-timezone'

moment_timezone.tz.setDefault('UTC');

// add component
Vue.component('message', require('./components/MessageComponent.vue'));
Vue.component('notification', require('./components/NotificationComponent.vue'));

// human readable time format
Vue.filter('humanReadableTime', function (value) {
	var notification_time = moment(value,'YYYY-MM-DD HH:mm:ss');
	var current_time = moment(moment(),'UTC');
	return moment.duration(notification_time-current_time).humanize()+' ago';
});

const app = new Vue({
    el: '#app'
});
