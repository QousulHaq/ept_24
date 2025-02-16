import React, { useEffect, useState } from 'react'

import DraftTable from '../ReactComponents/DraftTable';
import Pagination from '../ReactComponents/Pagination'

import VisibilityRoundedIcon from '@mui/icons-material/VisibilityRounded';

import "../../../../public/css/back-office/attachment.css"

const AttachmentModal = ({ attachments, setOpen, selectedAttachment }) => {
    const [data, setData] = useState(attachments?.data?.filter((item) => item?.id === selectedAttachment))

    useEffect(() => {
        console.log(data)
    }, [data])

    return (
        <div className="attachment-modal">
            <div className="modal-dialog" role="document">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">Attachment "{data[0]?.title}"</h5>
                        <button type="button" className="close" onClick={() => setOpen(null)}>
                            <span>&times;</span>
                        </button>
                    </div>
                    <div className="modal-body d-flex flex-column">
                        <audio className="align-self-center" src={data[0]?.url} controls></audio>
                        <span className="align-self-center pt-4">Used By: Items</span>
                    </div>
                </div>
            </div>
        </div>
    )
}

function Attachment({ attachments }) {

    const [selectedAttachment, setSelectedAttachment] = useState(null)

    useEffect(() => {
        console.log(attachments)
    }, [])

    return (
        <>
            {selectedAttachment && <AttachmentModal attachments={attachments} setOpen={setSelectedAttachment} selectedAttachment={selectedAttachment} />}
            <div className='bank-soal'>
                <div className="bank-soal-wrap tw-pt-5" style={{ width: "100%", height: "100%" }}>
                    <div className="page-title tw-flex tw-justify-between tw-items-center">
                        <h1 className='tw-text-3xl tw-font-bold tw-text-black tw-m-0'>Your Attachment</h1>
                    </div>
                    <div className="filter-button-page tw-my-5">
                    </div>
                    <div className="table-content">
                        <DraftTable
                            table_data={attachments}
                            showed_data={["title", "mime", "type"]}
                            color="secondary7"
                            action_button={
                                (row) => (
                                    <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-md">
                                        <button className='tw-px-3 tw-py-2 hover:tw-bg-slate-200' onClick={() => setSelectedAttachment(row.id)}>
                                            <VisibilityRoundedIcon fontSize='small' color='secondary' />
                                        </button>
                                    </div>
                                )}
                        />
                    </div>
                    <Pagination links={attachments?.links} />
                </div>
            </div>
        </>
    )
}

export default Attachment