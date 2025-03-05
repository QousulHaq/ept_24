import React from 'react'
import { useSelector } from 'react-redux';

import { Avatar } from '@mui/material';


import logo from "../public/img/logo-white.svg";

const UpbarPerform = () => {
    const username = useSelector(state => state.auth?.user?.username)

    return (
        <div className='tw-bg-primary3 tw-w-full tw-flex tw-justify-between tw-p-5 tw-px-10'>
            <img src={logo} alt="" />
            <div className="account-wrap tw-flex tw-justify-end tw-items-center">
                <div className="avatar tw-me-2">
                    <Avatar sx={{ width: 35, height: 35, bgcolor : "white", color : "#2B7FD4" }}>F</Avatar>
                </div>
                <div className="account-name">
                    <p className='tw-text-sm tw-font-semibold tw-text-white'>{username}</p>
                    <p className='tw-text-xs tw-font-light tw-text-white'>Student</p>
                </div>
            </div>
        </div>
    )
}

export default UpbarPerform