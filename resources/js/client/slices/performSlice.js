import { createSlice, createAsyncThunk, createSelector } from '@reduxjs/toolkit'
import { STATUS, MUTATION as BASE_MUTATION } from './types'
import laravelClient from '../utils/laravelClient'
import howler from '../utils/howlerClient'
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

const performSlice = createSlice({
    name: 'perform',
    initialState: { ...defaultState },
    reducers: {
        reset_state: (state) => {
            Object.assign(state, _.cloneDeep(defaultState))
        },
        change_status: (state, action) => {
            state.status = action.payload
        },
        change_matter: (state, action) => {
            Object.assign(state.matter, action.payload)
        },
        change_active: (state, { payload = {} }) => {
            Object.assign(state, {
                active: {
                    ...state.active,
                    ...payload
                }
            })
        },
        update_section: (state, { payload = {} }) => {
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
        update_item: (state, { payload = {} }) => {
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
        update_attempt: (state, action) => {
            const { sectionId = null, itemId = null, attempt = null } = action.payload;

            if (sectionId === null || itemId === null || !attempt.hasOwnProperty('id'))
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
        sort_matter: (state) => {
            const sections = state.matter.sections

            // sorting sections
            sections.sort((a, b) => parseInt(_.get(a, 'config.order')) - parseInt(_.get(b, 'config.order')))

            sections.forEach(section => {
                if (!section.hasOwnProperty('items'))
                    return section

                // order items by `order` property
                section.items.sort((a, b) => a.order - b.order)
            })

            Object.assign(state.matter, {
                sections
            })
        },
        reset_item_total: (state) => {
            state.items_count.loaded = 0
            state.items_count.total = 0
        },
        add_item_total: (state, action) => {
            state.items_count.total += action.payload
        },
        advance_loaded_item_total: (state) => {
            state.items_count.loaded++
        }
    }
})

// Getter

const selectPerform = (state) => state.perform;

export const getSection = createSelector(
    [selectPerform],
    (perform) => perform.matter.sections
);

export const getActiveSection = createSelector(
    [selectPerform, getSection],
    (perform, sections) => {
        if (sections.length === 0) return null;
        return sections.find((s) => s.id === perform.active.section) ?? null;
    }
);

export const getActiveItem = createSelector(
    [selectPerform, getActiveSection],
    (perform, activeSection) => {
        if (!activeSection || !perform.active.item) return null;
        return activeSection.items.find((item) => item.id === perform.active.item) ?? null;
    }
);

export const getActiveAttempt = createSelector(
    [getActiveItem, getActiveSection],
    (activeItem, activeSection) => {
        if (!activeItem || !activeSection) return null;
        return (
            activeItem.attempts.find(
                (a) => _.get(a, 'attempt_number', -1) === _.get(activeSection, 'attempts')
            ) ?? null
        );
    }
);

export const getItemDuration = createSelector(
    [getActiveSection, getSection],
    (activeSection, sections) => {
        if (!activeSection) return null;
        return _.get(activeSection, 'item_duration', _.get(sections, 'config.item_duration', false));
    }
);

export const getIsDone = createSelector(
    [getSection],
    (sections) => sections.every((s) => _.get(s, 'ended_at'))
);

export const getSectionItemsAnswered = createSelector(
    [getSection],
    (sections) =>
        sections
            .map((section) =>
                section.items ? section.items
                    .filter((item) => item.attempts.some((attempt) => attempt.answer !== null))
                    .map((item) => item.id) : []
            )
            .flat()
);

export const getItemLoadedPercentage = createSelector(
    [selectPerform],
    (perform) => {
        // Pastikan total bukan 0 dan keduanya adalah angka yang valid
        if (!perform.items_count.total || isNaN(perform.items_count.loaded) || isNaN(perform.items_count.total)) {
            return 0; // Atau nilai default lain yang masuk akal
        }
        return parseInt((perform.items_count.loaded / perform.items_count.total) * 100);
    }
);

// Thunk

export const fetchSections = createAsyncThunk(
    'perform/fetchSections',
    async (_, { dispatch }) => {
        try {
            dispatch(change_status(STATUS.FETCHING))
            const res = await laravelClient.request('api.client.section')
            dispatch(change_matter(res.data))
            await dispatch(postFetchSections())
            dispatch(change_status(STATUS.IDLE))
            return res
        } catch (error) {
            dispatch(change_status(STATUS.ERROR))
            console.error('Error fetching sections:', error)
            throw error
        }
    }
)

export const postFetchSections = createAsyncThunk(
    'perform/postFetchSections',
    async (_, { dispatch, getState }) => {
        const state = getState().perform

        // all done while all section is ended
        if (state.matter.sections.every(s => s['ended_at'] !== null))
            return

        // #### THE SECTION BELOW IS FOR START OR LOAD DETAIL EVERY SECTION #### //
        dispatch(reset_item_total())
        for (const section of state.matter.sections) {
            const response = await dispatch(loadSection(section))
            const items = (response.payload.items ?? [])

            dispatch(add_item_total(items.filter(item => item.hasOwnProperty('attachments') && item.attachments.length > 0).length))

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

                    const files = item.attachments.map(a => ({ url: a.url, format: mimeToFormat(a.mime) }))
                    try {
                        await howler.load(files, item.id)
                        dispatch(advance_loaded_item_total())
                    } catch (e) {
                        throw new Error("failed load file! : " + JSON.stringify(files))
                    }
                }
            }
        }

        dispatch(sort_matter())

        // #### THE SECTION BELOW IS FOR DECIDING THE FIRST SECTION & ITEM TO BE SELECTED  #### //
        dispatch(calculateActive())
    }
)

export const loadSection = createAsyncThunk(
    'perform/loadSection',
    async (section, { dispatch }) => {
        if (!_.get(section, 'last_attempted_at') && !_.get(section, 'ended_at')) {
            const { status, data: responseData } = await laravelClient.request('api.client.section.start', {
                'participant_section': _.get(section, 'id')
            })

            if (status === 201 && _.get(responseData, 'status') === 'success')
                dispatch(update_section(_.get(responseData, 'data')))

            return _.get(responseData, 'data', responseData)

        } else {
            const { status, data: responseData } = await laravelClient.request('api.client.section.show', {
                'participant_section': _.get(section, 'id')
            })

            if (status === 200)
                dispatch(update_section(responseData))

            return responseData
        }
    }
)

export const calculateActive = () => (dispatch, getState) => {
    const state = getState().perform
    // TODO : implement multi section. [free to choose which section to be tackle first] | set the state.active.section

    // getting last progress of section
    const lastAttemptSection = state.matter.sections.find(section => !_.get(section, 'ended_at'))

    if (!lastAttemptSection)
        return

    const active = {
        section: lastAttemptSection.id
    }

    if (_.get(lastAttemptSection, 'items.length', 0) !== 0
        || lastAttemptSection.items.findIndex(i => i.id === state.active.item) === -1) {
        if (!_.get(lastAttemptSection, 'item_duration')) {
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

    dispatch(change_active(active))
}

export const saveAnswer = createAsyncThunk(
    'perform/saveAnswer',
    async ({ itemId = null, itemAnswerId = null, content = null }, { dispatch, getState }) => {
        const state = getState()
        const activeItem = getActiveItem(state)
        const activeAttempt = getActiveAttempt(state)
        const activeSection = getActiveSection(state)

        if (!activeItem)
            throw new Error("No active item")

        if (itemId === null && activeAttempt === null)
            throw new Error("no attempt found")

        if (itemAnswerId !== null && itemId === null && !activeItem.answers.some(a => a.id === itemAnswerId))
            throw new Error("item id not found")

        // define variable that support custom itemId [change answer for non active item]
        let attempt = null
        if (itemId !== null) {
            const item = activeSection.items.find(i => i.id === itemId)

            if (!item)
                throw new Error(`Item ${itemId} not found on this section`)

            attempt = _.get(item, 'attempts', [])
                .find(a => _.get(a, 'attempt_number', -1) === _.get(activeSection, 'attempts'))
                ?? null

            if (!attempt)
                throw new Error(`Item ${itemId} doesn't have attempt with number ${_.get(activeSection, 'attempts')}`)

            if (itemAnswerId !== null && !item.answers.some(a => a.id === itemAnswerId))
                throw new Error(`Item ${itemId} doesn't have answers with id ${itemAnswerId}`)
        }

        const { data: responseData } = await laravelClient.request('api.client.section.item.attempt', {
            participant_section: activeSection.id,
            section_item: itemId ?? activeItem.id,
            item_attempt: attempt?.id ?? activeAttempt.id,
            item_answer_id: itemAnswerId ?? undefined,
            content: content ?? undefined
        })

        if (responseData.status === 'success') {
            dispatch(update_attempt({
                sectionId: activeSection.id,
                itemId: itemId ?? activeItem.id,
                attempt: responseData.data
            }))
        }

        return responseData
    }
)

export const saveTime = createAsyncThunk(
    'perform/saveTime',
    async ({ withMutation = false, gap = 5 }, { dispatch, getState }) => {
        let res = {}
        const state = getState()
        const activeItem = getActiveItem(state)
        const activeSection = getActiveSection(state)
        const itemDuration = getItemDuration(state)

        try {
            if (itemDuration) {
                res = await laravelClient.request('api.client.section.item.tick', {
                    participant_section: activeSection.id,
                    section_item: activeItem.id,
                    amount: gap
                })
            } else {
                res = await laravelClient.request('api.client.section.tick', {
                    participant_section: activeSection.id,
                    amount: gap
                })
            }
        } catch (e) {
            res = e.response
        }

        if (withMutation) {
            dispatch(update_section(res.data.data))
        }
    }
)

export const endPerform = () => (dispatch) => {
    dispatch(fetchSections())
}

export const next = (payload) => (dispatch, getState) => {
    const state = getState()
    const activeItem = getActiveItem(state)
    const activeSection = getActiveSection(state)
    const itemDuration = getItemDuration(state)

    if (activeSection === null || activeItem === null) {
        console.warn('active.section or active.item is null')
        return
    }

    function nextSection() {
        const activeSectionIndex = state.perform.matter.sections.findIndex(s => s.id === activeSection.id)

        if (activeSectionIndex + 1 === state.perform.matter.sections.length)
            return dispatch(endPerform())

        const newActiveSection = state.perform.matter.sections[activeSectionIndex + 1]
        if (newActiveSection.items.length === 0) {
            console.warn('newActiveSection length is zero')
            return
        }
        dispatch(update_section(payload))
        dispatch(change_active({ section: newActiveSection.id, item: newActiveSection.items[0].id }))
    }

    function nextItem() {
        const activeItemIndex = activeSection.items.findIndex(i => i.id === activeItem.id)
        if (activeItemIndex + 1 === activeSection.items.length)
            return nextSection()

        dispatch(update_item(payload))
        dispatch(change_active({ item: activeSection.items[activeItemIndex + 1].id }))
    }

    if (!itemDuration) {
        nextSection()
    } else {
        nextItem()
    }
}

export const {
    reset_state,
    change_status,
    change_matter,
    change_active,
    update_section,
    update_item,
    update_attempt,
    sort_matter,
    reset_item_total,
    add_item_total,
    advance_loaded_item_total,
} = performSlice.actions
export default performSlice.reducer