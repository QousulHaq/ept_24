import React from 'react'

import { Outlet } from 'react-router-dom'

import Sidebar from '../components/Sidebar'
import Upbar from '../components/Upbar'

function RootLayout() {

    return (
        <div className="tw-flex tw-overflow-hidden">
            <Sidebar />
            <div className="tw-w-screen tw-h-screen">
                <Upbar />
                <Outlet />
            </div>
        </div>
    )
}

export default RootLayout