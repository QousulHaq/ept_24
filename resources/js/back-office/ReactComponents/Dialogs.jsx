import React, { useState } from 'react'

import { Dialog, Grid2 } from '@mui/material'

// import { DateRange } from 'react-date-range';
import { addDays } from 'date-fns';

// import 'react-date-range/dist/styles.css'; // main css file
// import 'react-date-range/dist/theme/default.css'; // theme css file

function Dialogs({ openCategory, openCalendar, openType, openDelete, handleCloseCategory, handleCloseCalendar, handleCloseType, handleCloseDelete }) {

    const [deleteMessage, setDeleteMessage] = useState(false)

    const handleCloseDeleteMessage = () => { setDeleteMessage(false) }

    const [dateValue, setDateValue] = useState([
        {
            startDate: new Date(),
            endDate: addDays(new Date(), 7),
            key: 'selection',
        },
    ]);

    return (
        <>
            <Dialog
                open={openCategory}
                onClose={handleCloseCategory}
            >
                <div className="tw-py-4 tw-px-5">
                    <h3 className='tw-font-bold'>Your Category Bank Question</h3>
                    <div className="tw-flex tw-justify-start tw-items-center tw-my-4">
                        <button className='tw-text-xs tw-border tw-border-secondary7 tw-py-1 tw-px-4 tw-rounded-full tw-mr-1 tw-hover:tw-bg-secondary7 hover:tw-text-white'>English</button>
                        <button className='tw-text-xs tw-border tw-border-secondary7 tw-py-1 tw-px-4 tw-rounded-full tw-mr-1 tw-hover:tw-bg-secondary7 hover:tw-text-white'>CPNS</button>
                        <button className='tw-text-xs tw-border tw-border-secondary7 tw-py-1 tw-px-4 tw-rounded-full tw-mr-1 tw-hover:tw-bg-secondary7 hover:tw-text-white'>SMAN</button>
                    </div>
                    <p className='tw-text-xs tw-my-4'>*You can choose multiple category</p>
                    <Grid2 container spacing={1}>
                        <Grid2 size={6}>
                            <button className='tw-text-xs tw-text-primary3 tw-border-primary3 tw-border tw-w-full tw-py-1 tw-rounded-md'>Cancel</button>
                        </Grid2>
                        <Grid2 size={6}>
                            <button className='tw-text-xs tw-text-white tw-bg-primary3 tw-w-full tw-py-1 tw-rounded-md'>Apply Now</button>
                        </Grid2>
                    </Grid2>
                </div>
            </Dialog>

            {/* <Dialog
                open={openCalendar}
                onClose={handleCloseCalendar}
            >
                <div className="tw-py-4 tw-px-5">
                    <DateRange
                        editableDateInputs={true}
                        onChange={item => setDateValue([item.selection])}
                        moveRangeOnFirstSelection={false}
                        ranges={dateValue}
                    />
                    <p className='tw-text-xs tw-my-4'>*You can choose multiple category</p>
                    <Grid2 container spacing={1}>
                        <Grid2 size={6}>
                            <button className='tw-text-xs tw-text-primary3 tw-border-primary3 tw-border tw-w-full tw-py-1 tw-rounded-md'>Cancel</button>
                        </Grid2>
                        <Grid2 size={6}>
                            <button className='tw-text-xs tw-text-white tw-bg-primary3 tw-w-full tw-py-1 tw-rounded-md'>Apply Now</button>
                        </Grid2>
                    </Grid2>
                </div>
            </Dialog> */}

            <Dialog
                open={openDelete}
                onClose={handleCloseDelete}
            >
                <div className="tw-py-4 tw-px-5">
                    <h3 className='tw-font-bold tw-me-20'>Do you want delete this?</h3>
                    <p className='tw-text-xs tw-my-4'>*You cannot return your data</p>
                    <Grid2 container spacing={1}>
                        <Grid2 size={6}>
                            <button className='tw-text-xs tw-text-primary3 tw-border-primary3 tw-border tw-w-full tw-py-1 tw-rounded-md' onClick={handleCloseDelete}>Cancel</button>
                        </Grid2>
                        <Grid2 size={6}>
                            <button className='tw-text-xs tw-text-white tw-bg-primary3 tw-w-full tw-py-1 tw-rounded-md'
                                onClick={() => {
                                    setDeleteMessage(true)
                                    handleCloseDelete()
                                }
                                }>Delete</button>
                        </Grid2>
                    </Grid2>
                </div>
            </Dialog>

            <Dialog
                open={deleteMessage}
                onClose={handleCloseDeleteMessage}
            >
                <div className="tw-p-5">
                    <div className="tw-dialog-wrapper tw-flex tw-flex-col tw-items-center">
                        <h3 className='tw-font-bold'>Your Bank Soal has been deleted</h3>
                        <button className='tw-text-xs tw-mt-3 tw-text-white tw-bg-primary3 tw-py-2 tw-px-10 tw-rounded-md' onClick={() => {handleCloseDeleteMessage()}}>Okay</button>
                    </div>
                </div>
            </Dialog>

            <Dialog
                open={openType}
                onClose={handleCloseType}
            >
                <div className="tw-py-4 tw-px-5">
                    <h3 className='tw-font-bold tw-me-20'>Your Attachment Type</h3>
                    <div className="tw-flex tw-justify-start tw-items-center tw-my-4">
                        <button className='tw-text-xs tw-border tw-border-secondary7 tw-py-1 tw-px-4 tw-rounded-full tw-mr-1 tw-hover:tw-bg-secondary7 tw-hover:tw-text-white'>Image</button>
                        <button className='tw-text-xs tw-border tw-border-secondary7 tw-py-1 tw-px-4 tw-rounded-full tw-mr-1 tw-hover:tw-bg-secondary7 tw-hover:tw-text-white'>Audio</button>
                    </div>
                    <Grid2 container spacing={1}>
                        <Grid2 size={6}>
                            <button className='tw-text-xs tw-text-primary3 tw-border-primary3 tw-border tw-w-full tw-py-1 tw-rounded-md'>Cancel</button>
                        </Grid2>
                        <Grid2 size={6}>
                            <button className='tw-text-xs tw-text-white tw-bg-primary3 tw-w-full tw-py-1 tw-rounded-md'>Apply Now</button>
                        </Grid2>
                    </Grid2>
                </div>
            </Dialog>
        </>
    )
}

export default Dialogs