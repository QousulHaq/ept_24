/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import * as CONSTANT from './const';
import './plugins/swal'
import './plugins/axios'
import '../lib/echo'
import '../lib/laravel'
import { formatISO9075 } from 'date-fns'
import Vue from 'vue'

import { createInertiaApp } from '@inertiajs/inertia-react';
import React from 'react';
import { createRoot } from 'react-dom/client';

import { AxiosRequestConfig, AxiosResponse } from 'axios'

require('./bootstrap');

window.Vue = Vue;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./public', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.mixin({
	data: () => ({
		...CONSTANT
	}),
	methods: {
		/**
		 * @param url : String
		 */
		redirect (url) {
			window.location.href = url
		},
		reload () {
			window.location.reload()
		},
		dateFormat (date = null) {
			if (date == null) {
				return date;
			}

			return formatISO9075(new Date(date))
		},
		dashIfNull (value = null) {
			return value ?? null
		},
		/**
		 *
		 * @param routeName
		 * @param data
		 * @param config : AxiosRequestConfig
		 * @param withLoading
		 * @return {Promise<AxiosResponse>}
		 */
		async request (routeName, data = {}, config = {}, withLoading = true) {
			const el = document.createElement('i');
			el.classList.add('fa', 'fa-circle-notch', 'fa-spin');

			if (withLoading) {
				this.swal({
					content: {
						element: el,
					},
					buttons: false,
					closeOnClickOutside: false,
					closeOnEsc: false,
				});
			}

			const route = await this.laravel.router.get(routeName, data);

			try {
				const res = await this.axios.request(Object.assign({}, {
					method: route.method,
					url: (route.domain ?? '') + '/' + route.uri,
					data: data
				}, config));

				if (withLoading) this.swal.close();

				return res
			} catch (e) {
				if (withLoading) this.swal.close();

				throw e
			}
		},
		checkImage (attachment) {
			if (attachment === undefined || attachment === null) {
				return '/assets/img/avatar/avatar-1.png'
			} else {
				return attachment.url
			}
		},
	}
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

new Vue({
    el: '#app-vue',
		mounted() {

		}
});

createInertiaApp({
    resolve: name => require(`./Pages/${name}`).default, // Path ke folder Pages
	setup({ el, App, props }) {
		createRoot(el).render(<App {...props} />)
	},
});