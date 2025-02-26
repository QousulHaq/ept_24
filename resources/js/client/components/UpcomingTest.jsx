import React, { useEffect } from 'react'
import { useDispatch, useSelector } from 'react-redux';

import { fetchFutureExam } from '../slices/examSlice';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';

import CalendarTodayOutlinedIcon from '@mui/icons-material/CalendarTodayOutlined';
import WatchLaterOutlinedIcon from '@mui/icons-material/WatchLaterOutlined';
import Skeleton from '@mui/material/Skeleton';

function UpcomingTest() {
    const dispatch = useDispatch()

    const loading = useSelector((state) => state.exam.status === "fetching")
    const exams = useSelector((state) => state.exam.future_matter?.data)

    const dateFormat = (isoDate) => {
        const date = new Date(isoDate);
        const wibOffset = 7 * 60 * 60 * 1000;
        const wibDate = new Date(date.getTime() + wibOffset);
        const tanggal = format(wibDate, "EEEE, d MMMM yyyy", { locale: id });
        const waktu = format(wibDate, "HH:mm 'WIB'");
        return [tanggal, waktu];
    };

    useEffect(() => {
        dispatch(fetchFutureExam())
    }, [])

    useEffect(() => {
        if (exams) {
            console.log({
                future_exam: exams,
            })
        }
    }, [exams])

    return (
        <div className='upcoming-test-card tw-rounded-lg tw-shadow-lg tw-p-6 tw-border tw-border-neutral3'>
            <h1 className={`tw-text-lg tw-text-primary1 tw-font-bold tw-text-center tw-mb-5`}>Tes yang akan datang</h1>
            <div className="upcoming-test-content">
                {exams.map((value, index) => (
                    <div className={`upcoming-test tw-bg-primary1 tw-flex tw-justify-center tw-items-center tw-py-5 tw-rounded-lg tw-gap-4 tw-mb-3`} key={index}>
                        <h1 className='tw-text-xl tw-font-bold tw-text-white'>{value.name}</h1>
                        <div className="test-date-time">
                            <div className="jadwal tw-flex tw-items-center tw-gap-2 tw-mb-1">
                                <CalendarTodayOutlinedIcon fontSize='small' className={`tw-text-white`} />
                                <p className={`tw-text-sm tw-font-medium tw-text-white`}>{dateFormat(value?.scheduled_at)[0]}</p>
                            </div>
                            <div className="waktu tw-flex tw-items-center tw-gap-2">
                                <WatchLaterOutlinedIcon fontSize='small' className={`tw-text-white`} />
                                <p className={`tw-text-sm tw-font-medium tw-text-white`}>{dateFormat(value?.scheduled_at)[1]}</p>
                            </div>
                        </div>
                    </div>
                ))}
                {
                    loading && exams.length === 0 ? <>
                        <Skeleton variant='rounded' animation={"wave"} width={"100%"} height={"6rem"} sx={{ margin: "0 0 0.8rem", padding: 0 }} />
                        <Skeleton variant='rounded' animation={"wave"} width={"100%"} height={"6rem"} sx={{ margin: "0 0 0.8rem", padding: 0 }} />
                        <Skeleton variant='rounded' animation={"wave"} width={"100%"} height={"6rem"} sx={{ margin: "0 0 0.8rem", padding: 0 }} />
                    </> : ""
                }
            </div>
        </div>
    )
}

export default UpcomingTest