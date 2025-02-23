import { createBrowserRouter, Navigate } from "react-router-dom"

import App from "../App"
import Home from "../views/Home"
import HasilUjian from "../views/HasilUjian"
import Pengaturan from "../views/Pengaturan"
import Presentase from "../views/Presentase"
import Profile from "../views/Profile"
import RiwayatUjian from "../views/RiwayatUjian"

const router = createBrowserRouter([
    {
        path: "/client",
        element: <App />,
        children: [
            {
                index: true,
                element: <Home />
            },
            {
                path: "hasil-ujian",
                element: <HasilUjian />
            },
            {
                path: "presentase",
                element: <Presentase />
            },
            {
                path: "riwayat-ujian",
                element: <RiwayatUjian />
            },
            {
                path: "profile",
                element: <Profile />
            },
            {
                path: "pengaturan",
                element: <Pengaturan />
            },
        ]
    },
])

export default router