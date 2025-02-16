import React, { useEffect } from 'react'

import { Paper } from '@mui/material'

import AddIcon from '@mui/icons-material/Add';
import Divider from '@mui/material/Divider';

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
                        <DraftTable
                            table_data={schedule}
                            showed_data={["name", "scheduled_at", "started_at", "updated_at"]}
                            color="secondary5"
                            action_button={
                                (row) => (
                                    <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-xl tw-p-1">
                                        <Link className="action-button tw-w-fit tw-bg-primary3 tw-text-white tw-py-1 tw-px-4 tw-mx-auto tw-rounded-full tw-no-underline" href={`/back-office/schedule/${row.id}/detail`}>
                                            Detail
                                        </Link>
                                        <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4", margin: "0 0.25rem" }} />
                                        <Link className="action-button tw-w-fit tw-bg-yellow-500 tw-text-white tw-py-1 tw-px-4 tw-mx-auto tw-rounded-full" href={`/back-office/schedule/${row.id}/edit`}>
                                            Edit
                                        </Link>
                                    </div>
                                )
                            }
                        />
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Index