import React, { useEffect, useState } from 'react'
import { Link } from '@inertiajs/inertia-react'

import Pagination from '../../ReactComponents/Pagination'
import DeleteModal from '../../ReactComponents/DeleteModal'

import "../../../../../public/css/back-office/packages.css"

const Index = ({ packages }) => {
    const [seletedItems, setSelectedItem] = useState(null)

    useEffect(() => {
        console.log(packages)
    }, [])

    return (
        <>
            {seletedItems && <DeleteModal item={packages?.data.filter((item) => item.id === seletedItems)} closeDeleteModal={() => setSelectedItem(null)} />}
            <div className="page-title-container">
                <h3 className='page-title text-black'>New Bank Question</h3>
            </div>
            <div className="packages-content">
                <div className="new-packages-button-wrapper">
                    <Link href='package/create' className="add-new-package">
                        <i className="add-icon fas fa-regular fa-plus"></i>
                        <p>Start a blank Bank Question</p>
                    </Link>
                    <a href="#" className="add-new-package">
                        <i className="add-icon fas fa-regular fa-cube"></i>
                        <p>Get Distributed Package</p>
                    </a>
                </div>
                <h3 className='page-title text-black'>Make From Your Draft</h3>
                <div className="card-table">
                    <div className="card-table-wrapper">
                        <div className="table-title">
                            <h4 className='table-title-text text-black'>Draft Details</h4>
                        </div>
                        <div className="table-content">
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
                                                    <th className='text-black'>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {
                                                    packages?.data.length === 0 ?
                                                        (
                                                            <tr>
                                                                <td colspan="7" className="text-center">There is no packages</td>
                                                            </tr>
                                                        )
                                                        :
                                                        packages?.data.map((data, index) => (
                                                            <tr key={index}>
                                                                <td>{packages?.from + index}</td>
                                                                <td>{data?.title}</td>
                                                                <td>{data?.description}</td>
                                                                <td>{data?.level}</td>
                                                                <td><i className={`fas ${data?.is_encrypted ? 'fa-check' : 'fa-times'}`}></i></td>
                                                                <td>{new Intl.DateTimeFormat('id-ID', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' }).format(new Date(data?.updated_at))}</td>
                                                                <td className="align-content-center">
                                                                    <Link href={`/back-office/package/${data?.id}?subpackage=${data?.children[0].id}`} className="mr-2"><i className="fas fa-eye alert-info"></i></Link>
                                                                    <Link href={`/back-office/package/${data?.id}/edit`} className="mr-2"><i className="fas fa-edit alert-warning"></i></Link>
                                                                    <button type='button' className="delete-button" onClick={() => setSelectedItem(data?.id)}><i className="fas fa-trash alert-danger"></i></button>
                                                                </td>
                                                            </tr>
                                                        ))
                                                }
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div className="card-footer">
                                    <Pagination links={packages?.links} />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}

export default Index