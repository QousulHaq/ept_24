import Vue from 'vue'
import { STATUS, MUTATION as BASE_MUTATION } from '../types'
import perform from './exam/perform'
import _ from 'lodash'

export const MUTATION = {
	...BASE_MUTATION,
	CHANGE_PARAMS: 'change_params',
	CHANGE_MATTER: 'change_matter',
	ENROLLED_EXAM: 'enrolled_exam',
	SET_ROOM: 'set_room',
};

const defaultState = {
	status: STATUS.IDLE,
	params: {
		state: 'running'
	},
	matter: {
		data: []
	},
	chosenExam: null,
	token: {
		signature: null,
		expires_in: null
	},
	room: null,
};

const getters = {
	'getExamById': state => id => state.matter.data.find(d => d.id === id),
	'hasEnrolledExam': state => !!state.chosenExam,
	'activeExam': state => state.matter.data.find(d => d.id === state.chosenExam),
	'isStarted': (state, getters) => !!_.get(getters.activeExam, 'started_at') || _.get(getters.activeExam, 'is_anytime', false),
	'isBanned': (state, getters) => _.get(getters.activeExam, 'detail.status') === 'banned'
};

const mutations = {
	[MUTATION.RESET_STATE] (state) {
		Object.assign(state, _.cloneDeep(defaultState))
	},
	[MUTATION.CHANGE_STATUS] (state, payload) {
		state.status = payload
	},
	[MUTATION.CHANGE_PARAMS] (state, payload) {
		Object.assign(state.params, payload)
	},
	[MUTATION.CHANGE_MATTER] (state, payload) {
		Object.assign(state.matter, payload)
	},
	[MUTATION.ENROLLED_EXAM] (state, { examId, token }) {
		state.chosenExam = examId;
		Object.assign(state.token, token)
	},
	[MUTATION.SET_ROOM] (state, room) {
		state.room = room
	},
};

const actions = {
	async changeParams ({ commit, dispatch }, params = {}) {
		commit(MUTATION.CHANGE_PARAMS, params);

		return await dispatch('fetchExams')
	},
	async fetchExams ({ state, commit }) {
		commit(MUTATION.CHANGE_STATUS, STATUS.FETCHING);
		const res = await Vue.laravel.request('api.client.exam', state.params);
		commit(MUTATION.CHANGE_STATUS, STATUS.IDLE);
		if (res.status === 200) {
			commit(MUTATION.CHANGE_MATTER, res.data);

			return res
		}
	},
	async enroll ({ state, commit, dispatch }, examId) {
		commit(MUTATION.CHANGE_STATUS, STATUS.FETCHING);
		const res = await Vue.laravel.request('api.client.exam.enroll', { exam: examId });
		commit(MUTATION.CHANGE_STATUS, STATUS.IDLE);
		if (res.status === 200 && res.data.status === 'success') {
			commit(MUTATION.ENROLLED_EXAM, { examId, token: res.data.data })
			dispatch('listenToExamChannel').then(() => dispatch('windowLeavingCheckerInit'))
			return res
		}
	},
	async checkQualifiedStatus ({ getters, dispatch }, { participant: detail }) {
		if (getters.activeExam.detail.id === detail.id && getters.activeExam.detail.status !== detail.status) {
			await dispatch('fetchExams')
		}
	},
	async listenToExamChannel ({ state, dispatch }) {
		const qualifyChange = (data) => dispatch('checkQualifiedStatus', data)

		const room = this._vm.$echo.join(`exam.${state.chosenExam}`)

		room.listen('Exam\\ExamStarted', () => dispatch('fetchExams'))
		room.listen('Exam\\Participant\\ParticipantDisqualified', qualifyChange)
		room.listen('Exam\\Participant\\ParticipantQualified', qualifyChange)
	},
	async windowLeavingCheckerInit ({ state, rootState }) {
		const room = this
			._vm.$echo.connector
			.channels['presence-exam.' + state.chosenExam]

		document
			.getElementsByTagName('html')[0]
			.onmouseleave = () => {
			this._vm.$notification.warn({
				message: 'you\'re open another window.',
				description: 'proctor notice it.',
				placement: 'bottomLeft'
			})

			room.whisper('security', {
				hash: rootState.auth.user.hash,
				type: 'mouseleave'
			})
		}
	}
};

export default {
	namespaced: true,
	state: _.cloneDeep(defaultState),
	getters,
	mutations,
	actions,
	modules: { perform }
}
