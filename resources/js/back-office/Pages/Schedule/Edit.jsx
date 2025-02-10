import React, { useEffect, useState } from 'react'

import DraftTable from '../../ReactComponents/DraftTable'
import ActionButton from '../../ReactComponents/ActionButton';

import { Grid2, Chip } from '@mui/material'
import SchoolIcon from '@mui/icons-material/School';

import moment from 'moment';

const Edit = ({ exam, packages, participants, new_participants }) => {

    const [name, setName] = useState(exam?.name)
    const [selectedPackage, setSelectedPackage] = useState(exam?.package_id)
    const [participantData, setParticipantData] = useState(new_participants || participants)
    const [scheduledDate, setScheduledDate] = useState(moment(exam?.scheduled_at).format("YYYY-MM-DD"))
    const [scheduledTime, setScheduledTime] = useState(moment(exam?.scheduled_at).format("HH:mm"))

    const [selectedParticipants, setSelectedParticipants] = useState(exam?.participants)

    useEffect(() => {
        console.log(exam)
        console.log(packages)
        console.log(participants)
        console.log("ini new", new_participants)
    }, [])

    const handleSelectParticipant = (hash) => {
        const newItem = participantData?.data.find(item => item.hash === hash)
        setSelectedParticipants([...selectedParticipants, newItem])
    }

    const handleDeleteParticipant = (hash) => {
        const newItem = selectedParticipants.filter(item => item.hash !== hash)
        setSelectedParticipants(newItem)
    }

    return (
        <div className='schedule-edit'>
            <div className="schedule-edit-wrap tw-pt-5" style={{ width: "100%", height: "100%" }}>
                <div className="page-title tw-flex tw-justify-between tw-items-center">
                    <h1 className='tw-text-3xl tw-font-bold tw-text-black tw-m-0'>Edit Exam Schedule</h1>
                </div>
                <div className="exam-form tw-py-5 tw-px-8 tw-bg-white tw-rounded-xl tw-mt-5">
                    <Grid2 container spacing={4}>
                        <Grid2 size={6}>
                            <h2 className='tw-text-lg tw-font-semibold tw-text-black'>Information</h2>
                            <div className="form-group tw-mb-5">
                                <label htmlFor="duration" className='tw-block tw-text-sm tw-font-semibold tw-text-neutral3 tw-mb-2'>Name</label>
                                <input type="text" name="name" id="duration" className='tw-w-full tw-py-2 tw-px-3 tw-border tw-border-neutral3 tw-rounded-lg tw-text-sm' onChange={(e) => setName(e.target.value)} value={name || ''} />
                            </div>
                            <div className="form-group tw-mb-5">
                                <label htmlFor="package" className='tw-block tw-text-sm tw-font-semibold tw-text-neutral3 tw-mb-2'>Package</label>
                                <select name="package" id="package" className='tw-w-full tw-py-2 tw-px-3 tw-border tw-border-neutral3 tw-rounded-lg tw-text-sm' onChange={(e) => setSelectedPackage(e.target.value)} value={selectedPackage || ''}>
                                    <option value="">Select Package</option>
                                    {packages.map((item, index) => (
                                        <option key={index} value={item.id}>{item.title}</option>
                                    ))}
                                </select>
                            </div>
                            <Grid2 container spacing={2}>
                                <Grid2 size={6}>
                                    <div className="form-group tw-mb-5">
                                        <label htmlFor="date" className='tw-block tw-text-sm tw-font-semibold tw-text-neutral3 tw-mb-2'>Date</label>
                                        <input type="date" name="date" id="date" className='tw-w-full tw-py-2 tw-px-3 tw-border tw-border-neutral3 tw-rounded-lg tw-text-sm' onChange={(e) => setScheduledDate(e.target.value)} value={scheduledDate || ''} />
                                    </div>
                                </Grid2>
                                <Grid2 size={6}>
                                    <div className="form-group tw-mb-5">
                                        <label htmlFor="time" className='tw-block tw-text-sm tw-font-semibold tw-text-neutral3 tw-mb-2'>Time</label>
                                        <input type="time" name="time" id="time" className='tw-w-full tw-py-2 tw-px-3 tw-border tw-border-neutral3 tw-rounded-lg tw-text-sm' onChange={(e) => setScheduledTime(e.target.value)} value={scheduledTime || ''} />
                                    </div>
                                </Grid2>
                            </Grid2>
                            <ActionButton
                                text="Apakah anda yakin ingin menyimpan perubahan?"
                                link={`/back-office/schedule/${exam?.id}`}
                                buttonText="Save"
                                colorButton="green"
                                method='patch'
                                data={{
                                    name,
                                    package_id: selectedPackage,
                                    is_anytime: exam?.is_anytime,
                                    scheduled_at: `${scheduledDate} ${scheduledTime}:00`,
                                    ended_at: exam?.ended_at,
                                    duration: exam?.duration,
                                    is_multi_attempt: exam?.is_multi_attempt,
                                    participants: selectedParticipants.map(item => item?.hash)
                                }}
                            />
                        </Grid2>
                        <Grid2 size={6} container spacing={0}>
                            <h2 className='tw-text-lg tw-font-semibold tw-text-black'>Participants</h2>
                            <Grid2 size={12}>
                                <div className="chip-wrapper tw-flex tw-flex-wrap tw-mb-6">
                                    {
                                        selectedParticipants.length > 0 &&
                                        selectedParticipants.map((item, index) => (
                                            <div className="tw-mr-2 tw-mb-2 tw-w-fit" key={index}>
                                                <Chip label={item?.name} variant="outlined" color="info" onDelete={() => handleDeleteParticipant(item?.hash)} icon={<SchoolIcon />} />
                                            </div>
                                        ))
                                    }
                                </div>
                            </Grid2>
                            <Grid2 size={12}>
                                <DraftTable table_data={participantData} showed_data={["username", "email"]} table_action={7} handleSelectParticipant={(hash) => handleSelectParticipant(hash)} selectedParticipants={selectedParticipants} isSearchBar={true}/>
                            </Grid2>
                        </Grid2>
                    </Grid2>
                </div>
            </div>
        </div>
    )
}

export default Edit