import { STATUS, MUTATION as BASE_MUTATION } from '../types'
import { stringify } from 'querystring'
import _ from 'lodash'
import { isAfter, addSeconds } from 'date-fns'
import Vue from 'vue'

export const MUTATION = {
	...BASE_MUTATION,
	CHANGE_CONNECTION_STATE: 'change_connection_state',
	SAVE_USER: 'save_user',
	SAVE_CREDENTIAL: 'save_credential',
	SAVE_ACTIVE_USERS: 'save_active_users',
	ADD_ACTIVE_USER: 'add_active_user',
	REMOVE_ACTIVE_USER: 'remove_active_user',
	CHANGE_CREDENTIAL_STATE: 'change_credential_state',
}

const defaultState = {
	status: STATUS.IDLE,
	connection_state: 'initialized',
	credential: {
		state: null,
		access_token: undefined
	},
	lastFetched: null,
	user: {},
	active_users: []
}

const getters = {
	'authenticated': state => !!state.credential?.access_token,
	'tokenExpired': state => !state.lastFetched
		?? isAfter(new Date(), addSeconds(state.lastFetched, _.get(state, 'credential.expires_in')))
}

const mutations = {
	[MUTATION.RESET_STATE] (state) {
		Object.assign(state, _.cloneDeep(defaultState))
	},
	[MUTATION.CHANGE_STATUS] (state, payload) {
		state.status = payload
	},
	[MUTATION.CHANGE_CONNECTION_STATE] (state, payload) {
		state.connection_state = payload
	},
	[MUTATION.CHANGE_CREDENTIAL_STATE] (state, payload) {
		state.credential.state = payload
	},
	[MUTATION.SAVE_CREDENTIAL] (state, payload) {
		Object.assign(state.credential, payload);
		state.lastFetched = new Date();

		// update token auth for echo
		Vue.prototype.$echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${state.credential.access_token}`
		Vue.prototype.$echo.connector.pusher.config.auth.headers['Accept'] = 'application/json'
	},
	[MUTATION.SAVE_ACTIVE_USERS] (state, payload) {
		state.active_users = payload
	},
	[MUTATION.ADD_ACTIVE_USER] (state, payload) {
		state.active_users.push(payload)
	},
	[MUTATION.REMOVE_ACTIVE_USER] (state, payload) {
		state.active_users.splice(state.active_users.indexOf(u => u.id === payload.id), 1)
	},
	[MUTATION.SAVE_USER] (state, payload) {
		state.user = payload
	}
}

const actions = {
	getCode ({ state, commit }) {
		if (state.credential.state === null) {
			commit(
				MUTATION.CHANGE_CREDENTIAL_STATE,
				Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
			)
		}

		window.location.replace(window.location.origin + '/oauth/authorize?' + stringify({
			client_id: process.env.MIX_VUE_APP_CLIENT_ID ?? 666,
			redirect_uri: process.env.MIX_VUE_APP_REDIRECT_URL ?? (window.location.origin + '/client'),
			response_type: 'token',
			state: state.credential.state,
		}))
	},
	logout ({ dispatch }) {
		window.onbeforeunload = null
		Vue.axios.post('/auth/logout')
			.then(() => dispatch('resetState', {}, { root: true }))
			.then(() => window.location.href = '/')
	},
	async login ({ state, dispatch, commit }, credential) {
		if (state.credential.state === credential?.state) {
			commit(MUTATION.SAVE_CREDENTIAL, credential)
			dispatch('listenAttendance')
			const user = await dispatch('getUser')

			dispatch('notification/listen', {}, { root: true })

			return user
		}

		throw new Error('state doesn\'t seem right')
	},
	async listenAttendance ({ commit }) {
		Vue.prototype.$echo.join('attendance').here(users => {
				commit(MUTATION.SAVE_ACTIVE_USERS, users)
			}).joining((user) => {
				commit(MUTATION.ADD_ACTIVE_USER, user)
			}).leaving((user) => {
				commit(MUTATION.REMOVE_ACTIVE_USER, user)
			})

		Vue.prototype.$echo.connector
			.pusher.connection.bind('state_change', states => commit(MUTATION.CHANGE_CONNECTION_STATE, states.current))
	},
	async getUser ({ commit }) {
		const { data } = await Vue.laravel.request('api.client.user')
		commit(MUTATION.SAVE_USER, data)

		return data
	}
}

export default {
	namespaced: true,
	state: _.cloneDeep(defaultState),
	getters,
	mutations,
	actions
}
