import { STATUS, MUTATION as BASE_MUTATION } from '../../types'
import _ from 'lodash'

export const MUTATION = {
	...BASE_MUTATION,
	CHANGE_ACTIVE: 'change_active',
	CHANGE_MATTER: 'change_matter',
	UPDATE_SECTION: 'update_section',
	UPDATE_ITEM: 'update_item',
	UPDATE_ATTEMPT: 'update_attempt',
	SORT_MATTER: 'sort_matter',
	RESET_ITEM_TOTAL: 'reset_item_total',
	ADD_ITEM_TOTAL: 'add_item_total',
	ADVANCE_LOADED_ITEM_TOTAL: 'advance_loaded_item_total',
}

const defaultState = {
	status: STATUS.IDLE,
	active: {
		section: null,
		item: null,
	},
	matter: {
		sections: [],
	},
	items_count: {
		total: 0,
		loaded: 0,
	}
}

const getters = {
	'sections': function (state) {
		return state.matter.sections
	},
	'isDone': (state) => state.matter.sections.every(s => _.get(s, 'ended_at')),
	'activeSection': function (state) {
		if (state.matter.sections.length === 0)
			return null

		return state.matter.sections.find(s => s.id === state.active.section)
	},
	'sectionItemsAnswered': function (state, getters) {
		return getters.sections.map(section => section.items.filter(item => item.attempts.some(attempt => attempt['answer'] !== null)).map(item => item.id)).flat()
	},
	'itemDuration': function (state, getters) {
		if (! getters.activeSection)
			return null

		return _.get(getters.activeSection, 'item_duration', _.get(getters.section, 'config.item_duration', false))
	},
	'activeItem': function (state, getters) {
		if (! getters.activeSection)
			return null

		if (_.get(getters.activeSection, 'items.length', 0) === 0)
			return null

		// item duration depend with remaining_time property from response...
		// so state.active.item will be IGNORED...
		// if (getters.itemDuration) {
			// if detected no progress, get first items that sorted in `section` getters
			// if (getters.activeSection.items.every(item => _.get(item, 'remaining_time', 0) !== 0))
			// 	return getters.activeSection.items[0]

			// getting last progress of item
			// return getters.activeSection.items
			//  	.find(item => parseInt(_.get(item, 'remaining_time', 0)) !== 0) ?? null
		// } else {
		if (! state.active.item)
			return null

		return getters.activeSection.items.find(item => item.id === state.active.item) ?? null
		// }
	},
	'activeAttempt': function(state, getters) {
		return _.get(getters.activeItem, 'attempts', [])
			.find(a => _.get(a, 'attempt_number', -1) === _.get(getters.activeSection, 'attempts'))
			?? null
	},
	'itemLoadedPercentage': function (state) {
		return parseInt(state.items_count.loaded / state.items_count.total * 100)
	}
}

const mutations = {
	[MUTATION.RESET_STATE] (state) {
		Object.assign(state, _.cloneDeep(defaultState))
	},
	[MUTATION.CHANGE_STATUS] (state, payload) {
		state.status = payload
	},
	[MUTATION.CHANGE_MATTER] (state, payload) {
		Object.assign(state.matter, payload)
	},
	[MUTATION.CHANGE_ACTIVE] (state, payload = {}) {
		Object.assign(state, {
			active: {
				...state.active,
				...payload
			}
		})
	},
	[MUTATION.UPDATE_SECTION] (state, payload = {}) {
		const sectionIndex = state.matter.sections.findIndex(s => s.id === payload.id)
		if (sectionIndex > -1) {
			let sections = [...state.matter.sections]
			sections.fill(Object.assign(sections[sectionIndex], payload), sectionIndex, sectionIndex + 1)
			Object.assign(state, {
				matter: {
					sections
				}
			})
		}
	},
	[MUTATION.UPDATE_ITEM] (state, payload = {}) {
		const sectionIndex = state.matter.sections.findIndex(s => s.id === state.active.section)
		if (sectionIndex > -1 && payload.hasOwnProperty('id')) {
			const itemIndex = state.matter.sections[sectionIndex].items.findIndex(i => i.id === payload.id)
			if (itemIndex > -1) {
				Object.assign(state.matter.sections[sectionIndex].items[itemIndex], payload)
			} else {
				console.warn('cannot find item @ mutation update item')
			}
		}
	},
	[MUTATION.SORT_MATTER] (state) {
		const sections = state.matter.sections

		// sorting sections
		sections.sort((a, b) => parseInt(_.get(a, 'config.order')) - parseInt(_.get(b, 'config.order')))

		sections.forEach(section => {
			if (! section.hasOwnProperty('items'))
				return section

			// order items by `order` property
			section.items.sort((a, b) => a.order - b.order)
		})

		Object.assign(state.matter, {
			sections
		})
	},
	[MUTATION.UPDATE_ATTEMPT] (state, { sectionId = null, itemId = null, attempt = null }) {
		if (sectionId === null || itemId === null || ! attempt.hasOwnProperty('id'))
			return false

		const sectionIndex = state.matter.sections.findIndex(s => s.id === sectionId),
			itemIndex = state.matter.sections[sectionIndex].items.findIndex(i => i.id === itemId),
			attemptIndex = state.matter.sections[sectionIndex].items[itemIndex]['attempts'].findIndex(a => a.id === attempt.id)

		if (sectionIndex < 0 || itemIndex < 0 || attemptIndex < 0)
			return false

		const sections = state.matter.sections
		sections[sectionIndex].items[itemIndex]['attempts'][attemptIndex] = attempt
		Object.assign(state, {
			matter: {
				sections
			}
		})
	},
	[MUTATION.RESET_ITEM_TOTAL] (state) {
		state.items_count.loaded = 0
		state.items_count.total = 0
	},
	[MUTATION.ADD_ITEM_TOTAL] (state, payload) {
		state.items_count.total += payload
	},
	[MUTATION.ADVANCE_LOADED_ITEM_TOTAL] (state) {
		state.items_count.loaded++
	}
}

// noinspection JSUnusedGlobalSymbols
const actions = {
	async fetchSections ({ commit, dispatch }) {
		commit(MUTATION.CHANGE_STATUS, STATUS.FETCHING)
		const res = await this._vm.laravel.request('api.client.section')
		commit(MUTATION.CHANGE_MATTER, res.data)
		await dispatch('postFetchSections')

		commit(MUTATION.CHANGE_STATUS, STATUS.IDLE)
		return res
	},
	async postFetchSections ({ state, dispatch, commit }) {
		// all done while all section is ended
		if (state.matter.sections.every(s => s['ended_at'] !== null))
			return

		// #### THE SECTION BELOW IS FOR START OR LOAD DETAIL EVERY SECTION #### //
		commit(MUTATION.RESET_ITEM_TOTAL)
		for (const section of state.matter.sections) {
			const response = await dispatch('loadSection', section)
			const items = (response.items ?? [])

			commit(MUTATION.ADD_ITEM_TOTAL, items.filter(item => item.hasOwnProperty('attachments') && item.attachments.length > 0).length)

			for (const item of items) {
				if (item.hasOwnProperty('attachments') && item.attachments.length > 0) {
					// create instance of howler

					const mimeToFormat = (mime) => {
						switch (mime) {
							case 'audio/mpeg':
							default:
								return 'mp3'
						}
					}

					const files = item.attachments.map(a => ({url : a.url, format: mimeToFormat(a.mime)}))
					try {
						await this._vm.howler.load(files, item.id)
						commit(MUTATION.ADVANCE_LOADED_ITEM_TOTAL)
					} catch (e) {
						throw new Error("failed load file! : " + JSON.stringify(files))
					}
				}
			}
		}

		commit(MUTATION.SORT_MATTER)

		// #### THE SECTION BELOW IS FOR DECIDING THE FIRST SECTION & ITEM TO BE SELECTED  #### //
		dispatch('calculateActive')
	},
	async loadSection ({ commit }, section) {
		if (! _.get(section, 'last_attempted_at') && ! _.get(section, 'ended_at')) {
			const { status, data: responseData } = await this._vm.laravel.request('api.client.section.start', {
				'participant_section': _.get(section, 'id')
			})

			if (status === 201 && _.get(responseData, 'status') === 'success')
				commit(MUTATION.UPDATE_SECTION, _.get(responseData, 'data'))

			return _.get(responseData, 'data', responseData)
		} else {
			const { status, data: responseData } = await this._vm.laravel.request('api.client.section.show', {
				'participant_section': _.get(section, 'id')
			})

			if (status === 200)
				commit(MUTATION.UPDATE_SECTION, responseData)

			return responseData
		}
	},
	calculateActive ({ state, commit }) {
		// TODO : implement multi section. [free to choose which section to be tackle first] | set the state.active.section

		// getting last progress of section
		const lastAttemptSection = state.matter.sections.find(section => ! _.get(section, 'ended_at'))

		if (! lastAttemptSection)
			return

		const active = {
			section: lastAttemptSection.id
		}

		if (_.get(lastAttemptSection, 'items.length', 0) !== 0
			|| lastAttemptSection.items.findIndex(i => i.id === state.active.item) === -1) {
			if (! _.get(lastAttemptSection, 'item_duration')) {
				active['item'] = lastAttemptSection.items[0].id
			} else {
				const item = lastAttemptSection.items.find(i => _.get(i, 'remaining_time', 0) > 0)
				if (item !== undefined) {
					active['item'] = _.get(item, 'id')
				} else {
					// indicate that it is item_duration and all items on lastAttemptSection is all zero
					console.warn(
						`all remaining time of` +
						`\nsection ${lastAttemptSection.id}:${_.get(lastAttemptSection, 'config.title')} is zero`)

					active['item'] = null
				}
			}
		}

		commit(MUTATION.CHANGE_ACTIVE, active)
	},
	async saveAnswer ({ getters, commit }, { itemId = null, itemAnswerId = null, content = null }) {
		if (! getters.activeItem)
			throw new Error("No active item")

		if (itemId === null && getters.activeAttempt === null)
			throw new Error("no attempt found")

		if (itemAnswerId !== null && itemId === null && ! getters.activeItem.answers.some(a => a.id === itemAnswerId))
			throw new Error("item id not found")

		// define variable that support custom itemId [change answer for non active item]
		let attempt = null
		if (itemId !== null) {
			const item = getters.activeSection.items.find(i => i.id === itemId)

			if (! item)
				throw new Error(`Item ${itemId} not found on this section`)

			attempt = _.get(item, 'attempts', [])
					.find(a => _.get(a, 'attempt_number', -1) === _.get(getters.activeSection, 'attempts'))
				?? null

			if (! attempt)
				throw new Error(`Item ${itemId} doesn't have attempt with number ${_.get(getters.activeSection, 'attempts')}`)

			if (itemAnswerId !== null && ! item.answers.some(a => a.id === itemAnswerId))
				throw new Error(`Item ${itemId} doesn't have answers with id ${itemAnswerId}`)
		}

		const { data: responseData } = await this._vm.laravel.request('api.client.section.item.attempt', {
			participant_section: getters.activeSection.id,
			section_item: itemId ?? getters.activeItem.id,
			item_attempt: attempt?.id ?? getters.activeAttempt.id,
			item_answer_id: itemAnswerId ?? undefined,
			content: content ?? undefined
		})

		if (responseData.status === 'success') {
			commit(MUTATION.UPDATE_ATTEMPT, {
				sectionId: getters.activeSection.id,
				itemId: itemId ?? getters.activeItem.id,
				attempt: responseData.data
			})
		}

		return responseData
	},
	async saveTime ({ getters, dispatch, commit }, { withMutation = false, gap = 5 }) {
		let res = {}
		try {
			if (getters.itemDuration) {
				res = await this._vm.laravel.request('api.client.section.item.tick', {
					participant_section: getters.activeSection.id,
					section_item: getters.activeItem.id,
					amount: gap
				})
			} else {
				res = await this._vm.laravel.request('api.client.section.tick', {
					participant_section: getters.activeSection.id,
					amount: gap
				})
			}
		} catch (e) {
			res = e.response
		}

		if (withMutation) {
			commit(MUTATION.UPDATE_SECTION, res.data.data)
		}
	},
	next ({ state, getters, dispatch, commit }, payload) {
		if (getters.activeSection === null || getters.activeItem === null) {
			console.warn('active.section or active.item is null')
			return
		}

		function nextSection() {
			const activeSectionIndex = state.matter.sections.findIndex(s => s.id === getters.activeSection.id)

			if (activeSectionIndex + 1 === state.matter.sections.length)
				return dispatch('endPerform')

			const newActiveSection = state.matter.sections[activeSectionIndex + 1]
			if (newActiveSection.items.length === 0) {
				console.warn('newActiveSection length is zero')
				return
			}
			commit(MUTATION.UPDATE_SECTION, payload)
			commit(MUTATION.CHANGE_ACTIVE, { section: newActiveSection.id, item: newActiveSection.items[0].id })
		}

		function nextItem() {
			const activeItemIndex = getters.activeSection.items.findIndex(i => i.id === getters.activeItem.id)
			if (activeItemIndex + 1 === getters.activeSection.items.length)
				return nextSection()

			commit(MUTATION.UPDATE_ITEM, payload)
			commit(MUTATION.CHANGE_ACTIVE, { item: getters.activeSection.items[activeItemIndex + 1].id })
		}

		if (! getters.itemDuration) {
			nextSection()
		} else {
			nextItem()
		}
	},
	endPerform ({ dispatch }) {
		// we refresh the matter data then
		// let the Perform.vue do the rest
		dispatch('fetchSections')
	}
}

export default {
	namespaced: true,
	state: _.cloneDeep(defaultState),
	getters,
	mutations,
	actions,
}
