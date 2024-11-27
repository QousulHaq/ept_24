import Vue from 'vue'

// noinspection JSUnusedGlobalSymbols
const events = {
	log ({ content }) {
		console.log(content)
	},
	// next session or item depend on active section
	next ({ data }) {
		Vue.$store.dispatch('exam/perform/next', data).then(_ => _)
	},
	// end an perform
	end () {
		// Vue.$store.dispatch()
	},
	disqualified () {
	}
}

class EventPlugin extends Plugin {
	static install () {
		Object.defineProperties(Vue.prototype, {
			$events: {
				get () {
					return events
				}
			}
		})
	}
}

Vue.use(EventPlugin)
