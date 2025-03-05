import React, { useEffect, useRef, useState } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useSelector, useDispatch } from 'react-redux';

import { isAfter, format } from 'date-fns';
import { id } from 'date-fns/locale';

import { Grid2 } from '@mui/material'
import CalendarTodayOutlinedIcon from '@mui/icons-material/CalendarTodayOutlined';
import WatchLaterOutlinedIcon from '@mui/icons-material/WatchLaterOutlined';
import ArrowBackIosRoundedIcon from '@mui/icons-material/ArrowBackIosRounded';

import { getExamById, enroll } from '../slices/examSlice';
import store from '../slices/store';


const ExamDetail = () => {
    const { id: exam_id } = useParams()
    const dispatch = useDispatch()
    const navigate = useNavigate()

    const [isFullScreen, setIsFullscreen] = useState(!!document.fullscreenElement);

    const exam = useSelector(getExamById(exam_id))
    const appRef = useRef(null)

    useEffect(() => {
        const handleFullscreenChange = () => {
            setIsFullscreen(!!document.fullscreenElement);
        };

        document.addEventListener('fullscreenchange', handleFullscreenChange);
        return () => document.removeEventListener('fullscreenchange', handleFullscreenChange);
    }, []);

    const isReadyToEnter = () => {
        return (!['not ready', 'banned', 'finished'].includes(exam.detail.status)) && isAfter(new Date(), new Date(exam.scheduled_at));
    }
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
    const enterExam = () => {
        dispatch(enroll(exam_id)).then(() => {
            navigate("/client/perform")
        })
    }
    const toFullScreen = () => {
        if (appRef.current) {
            appRef.current.requestFullscreen();
        }
    }

    useEffect(() => {
        console.log(exam_id)
        console.log(exam)
    }, [])

    return (
        <div className={`exam-detail-wrap tw-overflow-y-auto tw-bg-white tw-p-10 tw-pb-32`} style={{ width: "100%", height: "100vh" }} ref={appRef}>
            <Link to={"/client"} className='tw-flex tw-items-center tw-mb-4'>
                <ArrowBackIosRoundedIcon className='tw-text-primary3' fontSize='small' />
                <p className='tw-text-primary3 tw-text-base tw-font-medium'>Kembali</p>
            </Link>
            <Grid2 size={12}>
                <div className={`test-card tw-overflow-hidden tw-border tw-border-primary1 tw-bg-secondary6 tw-rounded-xl`}>
                    <div className={`test-card-title tw-p-4 tw-border-b tw-border-primary1`}>
                        <h1 className={`tw-text-2xl tw-font-bold tw-text-primary1 tw-text-center`}>{exam?.name}</h1>
                    </div>
                    <div className="test-card-content tw-p-6 tw-pt-3 tw-flex tw-flex-col tw-items-center">
                        <p className={`tw-text-lg tw-font-semibold tw-text-primary1 tw-text-center`}>{exam?.package?.code}</p>
                        <div className="datetime-button-wrapper tw-flex tw-justify-between tw-items-end tw-w-full">
                            <div className="test-date-time">
                                <div className="jadwal tw-flex tw-items-center tw-gap-6 tw-mb-4">
                                    <CalendarTodayOutlinedIcon className={`tw-text-primary1`} style={{ fontSize: "2rem" }} />
                                    <p className={`tw-text-3xl tw-font-medium tw-text-primary1 tw-pb-2`}>{dateFormat(exam?.scheduled_at)[0]}</p>
                                </div>
                                <div className="waktu tw-flex tw-items-center tw-gap-6">
                                    <WatchLaterOutlinedIcon className={`tw-text-primary1`} style={{ fontSize: "2rem" }} />
                                    <p className={`tw-text-3xl tw-font-medium tw-text-primary1 tw-pb-2`}>{dateFormat(exam?.scheduled_at)[1]}</p>
                                </div>
                            </div>
                            {/* <Link to={`exam/${value?.id}`} className={`tw-bg-primary1 tw-py-1 tw-px-4 tw-rounded-full tw-text-white tw-text-sm`}>{readyToEnter(value?.scheduled_at) ? "Siap Dikerjakan" : "Detail Ujian"}</Link> */}
                            {
                                isReadyToEnter() ?
                                    <>
                                        {
                                            isFullScreen ?
                                                <button onClick={enterExam} className={`tw-bg-primary1 tw-py-2 tw-px-4 tw-rounded-full tw-text-white tw-text-base tw-h-fit`}>{"Click Here to Start"}</button>
                                                :
                                                <button onClick={toFullScreen} className={`tw-bg-primary1 tw-py-2 tw-px-4 tw-rounded-full tw-text-white tw-text-base tw-h-fit`}>{"Set Fullscreen"}</button>
                                        }
                                    </>
                                    :
                                    <button disabled className={`tw-bg-primary1 tw-py-2 tw-px-4 tw-rounded-full tw-text-white tw-text-base tw-h-fit tw-opacity-25`}>{"Banned"}</button>
                            }
                        </div>
                    </div>
                </div>
            </Grid2>
        </div>
    )
}

export default ExamDetail