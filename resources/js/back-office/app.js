// /**
//  * First we will load all of this project's JavaScript dependencies which
//  * includes Vue and other libraries. It is a great starting point when
//  * building robust, powerful web applications using Vue and Laravel.
//  */
// import * as CONSTANT from './const';
// import './plugins/swal'
// import './plugins/axios'
// import '../lib/echo'
// import '../lib/laravel'
// import { formatISO9075 } from 'date-fns'
// import Vue from 'vue'

// import { createInertiaApp } from '@inertiajs/inertia-react';
// import React from 'react';
// import { createRoot } from 'react-dom/client';

// import { AxiosRequestConfig, AxiosResponse } from 'axios'

// require('./bootstrap');

// window.Vue = Vue;

// /**
//  * The following block of code may be used to automatically register your
//  * Vue components. It will recursively scan this directory for the Vue
//  * components and automatically register them with their "basename".
//  *
//  * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
//  */

// const files = require.context('./public', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.mixin({
// 	data: () => ({
// 		...CONSTANT
// 	}),
// 	methods: {
// 		/**
// 		 * @param url : String
// 		 */
// 		redirect (url) {
// 			window.location.href = url
// 		},
// 		reload () {
// 			window.location.reload()
// 		},
// 		dateFormat (date = null) {
// 			if (date == null) {
// 				return date;
// 			}

// 			return formatISO9075(new Date(date))
// 		},
// 		dashIfNull (value = null) {
// 			return value ?? null
// 		},
// 		/**
// 		 *
// 		 * @param routeName
// 		 * @param data
// 		 * @param config : AxiosRequestConfig
// 		 * @param withLoading
// 		 * @return {Promise<AxiosResponse>}
// 		 */
// 		async request (routeName, data = {}, config = {}, withLoading = true) {
// 			const el = document.createElement('i');
// 			el.classList.add('fa', 'fa-circle-notch', 'fa-spin');

// 			if (withLoading) {
// 				this.swal({
// 					content: {
// 						element: el,
// 					},
// 					buttons: false,
// 					closeOnClickOutside: false,
// 					closeOnEsc: false,
// 				});
// 			}

// 			const route = await this.laravel.router.get(routeName, data);

// 			try {
// 				const res = await this.axios.request(Object.assign({}, {
// 					method: route.method,
// 					url: (route.domain ?? '') + '/' + route.uri,
// 					data: data
// 				}, config));

// 				if (withLoading) this.swal.close();

// 				return res
// 			} catch (e) {
// 				if (withLoading) this.swal.close();

// 				throw e
// 			}
// 		},
// 		checkImage (attachment) {
// 			if (attachment === undefined || attachment === null) {
// 				return '/assets/img/avatar/avatar-1.png'
// 			} else {
// 				return attachment.url
// 			}
// 		},
// 	}
// });

// /**
//  * Next, we will create a fresh Vue application instance and attach it to
//  * the page. Then, you may begin adding components to this application
//  * or customize the JavaScript scaffolding to fit your unique needs.
//  */

// new Vue({
//     el: '#app-vue',
// 		mounted() {

// 		}
// });

// createInertiaApp({
//     resolve: name => require(`./Pages/${name}`).default, // Path ke folder Pages
// 	setup({ el, App, props }) {
// 		createRoot(el).render(<App {...props} />)
// 	},
// });

import * as CONSTANT from './const';
import axios from 'axios';
import swal from 'sweetalert';
import Echo from 'laravel-echo';
import { formatISO9075 } from 'date-fns';
import { createInertiaApp } from '@inertiajs/inertia-react';
import React from 'react';
import { createRoot } from 'react-dom/client';

// Setup axios
let token = document.head.querySelector('meta[name="csrf-token"]');
const _axios = axios.create({
    headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token ? token.content : ''
    }
});

// Axios interceptor untuk handling error
_axios.interceptors.response.use(
    response => response,
    async error => {
        let message = '';
        if (error.response?.status === 422 && error.response.data?.errors) {
            message += Object.values(error.response.data.errors).flat().join('\n');
        }
        await swal(error.response?.data?.message || '', message, 'error');
        throw error;
    }
);

// Setup Echo
window.Pusher = require('pusher-js');
const echo = new Echo({
    broadcaster: 'pusher',
    enabledTransports: ['ws', 'wss'],
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: process.env.MIX_PUSHER_HOST ?? window.location.hostname,
    wsPort: process.env.MIX_PUSHER_PORT ?? window.location.port,
    wsPort: process.env.MIX_PUSHER_PORT ?? window.location.port,
    httpHost: process.env.MIX_PUSHER_HOST,
    forceTLS: false,
    disableStats: true,
});

// Utility functions yang bisa digunakan di komponen React
export const utils = {
    redirect: (url) => {
        window.location.href = url;
    },
    reload: () => {
        window.location.reload();
    },
    dateFormat: (date) => {
        if (date == null) return date;
        return formatISO9075(new Date(date));
    },
    dashIfNull: (value) => {
        return value ?? null;
    },
    checkImage: (attachment) => {
        if (attachment === undefined || attachment === null) {
            return '/assets/img/avatar/avatar-1.png';
        }
        return attachment.url;
    },
    // Laravel route helper
    request: async (routeName, data = {}, config = {}, withLoading = true) => {
        if (withLoading) {
            swal({
                content: {
                    element: 'i',
                    attributes: {
                        className: 'fa fa-circle-notch fa-spin'
                    }
                },
                buttons: false,
                closeOnClickOutside: false,
                closeOnEsc: false,
            });
        }

        try {
            // Ambil route dari API
            const routeRes = await _axios.get('/api');
            const route = routeRes.data.find(r => r.name === routeName);
            
            if (!route) throw new Error('Laravel route not found');

            // Process URI parameters
            let uri = route.uri;
            const queryCandidates = {};
            
            for (const [key, value] of Object.entries(data)) {
                if (uri.includes(`{${key}}`)) {
                    uri = uri.replace(`{${key}}`, value);
                    delete data[key];
                } else if (route.method === 'HEAD' || route.method === 'GET') {
                    queryCandidates[key] = value;
                    delete data[key];
                }
            }

            // Add query string if needed
            if (Object.keys(queryCandidates).length > 0) {
                uri += '?' + new URLSearchParams(queryCandidates).toString();
            }

            const res = await _axios.request({
                method: route.method,
                url: `${route.domain || ''}/${uri}`,
                data,
                ...config
            });

            if (withLoading) swal.close();
            return res;

        } catch (e) {
            if (withLoading) swal.close();
            throw e;
        }
    }
};

// Expose globals
window.$axios = _axios;
window.axios = _axios;
window.swal = swal;
window.Echo = echo;

// Setup Inertia
createInertiaApp({
    resolve: name => require(`./Pages/${name}`).default,
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});

// Import Stisla scripts
require('./bootstrap');
require('./stisla/stisla');
require('./stisla/scripts');
require('./stisla/custom');