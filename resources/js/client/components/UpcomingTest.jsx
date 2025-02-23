import React from 'react'

import { tipeUjian } from '../data/user-home-data';

import CalendarTodayOutlinedIcon from '@mui/icons-material/CalendarTodayOutlined';
import WatchLaterOutlinedIcon from '@mui/icons-material/WatchLaterOutlined';

function UpcomingTest({namaTipe}) {

    return (
        <div className='upcoming-test-card tw-rounded-lg tw-shadow-lg tw-p-6 tw-border tw-border-neutral3'>
            <h1 className={`tw-text-lg tw-text-${tipeUjian[namaTipe][0].primaryColor} tw-font-bold tw-text-center tw-mb-5`}>Tes yang akan datang</h1>
            <div className="upcoming-test-content">
                {tipeUjian[namaTipe].map((value, index) => (
                    <div className={`upcoming-test tw-bg-${value.primaryColor} tw-flex tw-justify-center tw-items-center tw-py-5 tw-rounded-lg tw-gap-4 tw-mb-3`} key={index}>
                        <h1 className='tw-text-xl tw-font-bold tw-text-white'>{value.title}</h1>
                        <div className="test-date-time">
                            <div className="jadwal tw-flex tw-items-center tw-gap-2 tw-mb-1">
                                <CalendarTodayOutlinedIcon fontSize='small' className={`tw-text-white`} />
                                <p className={`tw-text-sm tw-font-medium tw-text-white`}>{value.jadwal}</p>
                            </div>
                            <div className="waktu tw-flex tw-items-center tw-gap-2">
                                <WatchLaterOutlinedIcon fontSize='small' className={`tw-text-white`} />
                                <p className={`tw-text-sm tw-font-medium tw-text-white`}>{value.waktu}</p>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    )
}

export default UpcomingTest