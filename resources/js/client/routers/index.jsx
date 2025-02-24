import { createBrowserRouter, Navigate } from "react-router-dom"

import RootLayout from "../layouts/RootLayout"
import ProtectedRoute from "./ProtectedRoute"

import AuthCallback from "../views/AuthCallback"
import Home from "../views/Home"
import HasilUjian from "../views/HasilUjian"
import Pengaturan from "../views/Pengaturan"
import Presentase from "../views/Presentase"
import Profile from "../views/Profile"
import RiwayatUjian from "../views/RiwayatUjian"


const router = createBrowserRouter([
    {
        path: "/client",
        element: <RootLayout />,
        children: [
            {
                index: true,
                element: (
                    <ProtectedRoute>
                        <Home />
                    </ProtectedRoute>
                )
            },
            {
                path: "callback",
                element: (
                    <ProtectedRoute>
                        <AuthCallback />
                    </ProtectedRoute>
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
])

export default router