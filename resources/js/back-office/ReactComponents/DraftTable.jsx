import React from 'react'

const DraftTable = ({ packages }) => {
    return (
        <div className="card">
            <div className="card-body p-0">
                <div className="table-responsive">
                    <table className="table table-striped table-md table-head">
                        <thead>
                            <tr>
                                <th className='text-black'>No.</th>
                                <th className='text-black'>Name</th>
                                <th className='text-black'>Description</th>
                                <th className='text-black'>Level</th>
                                <th className='text-black'>Encrypted</th>
                                <th className='text-black'>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            {
                                packages ? (
                                    packages.data.map((data, index) => (
                                        <tr key={index}>
                                            <td>{index + 1}</td>
                                            <td>{data?.title}</td>
                                            <td>{data?.description}</td>
                                            <td>{data?.level}</td>
                                            <td><i className={`fas ${data?.is_encrypted ? 'fa-check' : 'fa-times'}`}></i></td>
                                            <td>{new Intl.DateTimeFormat('id-ID', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' }).format(new Date(data?.updated_at))}</td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colspan="6" className="text-center">There is no packages</td>
                                    </tr>
                                )
                            }
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    )
}

export default DraftTable