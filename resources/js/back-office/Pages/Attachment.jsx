import React, { useEffect, useState } from 'react'
import Pagination from '../ReactComponents/Pagination'

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

const Attachment = ({ attachments }) => {
    const [selectedAttachment, setSelectedAttachment] = useState(null)

    useEffect(() => {
        console.log(attachments)
    }, [])

    return (
        <div className="card">
            {selectedAttachment && <AttachmentModal attachments={attachments} setOpen={setSelectedAttachment} selectedAttachment={selectedAttachment} />}
            <div className="card-body p-0">
                <div className="table-responsive">
                    <table className="table table-stripped table-md">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Mime</th>
                                <th className="text-center">Description</th>
                                <th className="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {
                                attachments?.data.map((attachment, index) => (
                                    <tr key={index}>
                                        <td>{attachments?.from + index}</td>
                                        <td>{attachment?.title}</td>
                                        <td>{attachment?.mime}</td>
                                        <td className="text-center">{attachment?.description ? attachment?.description : '-'}</td>
                                        <td className="align-content-center">
                                            <button type="button" className="btn btn-sm mr-4" onClick={() => setSelectedAttachment(attachment?.id)}><i className="fas fa-eye alert-primary"></i></button>
                                        </td>
                                    </tr>
                                ))
                            }
                        </tbody>
                    </table>
                </div>
            </div>
            <div className="card-footer">
            <Pagination links={attachments?.links}/>
            </div>
        </div>
    )
}

export default Attachment