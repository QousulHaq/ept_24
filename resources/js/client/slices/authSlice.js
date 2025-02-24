import { createSlice, createAsyncThunk, createSelector } from "@reduxjs/toolkit";
import queryString from "query-string";
import _ from "lodash";
import { isAfter, addSeconds, format, parseISO } from "date-fns";

import { STATUS, MUTATION as BASE_MUTATION } from "./types";
import { reset_state as exam_reset_state } from "./examSlice";
import { reset_state as notification_reset_state } from "./notificationSlice";
import { reset_state as perform_reset_state } from "./performSlice";

import laravelClient from "../utils/laravelClient";
import axiosInstance from "../utils/axiosClient";

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

const authSlice = createSlice({
    name: "auth",
    initialState: { ...defaultState },
    reducers: {
        reset_state: (state) => {
            Object.assign(state, _.cloneDeep(defaultState))
        },
        change_status: (state, action) => {
            state.status = action.payload
        },
        change_connection_state: (state, action) => {
            state.connection_state = action.payload
        },
        change_credential_state: (state, action) => {
            state.credential.state = action.payload
        },
        save_credential: (state, action) => {
            Object.assign(state.credential, action.payload);
            state.lastFetched = new Date().toISOString();

            // ada di echoMiddleware.js
            // Vue.prototype.$echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${state.credential.access_token}`
            // Vue.prototype.$echo.connector.pusher.config.auth.headers['Accept'] = 'application/json'
        },
        save_active_users: (state, action) => {
            state.active_users = action.payload
        },
        add_active_user: (state, action) => {
            state.active_users.push(action.payload)
        },
        remove_active_user: (state, action) => {
            state.active_users = state.active_users.filter(user => user.id !== action.payload.id)
        },
        save_user: (state, action) => {
            state.user = action.payload
        },
    },
});

// Getter

const selectAuth = (state) => state.auth;

// Apakah user terautentikasi?
export const getAuthenticated = createSelector(
    [selectAuth],
    (auth) => !!auth.credential?.access_token
);

// Apakah token sudah kedaluwarsa?
export const getTokenExpired = createSelector(
    [selectAuth],
    (auth) => {
        if (!auth.lastFetched) return true;
        const expiresIn = _.get(auth, 'credential.expires_in', 0);
        return isAfter(
            parseISO(format(new Date(), 'yyyy-MM-dd')), 
            addSeconds(parseISO(auth.lastFetched), expiresIn)
        );
    }
);

//Thunk

export const resetState = () => (dispatch) => {
    dispatch(reset_state())
    dispatch(exam_reset_state())
    dispatch(notification_reset_state())
    dispatch(perform_reset_state())
}

export const getCode = () => (dispatch, getState) => {
    const state = getState().auth;

    if (state.credential?.state === null) {
        dispatch(change_credential_state(Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)));
    }

    const queryParams = {
        client_id: process.env.MIX_VUE_APP_CLIENT_ID ?? 666,
        redirect_uri: process.env.MIX_VUE_APP_REDIRECT_URL ?? `${window.location.origin}/client`,
        response_type: 'token',
        state: state.credential?.state,
    };

    window.location.replace(`${window.location.origin}/oauth/authorize?${queryString.stringify(queryParams)}`);
};

export const logout = () => async (dispatch) => {
    try {
        window.onbeforeunload = null;
        await axiosInstance.post('/auth/logout');
        dispatch(resetState());
        window.location.href = '/';
    } catch (error) {
        console.error('Logout failed:', error);
    }
};

export const login = createAsyncThunk(
    'auth/login',
    async (credential, { getState, dispatch }) => {
        const state = getState().auth

        if (state.credential.state === credential?.state) {
            dispatch(save_credential(credential))
            dispatch({ type: "echo/auth/listenAttendance" })
            const user = await dispatch(getUser())
            console.log("ini isi dari user", user)
            dispatch({ type: "echo/notification/listen" })

            return user
        }

        throw new Error('state doesn\'t seem right')
    }
)

export const getUser = createAsyncThunk(
    "auth/getUser",
    async (_, { dispatch }) => {
        const { data } = await laravelClient.request('api.client.user')
        dispatch(save_user(data))

        return data
    }
)

export const {
    reset_state,
    change_status,
    change_connection_state,
    change_credential_state,
    save_credential,
    save_active_users,
    add_active_user,
    remove_active_user,
    save_user
} = authSlice.actions;

export default authSlice.reducer;