import React, { useState } from 'react'
import { Link } from 'react-router-dom'

import { card1, tipeUjian } from '../data/user-home-data'

import { Grid2, SvgIcon, Skeleton } from '@mui/material'
import CalendarTodayOutlinedIcon from '@mui/icons-material/CalendarTodayOutlined';
import WatchLaterOutlinedIcon from '@mui/icons-material/WatchLaterOutlined';

import { format, differenceInSeconds } from 'date-fns'
import { id } from 'date-fns/locale'

import UserProfessional from './icons/UserProfessional'
import UserPelajar from './icons/UserPelajar'
import UserMahasiswa from './icons/UserMahasiswa'

export const toggleCard = [
    {
        nama: "CPNS",
        description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        icon: (props) => <UserProfessional {...props} />,
        primaryColor: "primary1",
        secondaryColor: "secondary6",
    },
    {
        nama: "SMAN",
        description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        icon: (props) => <UserMahasiswa {...props} />,
        primaryColor: "primary2",
        secondaryColor: "secondary5",
    },
    {
        nama: "E-TEFL",
        description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        icon: (props) => <UserPelajar {...props} />,
        primaryColor: "primary3",
        secondaryColor: "secondary7",
    }
]

const indexType = {
    "CPNS": 0,
    "SMAN": 1,
    "E-TEFL": 2
}

function CardStudentHome({ exams, loading }) {

    const [namaTipeUjian, setNamaTipeUjian] = useState('E-TEFL')

    const safeExams = {
        "CPNS": [],
        "SMAN": [],
        "E-TEFL": [],
        ...exams
    };

    const dateFormat = (isoDate) => {
        // Buat objek Date dari ISO string
        const date = new Date(isoDate);

        // Konversi secara manual ke zona WIB (UTC+7)
        const wibOffset = 7 * 60 * 60 * 1000; // 7 jam dalam milidetik
        const wibDate = new Date(date.getTime() + wibOffset);

        // Format tanggal dan waktu secara terpisah
        const tanggal = format(wibDate, "EEEE, d MMMM yyyy", { locale: id });
        const waktu = format(wibDate, "HH:mm 'WIB'");

        return [tanggal, waktu];
    };

    const readyToEnter = (value) => {
        return differenceInSeconds(new Date(), new Date(value)) > 0
    }

    return (
        <Grid2 container spacing={2}>
            {
                toggleCard.map((value, index) => (
                    <Grid2 size={4} key={index}>
                        <button className='user-card-trigger' onClick={() => setNamaTipeUjian(value.nama)}>
                            <div className={`user-card tw-px-4 tw-py-5 tw-rounded-2xl ${namaTipeUjian === value.nama ? `tw-bg-gradient-to-b tw-from-${value.primaryColor} tw-to-${value.secondaryColor} tw-shadow-md` : `tw-border tw-border-${value.primaryColor}`}`}>
                                <h1 className={`tw-text-xl tw-font-bold ${namaTipeUjian === value.nama ? `tw-text-white` : `tw-text-${value.primaryColor}`} tw-mb-2`}>{value.nama}</h1>
                                <div className="user-card-content tw-flex tw-justify-between tw-items-center tw-gap-2">
                                    {value.icon(namaTipeUjian === value.nama ? { color: 'white' } : null)}
                                    <p className={`tw-text-xs tw-text-left ${namaTipeUjian === value.nama ? `tw-text-white` : `tw-text-${value.primaryColor}`}`}>{value.description}</p>
                                </div>
                            </div>
                        </button>
                    </Grid2>
                ))
            }
            <Grid2 size={12}>
                <h2 className={`tw-text-lg tw-font-bold tw-text-${toggleCard[indexType[namaTipeUjian]].primaryColor}`}>Tipe Ujian</h2>
            </Grid2>
            {!loading && safeExams[namaTipeUjian] && safeExams[namaTipeUjian].map((value, index) => (
                <Grid2 size={4} key={index}>
                    <div className={`test-card tw-overflow-hidden tw-border tw-border-${toggleCard[indexType[namaTipeUjian]].primaryColor} tw-bg-${toggleCard[indexType[namaTipeUjian]].secondaryColor} tw-rounded-xl`}>
                        <div className={`test-card-title tw-p-2 tw-border-b tw-border-${toggleCard[indexType[namaTipeUjian]].primaryColor}`}>
                            <h1 className={`tw-text-xl tw-font-bold tw-text-${toggleCard[indexType[namaTipeUjian]].primaryColor} tw-text-center`}>{value?.name}</h1>
                        </div>
                        <div className="test-card-content tw-p-3 tw-flex tw-flex-col tw-items-center">
                            <p className={`tw-font-semibold tw-text-${toggleCard[indexType[namaTipeUjian]].primaryColor} tw-text-center`}>{value?.package?.code}</p>
                            <div className="test-date-time tw-my-4">
                                <div className="jadwal tw-flex tw-items-center tw-gap-2 tw-mb-1">
                                    <CalendarTodayOutlinedIcon fontSize='small' className={`tw-text-${toggleCard[indexType[namaTipeUjian]].primaryColor}`} />
                                    <p className={`tw-text-sm tw-font-medium tw-text-${toggleCard[indexType[namaTipeUjian]].primaryColor}`}>{dateFormat(value?.scheduled_at)[indexType[namaTipeUjian]]}</p>
                                </div>
                                <div className="waktu tw-flex tw-items-center tw-gap-2">
                                    <WatchLaterOutlinedIcon fontSize='small' className={`tw-text-${toggleCard[indexType[namaTipeUjian]].primaryColor}`} />
                                    <p className={`tw-text-sm tw-font-medium tw-text-${toggleCard[indexType[namaTipeUjian]].primaryColor}`}>{dateFormat(value?.scheduled_at)[1]}</p>
                                </div>
                            </div>
                            <Link to={`exam/${value?.id}`} className={`tw-bg-${toggleCard[indexType[namaTipeUjian]].primaryColor} tw-py-1 tw-px-4 tw-rounded-full tw-text-white tw-text-sm`}>{readyToEnter(value?.scheduled_at) ? "Pilih Ujian" : "Detail Ujian"}</Link>
                        </div>
                    </div>
                </Grid2>
            ))}
            {
                loading || !safeExams[namaTipeUjian] || safeExams[namaTipeUjian].length === 0 ?
                [0, 1, 2].map((value) => (
                        <Grid2 size={4} key={value}>
                            <div className={`test-card tw-overflow-hidden tw-border tw-border-white tw-bg-gray-200 tw-rounded-xl`}>
                                <div className={`test-card-title tw-p-2 tw-border-b tw-border-white`}>
                                    <h1 className={`tw-text-xl tw-font-bold tw-text-center`}>
                                        <Skeleton animation={"wave"} width={"100%"} />
                                    </h1>
                                </div>
                                <div className="test-card-content tw-p-3 tw-flex tw-flex-col tw-items-center">
                                    <Skeleton animation={"wave"} width={"75%"} />
                                    <div className="test-date-time tw-my-4 tw-w-3/4">
                                        <div className="jadwal tw-flex tw-items-center tw-gap-2 tw-mb-1 tw-w-full">
                                            <CalendarTodayOutlinedIcon fontSize='small' className={`tw-text-gray-500`} />
                                            <Skeleton animation={"wave"} width={"100%"} />
                                        </div>
                                        <div className="waktu tw-flex tw-items-center tw-gap-2 tw-w-full">
                                            <WatchLaterOutlinedIcon fontSize='small' className={`tw-text-gray-500`} />
                                            <Skeleton animation={"wave"} width={"100%"} />
                                        </div>
                                    </div>
                                    <div className={`tw-rounded-full tw-w-40 tw-overflow-hidden tw-h-7`}>
                                        <Skeleton variant='rounded' animation={"wave"} width={"100%"} height={"100%"} sx={{ margin: 0, padding: 0 }} />
                                    </div>
                                </div>
                            </div>
                        </Grid2>
                    ))
                    : ""
            }
        </Grid2>
    )
}

export default CardStudentHome