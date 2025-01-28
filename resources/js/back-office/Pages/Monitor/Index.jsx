import React, { useEffect } from 'react'

import DraftTable from '../../ReactComponents/DraftTable';
// import TableFilterTool from '@components/TableFilterTool';

function Index({monitor, flash}) {

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
                    <DraftTable table_data={monitor} showed_data={["name", "scheduled_at", "started_at", "updated_at"]} table_action={5} color="secondary5" pathDetail={pathDetailMonitor} status={flash?.status}/>
                </div>
            </div>
        </div>
    )
}

export default Index