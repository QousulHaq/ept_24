import { createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import { MUTATION as BASE_MUTATION } from './types'

export const MUTATION = {
    ...BASE_MUTATION,
    ADD_NOTIFICATION: 'add_notification'
}

const defaultState = {
    matter: []
}

const notificationSlice = createSlice({
    name: "notification",
    initialState: { ...defaultState },
    reducers: {
        reset_state: (state) => {
            Object.assign(state, _.cloneDeep(defaultState))
        },
        add_notification: (state, action) => {
            if (action.payload instanceof Notification) {
                state.matter.push(action.payload)
            }
        }
    }
})


// masih percobaan
export const addNotification = createAsyncThunk(
    "notification/addNotification",
    async (notification, { dispatch }) => {
        console.log("ini dari thunk notification", notification)
        if (notification.save)
            dispatch(add_notification(notification))
    }
)

export const {
    reset_state,
    add_notification
} = notificationSlice.actions
export default notificationSlice.reducer