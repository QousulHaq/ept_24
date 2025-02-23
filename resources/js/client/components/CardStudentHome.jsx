import React, { useState } from 'react'

import { card1, tipeUjian } from '../data/user-home-data'

import UserMahasiswa from './icons/UserMahasiswa'

import { Grid2, SvgIcon } from '@mui/material'

import CalendarTodayOutlinedIcon from '@mui/icons-material/CalendarTodayOutlined';
import WatchLaterOutlinedIcon from '@mui/icons-material/WatchLaterOutlined';

function CardStudentHome(props) {

    const [namaTipeUjian, setNamaTipeUjian] = useState('Professional')

    const namaTipeHandler = (nama) => {
        setNamaTipeUjian(nama);
        props.onSetNamaTipe(nama)
    }

    return (
        <Grid2 container spacing={2}>
            {
                card1.map((value, index) => (
                    <Grid2 size={4} key={index}>
                        <button className='user-card-trigger' onClick={() => namaTipeHandler(value.nama)}>
                            <div className={`user-card tw-px-3 tw-py-5 tw-rounded-2xl ${namaTipeUjian === value.nama ? `tw-bg-gradient-to-b tw-from-${value.primaryColor} tw-to-${value.secondaryColor} tw-shadow-md` : `tw-border tw-border-${value.primaryColor}`}`}>
                                <h1 className={`tw-text-xl tw-font-bold ${namaTipeUjian === value.nama ? `tw-text-white` : `tw-text-${value.primaryColor}`} tw-mb-2`}>{value.nama}</h1>
                                <div className="user-card-content tw-flex tw-justify-between tw-items-center tw-gap-2">
                                    {value.icon(namaTipeUjian === value.nama ? {color : 'white'} : null)}
                                    <p className={`tw-text-xs tw-text-left ${namaTipeUjian === value.nama ? `tw-text-white` : `tw-text-${value.primaryColor}`}`}>{value.description}</p>
                                </div>
                            </div>
                        </button>
                    </Grid2>
                ))
            }
            <Grid2 size={12}>
                <h2 className={`tw-text-lg tw-font-bold tw-text-${tipeUjian[namaTipeUjian][0].primaryColor}`}>Tipe Ujian</h2>
            </Grid2>
            {tipeUjian[namaTipeUjian].map((value, index) => (
                <Grid2 size={4} key={index}>
                    <div className={`test-card tw-overflow-hidden tw-border tw-border-${value.primaryColor} tw-bg-${value.secondaryColor} tw-rounded-xl`}>
                        <div className={`test-card-title tw-p-2 tw-border-b tw-border-${value.primaryColor}`}>
                            <h1 className={`tw-text-xl tw-font-bold tw-text-${value.primaryColor} tw-text-center`}>{value.title}</h1>
                        </div>
                        <div className="test-card-content tw-p-3 tw-flex tw-flex-col tw-items-center">
                            <p className={`tw-font-semibold tw-text-${value.primaryColor} tw-text-center`}>{value.noUjian}</p>
                            <div className="test-date-time tw-my-4">
                                <div className="jadwal tw-flex tw-items-center tw-gap-2 tw-mb-1">
                                    <CalendarTodayOutlinedIcon fontSize='small' className={`tw-text-${value.primaryColor}`} />
                                    <p className={`tw-text-sm tw-font-medium tw-text-${value.primaryColor}`}>{value.jadwal}</p>
                                </div>
                                <div className="waktu tw-flex tw-items-center tw-gap-2">
                                    <WatchLaterOutlinedIcon fontSize='small' className={`tw-text-${value.primaryColor}`} />
                                    <p className={`tw-text-sm tw-font-medium tw-text-${value.primaryColor}`}>{value.waktu}</p>
                                </div>
                            </div>
                            <button className={`tw-bg-${value.primaryColor} tw-py-1 tw-px-4 tw-rounded-full tw-text-white tw-text-sm`}>Pilih Ujian</button>
                        </div>
                    </div>
                </Grid2>
            ))}
        </Grid2>
    )
}

export default CardStudentHome