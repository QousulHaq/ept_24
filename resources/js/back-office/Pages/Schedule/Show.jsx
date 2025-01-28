import React, { useEffect, useState } from 'react'

import DraftTable from '../../ReactComponents/DraftTable';

function ExamDetails({ exam }) {
    const [examData, setExamData] = useState({})
    const [participants, setParticipants] = useState({})

    useEffect(() => {
        if (Object.keys(exam).length !== 0) {
            setExamData(
                {
                    "data": [{
                        "name": exam.name,
                        "scheduled_at": exam.scheduled_at,
                        "started_at": exam.started_at,
                        "updated_at": exam.updated_at,
                        "created_at": exam.created_at
                    }],
                    "from": 1,
                }
            )
            setParticipants(
                {
                    "data": exam.participants,
                    "from": 1,
                }
            )
        }
    }, [exam])

    useEffect(() => {
        console.log(exam)
    }, [])

    return (
        <div className='exam-details'>
            <div className="exam-details-wrap tw-pt-5" style={{ width: "100%", height: "100%" }}>
                <div className="page-title tw-flex tw-justify-between tw-items-center">
                    <h1 className='tw-text-3xl tw-font-bold tw-text-black tw-m-0'>Detail Exam Result</h1>
                </div>
                <div className="table-content tw-mt-5">
                    {
                        Object.keys(examData).length !== 0 && (
                            <DraftTable table_data={examData} showed_data={["name", "scheduled_at", "started_at", "updated_at", "created_at"]} table_action={0} color="secondary5" />
                        )
                    }
                </div>
                <div className="page-title tw-flex tw-justify-between tw-items-center tw-mt-5">
                    <h1 className='tw-text-3xl tw-font-bold tw-text-black tw-m-0'>List Particiapant</h1>
                </div>
                <div className="table-content tw-mt-5">
                    {
                        Object.keys(participants).length !== 0 && (
                            <DraftTable table_data={participants} showed_data={["name", "username", "email"]} table_action={0} color="secondary5" />
                        )
                    }
                </div>
            </div>
        </div>
    )
}

export default ExamDetails