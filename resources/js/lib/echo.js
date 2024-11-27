import Vue from 'vue'
import Echo from 'laravel-echo'
window.Pusher = require('pusher-js')

const echo = new Echo({
	broadcaster: 'pusher',
	enabledTransports: ['ws', 'wss'],
	key: process.env.MIX_PUSHER_APP_KEY,
	cluster: process.env.MIX_PUSHER_APP_CLUSTER,
	wsHost: process.env.MIX_PUSHER_HOST ?? window.location.hostname,
	wsPort: process.env.MIX_PUSHER_PORT ?? window.location.port,
	httpHost: process.env.MIX_PUSHER_HOST,
	forceTLS: false,
	disableStats: true,
})

class EchoPlugin extends Plugin {
	static install () {
		window.Echo = echo
		Vue.$echo = echo
		Object.defineProperties(Vue.prototype, {
			echo: {
				get () {
					return echo
				}
			},
			$echo: {
				get () {
					return echo
				}
			}
		})
	}
}

Vue.use(EchoPlugin)
