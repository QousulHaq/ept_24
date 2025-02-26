import React from 'react'

import { useState } from 'react';
import { NavLink } from 'react-router-dom';

import { useDispatch } from 'react-redux';
import { logout } from '../slices/authSlice';
import Swal from 'sweetalert2';

import PowerSettingsNewIcon from '@mui/icons-material/PowerSettingsNew';
import KeyboardDoubleArrowLeftIcon from '@mui/icons-material/KeyboardDoubleArrowLeft';
import ListIcon from '@mui/icons-material/List';
import { Grid2, Divider } from '@mui/material';

import { menu } from '../data/menu';
import logo from "../public/img/logo.svg";

function Sidebar() {
    const [expanded, setExpanded] = useState(true)

    const dispatch = useDispatch()

    const handleLogout = () => {
        Swal.fire({
            title: "Are you sure want to logout",
            showCancelButton: true,
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                dispatch(logout())
            }
        });
    }

    return (
        <aside className={`tw-border-r ${expanded ? 'tw-w-64' : 'tw-w-20'} tw-border-neutral4 tw-transition-all`}>
            <header className="main-logo">
                <div className="header-wrap tw-p-3 tw-flex tw-justify-evenly tw-items-center">
                    {expanded && <img src={logo} className='tw-w-28 tw-pt-2' />}
                    <div className="appbar-collapse-icon">
                        {
                            expanded ?
                                <button className="icon-wrapper tw-bg-neutral5 tw-p-1 tw-rounded-md tw-opacity-30 hover:tw-opacity-100" onClick={() => setExpanded(false)}>
                                    <KeyboardDoubleArrowLeftIcon fontSize='small' />
                                </button>
                                :
                                <button className="icon-wrapper tw-p-1 tw-rounded-md tw-hover:tw-bg-neutral5" onClick={() => setExpanded(true)}>
                                    <ListIcon />
                                </button>
                        }
                    </div>
                </div>
            </header>
            <div className="nav-item">
                <nav className="nav-item-wrap tw-pe-4 tw-mb-4">
                    {menu.map((item, index) => (
                        <NavLink to={item.path} end key={index}>
                            {({ isActive, isPending }) => (
                                <Grid2 container columnSpacing={1}>
                                    <Grid2 size={expanded ? 1 : 3} className={`${isActive ? "tw-bg-primary3" : isPending ? "" : "tw-bg-white"} tw-rounded-r-md`}></Grid2>
                                    <Grid2 size={expanded ? 11 : 9}>
                                        <div className={`nav-item ${isActive ? "tw-bg-primary3 tw-text-white" : isPending ? "" : "tw-bg-white"} tw-rounded-md tw-px-2 tw-py-3 tw-flex tw-items-center`}>
                                            <div className={`${expanded ? 'tw-mx-2' : 'tw-mx-auto'}`}>
                                                {item.icon}
                                            </div>
                                            {expanded && <p className='tw-text-xs tw-font-normal tw-ms-1'>{item.name}</p>}
                                        </div>
                                    </Grid2>
                                </Grid2>
                            )}
                        </NavLink>
                    ))}
                </nav>
            </div>
            <Divider sx={{ borderWidth: 1 }} variant="middle"></Divider>
            <div className="logout-button">
                <div className={`logout-button-wrap tw-px-4 tw-pt-2 ${expanded ? '' : 'tw-flex tw-justify-center'}`}>
                    <button className="tw-flex tw-items-center tw-px-2 tw-py-2" onClick={() => handleLogout()}>
                        <PowerSettingsNewIcon fontSize="small" className='tw-mx-2' />
                        {expanded && <p className='tw-text-xs tw-font-normal tw-ms-2'>Log Out</p>}
                    </button>
                </div>
            </div>
        </aside>
    )
}

export default Sidebar