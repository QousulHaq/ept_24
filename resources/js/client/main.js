import Vue from 'vue'
import App from './App'
import AntD from 'ant-design-vue'
import store from './store'
import router from './router'

import * as Sentry from '@sentry/browser'
import { Vue as VueIntegration } from '@sentry/integrations'

import './plugins/axios'
import '../lib/echo'
import './plugins/event'
import './plugins/howler'
import '../lib/laravel'
import { authMiddleware, presenceMiddleware } from './middleware'

authMiddleware(router)
presenceMiddleware(router)

Vue.use(AntD)

// noinspection JSUnusedGlobalSymbols
new Vue({
	router,
	store,
	mounted () {
		if (this.$store.getters['auth/authenticated']) {
			const token = this.$store.state.auth.credential.access_token
			this.$echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${token}`

			this.$nextTick(function () {
				// !! Socket listen to everything that required by application
				// !!
				this.$store.dispatch('auth/listenAttendance').then(r => r)
				if (this.$store.state.auth.user.username) this.$store.dispatch('notification/listen').then(r => r)
				if (this.$store.getters['exam/hasEnrolledExam']) {
					this.$store.dispatch('exam/listenToExamChannel').then(() => this.$store.dispatch('exam/windowLeavingCheckerInit'))
				}
			})
		}
	},
	render: h => h(App),
}).$mount('#app')

if (process.env.MIX_SENTRY_VUE_DSN) {
	Sentry.init({
		dsn: process.env.MIX_SENTRY_VUE_DSN,
		integrations: [
			new VueIntegration({ Vue, attachProps: true, logErrors: true })
		],
	})
}

if (process.env.NODE_ENV !== 'production') {
	window.vm = Vue
} else {
	window.onbeforeunload = () => {
		Vue.prototype.$notification.warning({
			message: 'please avoid reload page!',
			description: 'Something might happens while you reload page and proctor will know when you reload it ...',
			placement: 'bottomLeft'
		})

		return 'please avoid reload page!'
	}
}
