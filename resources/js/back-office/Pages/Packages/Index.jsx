import React, { useEffect, useState } from 'react'

import { Paper } from '@mui/material'

import AddIcon from '@mui/icons-material/Add';
import SaveAltIcon from '@mui/icons-material/SaveAlt';

import DraftTable from '../../ReactComponents/DraftTable';
import Pagination from '../../ReactComponents/Pagination'
import DeleteModal from '../../ReactComponents/DeleteModal'

import "../../../../../public/css/back-office/packages.css"

import { Link } from '@inertiajs/inertia-react'

function Index({ packages }) {
    const [seletedItems, setSelectedItem] = useState(null)

    const pathDetailPackage = (package_id, subpackage_id) => `/back-office/package/${package_id}?subpackage=${subpackage_id}`
    const pathEditPackage = (package_id, subpackage_id) => `/back-office/package/${package_id}/edit`

    useEffect(() => {
        console.log(packages)
    }, [])

    return (
        <>
            {seletedItems && <DeleteModal item={packages?.data.filter((item) => item.id === seletedItems)} closeDeleteModal={() => setSelectedItem(null)} />}
            <div className='new-bank-soal'>
                <div className={`home-wrap tw-pt-5`} style={{ width: "100%", height: "100%" }}>
                    {/* <div className="new-bank-soal-wrap overflow-y-auto bg-neutral5 p-10 pb-32" style={{ width: "100%", height: "100vh" }}> */}
                    <div className="page-title tw-flex tw-justify-between tw-items-center">
                        <h1 className='tw-text-3xl tw-font-bold tw-text-black tw-m-0'>New Bank Question</h1>
                    </div>
                    <div className="new-bank-soal-button tw-flex tw-items-center tw-mt-5 tw-mb-10">
                        <Link href='package/create' style={{ textDecoration: 'none' }}>
                            <Paper elevation={2} sx={{ marginRight: '2rem', padding: '1rem', height: "13rem", width: "16rem", borderRadius: "0.8rem" }}>
                                <div className="tw-h-full tw-flex tw-flex-col tw-items-center tw-justify-around">
                                    <AddIcon sx={{ fontSize: "5rem", color: "#2B7FD4" }} />
                                    <p className='tw-text-black tw-m-0'>Start a blank Bank Question</p>
                                </div>
                            </Paper>
                        </Link>
                        <Link href='#' style={{ textDecoration: 'none' }}>
                            <Paper elevation={2} sx={{ padding: '1rem', height: "13rem", width: "13rem", borderRadius: "0.8rem" }}>
                                <div className="tw-h-full tw-flex tw-flex-col tw-items-center tw-justify-around">
                                    <SaveAltIcon sx={{ fontSize: "3.5rem", color: "#64398B" }} />
                                    <p className='tw-text-black tw-m-0'>Import from your file</p>
                                </div>
                            </Paper>
                        </Link>
                    </div>
                    <div className="card-table-content">
                        <h1 className='tw-text-3xl tw-font-bold tw-my-5 tw-text-black tw-m-0'>Make from your Draft</h1>
                        <div className="card-table-wrapper tw-py-5 tw-px-8 tw-bg-white tw-rounded-xl">
                            <div className="table-title tw-flex tw-justify-between tw-items-center">
                                <h2 className='tw-text-2xl tw-font-bold tw-text-black tw-m-0'>Draft Details</h2>
                                <button className='tw-bg-primary3 tw-rounded-md tw-px-12 tw-py-2 tw-text-white tw-text-xs tw-font-light'>See all</button>
                            </div>
                            <div className="table-content tw-my-5">
                                <DraftTable table_data={packages} showed_data={["title", "code", "created_at"]} table_action={3} pathDetail={pathDetailPackage} pathEdit={pathEditPackage} handleOpenDelete={(package_id) => setSelectedItem(package_id)} />
                            </div>
                            <Pagination links={packages?.links} />
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}

export default Index