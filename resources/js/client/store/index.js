import Vue from 'vue'
import Vuex from 'vuex'
import createLogger from 'vuex/dist/logger'
import createPersistedState from 'vuex-persistedstate'
import { MUTATION } from './types'

import auth from './modules/auth'
import exam from './modules/exam'
import notification from './modules/notification'

Vue.use(Vuex);

const store = new Vuex.Store({
	strict: true,
	plugins: [
		...(process.env.NODE_ENV !== 'production') ? [createLogger()] : [],
		createPersistedState({ key: 'etefl', storage: sessionStorage })
	],
	actions: {
		resetState ({ commit }) {
			commit(`auth/${MUTATION.RESET_STATE}`);
			commit(`exam/${MUTATION.RESET_STATE}`);
			commit(`exam/perform/${MUTATION.RESET_STATE}`)
		}
	},
	modules: {
		auth,
		exam,
		notification
	}
});

Vue.$store = store;
if (process.env.NODE_ENV !== 'production')
	window.$store = store;

export default store
