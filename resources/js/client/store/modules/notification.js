import Vue from 'vue'
import { MUTATION as BASE_MUTATION } from '../types'
import _ from 'lodash'

export const MUTATION = {
	...BASE_MUTATION,
	ADD_NOTIFICATION: 'add_notification'
}

const defaultState = {
	matter: []
}

const mutations = {
	[MUTATION.RESET_STATE] (state) {
		Object.assign(state, _.cloneDeep(defaultState))
	},
	[MUTATION.ADD_NOTIFICATION] (state, payload) {
		if (payload instanceof Notification) {
			state.matter.push(payload)
		}
	}
}

const actions = {
	listen ({ rootState, dispatch }) {
		Vue.prototype.$echo.private('notification.' + rootState.auth.user.username)
			.notification(notification => {
				dispatch('add', notification)
			})
	},
	add ({ commit }, payload) {
		const method = _.get(Vue.prototype, `$${payload.method}`)
		if (typeof method === 'function') {
			method({...payload})
			if (payload.save)
				commit(MUTATION.ADD_NOTIFICATION, payload)
		} else {
			console.warn('there is no method named $' + payload.method)
		}
	}
}

export default {
	namespaced: true,
	state: _.cloneDeep(defaultState),
	mutations,
	actions,
}
