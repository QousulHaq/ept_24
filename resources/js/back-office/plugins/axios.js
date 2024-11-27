import axios from 'axios'
import Vue from 'vue'

let token = document.head.querySelector('meta[name="csrf-token"]');

if (! token) {
	console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

const _axios = axios.create({
	headers: {
		'Accept': 'application/json',
		'X-CSRF-TOKEN': token ? token.content : ''
	}
});

_axios.interceptors.response.use(_ => _, async error => {
	let message = '';
	if (error.response.status === 422 && error.response.data?.errors) {
		message += _.flatten(_.values(error.response.data?.errors)).join('\n')
	}
	await Vue.swal(_.get(error, 'response.data.message', ''), message, 'error');

	throw error
});

class AxiosPlugin extends Plugin {
	static install () {
		Vue.axios = _axios;
		Vue.$axios = _axios;
		window.$axios = _axios;
		window.axios = _axios;
		Object.defineProperties(Vue.prototype, {
			axios: {
				get () {
					return _axios
				}
			},
			$axios: {
				get () {
					return _axios
				}
			}
		})
	}
}

Vue.use(AxiosPlugin);
