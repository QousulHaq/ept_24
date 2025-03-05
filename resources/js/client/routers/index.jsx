import { createBrowserRouter, Navigate } from "react-router-dom"

import RootLayout from "../layouts/RootLayout"
import PerformLayout from "../layouts/PerformLayout"
import ProtectedRoute from "./ProtectedRoute"

import AuthCallback from "../views/AuthCallback"
import Home from "../views/Home"
import HasilUjian from "../views/HasilUjian"
import Pengaturan from "../views/Pengaturan"
import Presentase from "../views/Presentase"
import Profile from "../views/Profile"
import RiwayatUjian from "../views/RiwayatUjian"
import ExamDetail from "../views/ExamDetail"

import Perform from "../views/perform/perform"
import PerformGoodbye from "../views/perform/PerformGoodbye"
import PerformTackle from "../views/perform/PerformTackle"
import PerformWaiting from "../views/perform/PerformWaiting"

const router = createBrowserRouter([
    {
        path: "/client",
        element: <RootLayout />,
        children: [
            {
                path: "callback",
                element: (
                    <ProtectedRoute>
                        <AuthCallback />
                    </ProtectedRoute>
                )
            },
            {
                index: true,
                element: (
                    <ProtectedRoute>
                        <Home />
                    </ProtectedRoute>
                )
            },
            {
                path: "exam/:id",
                element: (
                    <ExamDetail />
                )
            },
            {
                path: "hasil-ujian",
                element: (
                    <ProtectedRoute>
                        <HasilUjian />
                    </ProtectedRoute>
                )
            },
            {
                path: "presentase",
                element: (
                    <ProtectedRoute>
                        <Presentase />
                    </ProtectedRoute>
                )
            },
            {
                path: "riwayat-ujian",
                element: (
                    <ProtectedRoute>
                        <RiwayatUjian />
                    </ProtectedRoute>
                )
            },
            {
                path: "profile",
                element: (
                    <ProtectedRoute>
                        <Profile />
                    </ProtectedRoute>
                )
            },
            {
                path: "pengaturan",
                element: (
                    <ProtectedRoute>
                        <Pengaturan />
                    </ProtectedRoute>
                )
            },
        ]
    },
    {
        path: "/client/perform",
        element: <PerformLayout />,
        children: [
            {
                path : "tackle",
                element: (
                    <ProtectedRoute>
                        <PerformTackle />
                    </ProtectedRoute>
                )
            },
            {
                path : "waiting",
                element: (
                    <ProtectedRoute>
                        <PerformWaiting />
                    </ProtectedRoute>
                )
            },
            {
                path : "bye",
                element: (
                    <ProtectedRoute>
                        <PerformGoodbye />
                    </ProtectedRoute>
                )
            },
        ]
    },
])

export default router