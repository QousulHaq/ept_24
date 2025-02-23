import { createSlice, createAsyncThunk, createSelector } from "@reduxjs/toolkit";
import { STATUS, MUTATION as BASE_MUTATION } from "./types";
import _, { create } from "lodash";
import laravelClient from "../utils/laravelClient";

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

const examSlice = createSlice({
    name: "exam",
    initialState: { ...defaultState },
    reducers: {
        reset_state: (state) => {
            Object.assign(state, _.cloneDeep(defaultState))
        },
        change_status: (state, action) => {
            state.status = action.payload
        },
        change_params: (state, action) => {
            Object.assign(state.params, action.payload)
        },
        change_matter: (state, action) => {
            Object.assign(state.matter, action.payload)
        },
        enrolled_exam: (state, action) => {
            state.chosenExam = action.payload.examId
            Object.assign(state.token, action.payload.token)
        },
        set_room: (state, action) => {
            state.room = action.payload
        }
    }
})

// Getter

const selectExam = (state) => state.exam;

export const getExamById = (id) => createSelector(
  selectExam,
  (exam) => exam.matter.data.find((d) => d.id === id)
);

export const getHasEnrolledExam = createSelector(
  selectExam,
  (exam) => !!exam.chosenExam
);

export const getActiveExam = createSelector(
  selectExam,
  (exam) => exam.matter.data.find((d) => d.id === exam.chosenExam)
);

export const getIsStarted = createSelector(
  getActiveExam,
  (activeExam) => !!_.get(activeExam, 'started_at') || _.get(activeExam, 'is_anytime', false)
);

export const getIsBanned = createSelector(
  getActiveExam,
  (activeExam) => _.get(activeExam, 'detail.status') === 'banned'
);

// Thunk

export const changeParams = createAsyncThunk(
    "exam/changeParams",
    async (params = {}, { dispatch }) => {
        dispatch(change_params(params))

        return await dispatch(fetchExam())
    }
)

export const fetchExam = createAsyncThunk(
    "exam/fetchExam",
    async (_, { dispatch, getState }) => {
        const state = getState().exam

        dispatch(change_status(STATUS.FETCHING))
        const res = await laravelClient.request('api.client.exam', state.params)
        dispatch(change_status(STATUS.IDLE))
        if (res.status === 200) {
            dispatch(change_matter(res.data))
            return res
        }
    }
)

export const enroll = createAsyncThunk(
    "auth/enroll",
    async (examId, { dispatch, getState }) => {
        const state = getState().exam

        dispatch(change_status(STATUS.FETCHING))
        const res = await laravelClient.request('api.client.exam.enroll', { exam: examId })
        dispatch(change_status(STATUS.IDLE))
        if (res.status === 200 && res.data.status === 'success') {
            dispatch(enrolled_exam({ examId, token: res.data.data }))
            dispatch(listenToExamChannel()).then(() => dispatch({ type: "echo/exam/windowLeavingCheckerInit" }))
            return res
        }
    }
)

export const checkQualifiedStatus = createAsyncThunk(
    "exam/checkQualifiedStatus",
    async ({ participant: detail }, { dispatch, getState }) => {
        const state = getState().exam
        const activeExam = state.matter.data.find(d => d.id === state.chosenExam).detail

        if (activeExam && activeExam.id === detail.id && activeExam.status !== detail.status) {
            await dispatch(fetchExam())
        }
    }
)

export const listenToExamChannel = createAsyncThunk(
    "exam/listenToExamChannel",
    async (_, { dispatch }) => {
        dispatch({ type: "echo/exam/listenToExamChannel" })
    }
)

export const {
    reset_state,
    change_status,
    change_params,
    change_matter,
    enrolled_exam,
    set_room
} = examSlice.actions
export default examSlice.reducer