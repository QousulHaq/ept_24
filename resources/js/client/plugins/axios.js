import axios from 'axios'
import Vue from 'vue'

const _axios = axios.create({
	headers: {
		'Accept': 'application/json'
	}
})

_axios.interceptors.request.use(config => {
	if (Vue.$store.getters['auth/authenticated']) {
		const token = Vue.$store.state.auth.credential.access_token
		config.headers['Authorization'] = 'Bearer ' + token
	}

	if (Vue.$store.getters['exam/hasEnrolledExam']) {
		config.headers['X-Signature-Enroll'] = Vue.$store.state.exam.token.signature
	}

	return config
})

_axios.interceptors.response.use(_ => _, async error => {
	// await Vue.swal('ERROR', _.get(error, 'response.data.message', ''), 'error')

	throw error
})

class AxiosPlugin extends Plugin {
	static install () {
		Vue.axios = _axios
		window.$axios = _axios
		Vue.$axios = _axios
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

Vue.use(AxiosPlugin)
