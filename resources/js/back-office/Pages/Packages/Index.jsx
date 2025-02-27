import React, { useEffect, useState } from 'react'

import { Paper, Divider } from '@mui/material'

import AddIcon from '@mui/icons-material/Add';
import SaveAltIcon from '@mui/icons-material/SaveAlt';
import ModeEditIcon from '@mui/icons-material/ModeEdit';
import DeleteOutlineIcon from '@mui/icons-material/DeleteOutline';
import VisibilityRoundedIcon from '@mui/icons-material/VisibilityRounded';

import DraftTable from '../../ReactComponents/DraftTable';
import Pagination from '../../ReactComponents/Pagination'
import DeleteModal from '../../ReactComponents/DeleteModal'

import "../../../../../public/css/back-office/packages.css"

import { Link } from '@inertiajs/inertia-react'

function Index({ packages }) {
    const [seletedItems, setSelectedItem] = useState(null)

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
                                <DraftTable
                                    table_data={packages}
                                    showed_data={["title", "code", "created_at"]}
                                    action_button={
                                        (row) => (
                                            <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-md">
                                                <Link className='tw-px-3 tw-py-2 hover:tw-bg-slate-200' href={`/back-office/package/${row.id}?subpackage=${row.children[0].id}`}>
                                                    <VisibilityRoundedIcon fontSize='small' color='secondary' />
                                                </Link>
                                                <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4" }} />
                                                <Link className='tw-px-3 tw-py-2 hover:tw-bg-slate-200' href={`/back-office/package/${row.id}/edit`}>
                                                    <ModeEditIcon fontSize='small' color='primary' />
                                                </Link>
                                                <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4" }} />
                                                <button className='tw-px-3 tw-py-2 hover:tw-bg-slate-200' onClick={() => setSelectedItem(row.id)}>
                                                    <DeleteOutlineIcon fontSize='small' color='error' />
                                                </button>
                                            </div>
                                        )
                                    }
                                />
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