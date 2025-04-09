import React, { useEffect } from 'react'

import { RouterProvider } from 'react-router-dom'
import router from './routers'

import { useSelector, useDispatch } from 'react-redux'
import { showSnackbar } from './slices/snackbarSlice'

import { getAuthenticated } from './slices/authSlice'
import { getHasEnrolledExam, listenToExamChannel } from './slices/examSlice'
import { getEchoInstance } from './utils/echoServices'

import * as Sentry from "@sentry/react"

function App() {
    const dispatch = useDispatch()

    const isAuthenticated = useSelector(getAuthenticated)
    const token = useSelector((state) => state.auth?.credential?.access_token)
    const username = useSelector((state) => state.auth?.user?.username)
    const hasEnrolledExam = useSelector(getHasEnrolledExam)

    useEffect(() => {
        if (isAuthenticated) {
            const echo = getEchoInstance(token)

            dispatch({ type: "echo/auth/listenAttendance" })
            if (username) {
                dispatch({ type: "echo/notification/listen" })
            }
            if (hasEnrolledExam) {
                dispatch(listenToExamChannel()).then(() => dispatch({ type: "echo/exam/windowLeavingCheckerInit" }))
            }
        }

        // Handle onbeforeunload di mode produksi
        if (process.env.NODE_ENV === 'production') {
            window.onbeforeunload = () => {
                dispatch(showSnackbar({ message: 'Please avoid reloading the page!', severity: "warning" }))
                return 'Avoid reloading page during the exam.';
            };
        }
    }, [])

    return (
        <div className="app-client" style={{ width: "100%", height: "100%", background: "white" }}>
            <RouterProvider router={router} />
        </div>
    )
}

export default App

// Inisialisasi Sentry jika env tersedia
// if (process.env.REACT_APP_SENTRY_DSN) {
//     Sentry.init({
//         dsn: process.env.REACT_APP_SENTRY_DSN,
//         integrations: [new Sentry.BrowserTracing()],
//         tracesSampleRate: 1.0,
//     });
// }

// Debug di mode development
if (process.env.NODE_ENV !== 'production') {
    window.React = React;
} else {
    console.log('Running in production mode');
}