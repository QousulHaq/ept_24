import React, { useEffect } from 'react'

import DraftTable from '../../ReactComponents/DraftTable';
// import TableFilterTool from '@components/TableFilterTool';
import ActionButton from '../../ReactComponents/ActionButton';

import { Divider } from '@mui/material';

import { Link } from '@inertiajs/inertia-react';

function Index({ monitor, flash }) {

    const pathDetailMonitor = (schedule_id) => `/back-office/monitor/${schedule_id}/detail`

    useEffect(() => {
        console.log(monitor)
    })

    return (
        <div className='monitoring'>
            <div className="monitoring-wrap tw-pt-5" style={{ width: "100%", height: "100%" }}>
                <div className="page-title tw-flex tw-justify-between tw-items-center">
                    <h1 className='tw-text-3xl tw-font-bold tw-text-black tw-m-0'>Exam Monitoring</h1>
                </div>
                <div className="filter-button-page tw-my-5">
                    {/* <TableFilterTool /> */}
                </div>
                <div className="table-content">
                    <DraftTable
                        table_data={monitor}
                        showed_data={["name", "scheduled_at", "started_at", "updated_at"]}
                        color="secondary5"
                        action_button={
                            (row) => (
                                <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-xl tw-p-1">
                                    {
                                        row.started_at ?
                                            <ActionButton title="End Exam" text="Apakah anda ingin mengakhiri ujian?" link={`/back-office/monitor/${row.id}/end-exam`} buttonText="End Exam" colorButton="red" status={flash?.status} />
                                            :
                                            <ActionButton title="Start Exam" text="Apakah anda ingin memulai ujian?" link={`/back-office/monitor/${row.id}/start-exam`} buttonText="Start Exam" colorButton="green" status={flash?.status} />
                                    }
                                    <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4", margin: "0 0.25rem" }} />
                                    <Link className="action-button tw-w-fit tw-bg-primary3 tw-text-white tw-py-1 tw-px-4 tw-mx-auto tw-rounded-full" href={`/back-office/monitor/${row.id}/detail`}>
                                        Detail
                                    </Link>
                                </div>
                            )
                        }
                    />
                </div>
            </div>
        </div>
    )
}

export default Index