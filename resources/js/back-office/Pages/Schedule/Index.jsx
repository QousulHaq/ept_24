import React, { useEffect } from 'react'

import { Paper } from '@mui/material'

import AddIcon from '@mui/icons-material/Add';

import DraftTable from '../../ReactComponents/DraftTable';
// import TableFilterTool from '@components/TableFilterTool';

import { Link } from '@inertiajs/inertia-react';
import { Inertia } from '@inertiajs/inertia';
import Swal from 'sweetalert2';

function Index({ schedule, flash }) {
    useEffect(() => {
        console.log(schedule)
    })

    useEffect(() => {
        if (flash.status) {
            Swal.fire({
                text: flash.status,
                icon: 'success',
            })
            Inertia.replace(window.location.href, { preserveState: true, preserveScroll: true });
        }
    }, [flash])

    const pathDetailSchedule = (schedule_id) => `/back-office/schedule/${schedule_id}/detail`
    const pathEditSchedule = (schedule_id) => `/back-office/schedule/${schedule_id}/edit`

    return (
        <div className='new-bank-soal'>
            <div className="new-bank-soal-wrap tw-pt-5" style={{ width: "100%", height: "100%" }}>
                <div className="page-title tw-flex tw-justify-between tw-items-center">
                    <h1 className='tw-text-3xl tw-font-bold tw-text-black tw-m-0'>Schedule</h1>
                </div>
                <div className="new-bank-soal-button tw-flex tw-items-center tw-mt-5 tw-mb-10">
                    <Link href="/back-office/schedule/create" style={{ textDecoration: 'none' }}>
                        <Paper elevation={2} sx={{ marginRight: '2rem', padding: '1rem', height: "13rem", width: "16rem", borderRadius: "0.8rem" }}>
                            <div className="tw-h-full tw-flex tw-flex-col tw-items-center tw-justify-around">
                                <AddIcon sx={{ fontSize: "5rem", color: "#2B7FD4" }} />
                                <p>Start a blank Bank Question</p>
                            </div>
                        </Paper>
                    </Link>
                </div>
                <div className="card-table-content">
                    <h1 className='tw-text-3xl tw-font-bold tw-my-5 tw-text-black tw-m-0'>List Exam Schedule</h1>
                    <div className="filter-button-page tw-my-5">
                        {/* <TableFilterTool /> */}
                    </div>
                    <div className="table-content">
                        <DraftTable table_data={schedule} showed_data={["name", "scheduled_at", "started_at", "updated_at"]} table_action={4} color="secondary5" pathDetail={pathDetailSchedule} pathEdit={pathEditSchedule} />
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Index