import React, { useEffect } from 'react'
import { useState } from 'react';

import ActionButton from './ActionButton';

import { Chip, Divider, Card, Badge } from '@mui/material'
import { Table, TableCell, TableContainer, TableHead, TableBody, TableRow } from '@mui/material'

import ModeEditIcon from '@mui/icons-material/ModeEdit';
import DeleteOutlineIcon from '@mui/icons-material/DeleteOutline';
import VisibilityRoundedIcon from '@mui/icons-material/VisibilityRounded';

import { Link } from '@inertiajs/inertia-react'

import Swal from 'sweetalert2'

function DraftTable({ table_data, showed_data, color, table_action, pathDetail, pathEdit, handleOpenDelete, status }) {
    const [openDelete, setOpenDelete] = useState(false)

    const handleCloseDelete = () => [setOpenDelete(false)]

    const formattedTitle = (title) => {
        return title.replace(/_/g, ' ').toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    const handleOpenLogs = (studentName, log) => {
        Swal.fire({
            title: `${studentName} Logs`,
            text: `Content : ${log}`,
        });
    }

    return (
        <>
            <TableContainer sx={{ borderRadius: '1rem' }} component={Card} elevation={0}>
                <Table sx={{ minWidth: 650 }} aria-label="simple table">
                    <TableHead className={color ? `tw-bg-${color}` : `tw-bg-neutral5`} >
                        <TableRow>
                            <TableCell sx={{ fontWeight: 'bold' }}>No.</TableCell>
                            {
                                table_data?.data.length !== 0 ?
                                    (Object.keys(table_data?.data[0]).map((item, index) => (
                                        showed_data.includes(item) ?
                                            (
                                                item === "category" ?
                                                    <TableCell sx={{ fontWeight: 'bold' }} align='center' key={index}>{formattedTitle(item)}</TableCell>
                                                    :
                                                    <TableCell sx={{ fontWeight: 'bold' }} key={index}>{formattedTitle(item)}</TableCell>
                                            )
                                            :
                                            ("")
                                    )))
                                    :
                                    (showed_data.map((item, index) => (
                                        <TableCell sx={{ fontWeight: 'bold' }} key={index}>{formattedTitle(item)}</TableCell>
                                    )))
                            }

                            {/* Action Column */}

                            {[1, 2, 3, 4, 5, 6].includes(table_action) && (
                                <TableCell sx={{ fontWeight: 'bold' }} align='center'>Actions</TableCell>
                            )}

                            {table_action === 7 && (
                                <>
                                    <TableCell sx={{ fontWeight: 'bold' }} align='center'>Actions</TableCell>
                                    <TableCell sx={{ fontWeight: 'bold' }} align='center'>Actions</TableCell>
                                </>
                            )}
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {
                            table_data?.data.length === 0 &&
                            <TableRow>
                                <TableCell component="th" scope="row" colSpan={6} sx={{ textAlign: 'center' }}>no data  found</TableCell>
                            </TableRow>
                        }

                        {table_data?.data.map((item, index) => (
                            <TableRow
                                key={index}
                                sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
                            >
                                <TableCell component="th" scope="row">{table_data?.from + index}</TableCell>

                                {
                                    Object.values(item).map((value, index) => (
                                        showed_data.includes(Object.keys(item)[index]) && (
                                            // typeof value === 'object' ?
                                            // <TableCell align='center'>
                                            //     {/* <Chip label={value.category_name} sx={{ bgcolor: `${value.color}`, padding: '0.5rem 1rem' }} /> */}
                                            //     <Chip label={"English"} size='small' sx={{ bgcolor: `hsla(356, 58%, 53%, 0.3)`, color: 'white', padding: '0.5rem 1rem' }} />
                                            // </TableCell>
                                            // :
                                            (
                                                !isNaN(Date.parse(value)) && typeof value === 'string' ?
                                                    <TableCell component="th" scope="row" key={index}>{new Intl.DateTimeFormat('id-ID', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' }).format(new Date(value))}</TableCell>
                                                    :
                                                    <TableCell component="th" scope="row" key={index}>{value}</TableCell>
                                            )
                                        )
                                    ))
                                }

                                {/* Action Button */}

                                {
                                    table_action === 1 &&
                                    <TableCell align='center'>
                                        <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-md">
                                            {
                                                pathDetail ?
                                                    <Link className='tw-px-3 tw-py-2 hover:tw-bg-slate-200' href={pathDetail(item?.id, item?.children[0].id)}>
                                                        <VisibilityRoundedIcon fontSize='small' color='secondary' />
                                                    </Link>
                                                    :
                                                    <button className='tw-px-3 tw-py-2 hover:tw-bg-slate-200' onClick={() => handleOpenDelete(item.id)}>
                                                        <VisibilityRoundedIcon fontSize='small' color='secondary' />
                                                    </button>
                                            }
                                        </div>
                                    </TableCell>
                                }

                                {
                                    table_action === 2 &&
                                    <TableCell align='center'>
                                        <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-md">
                                            <Link className='px-3 py-2 hover:bg-slate-200' href={pathDetail(item?.id, item?.children[0].id)}>
                                                <ModeEditIcon fontSize='small' color='primary' />
                                            </Link>
                                            <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4" }} />
                                            <Link className='px-3 py-2 hover:bg-slate-200' onClick={handleOpenDelete}>
                                                <DeleteOutlineIcon fontSize='small' color='error' />
                                            </Link>
                                        </div>
                                    </TableCell>
                                }

                                {
                                    table_action === 3 &&
                                    <TableCell align='center'>
                                        <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-md">
                                            <Link className='tw-px-3 tw-py-2 hover:tw-bg-slate-200' href={pathDetail(item?.id, item?.children[0].id)}>
                                                <VisibilityRoundedIcon fontSize='small' color='secondary' />
                                            </Link>
                                            <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4" }} />
                                            <Link className='tw-px-3 tw-py-2 hover:tw-bg-slate-200' href={pathEdit(item?.id)}>
                                                <ModeEditIcon fontSize='small' color='primary' />
                                            </Link>
                                            <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4" }} />
                                            <button className='tw-px-3 tw-py-2 hover:tw-bg-slate-200' onClick={() => handleOpenDelete(item.id)}>
                                                <DeleteOutlineIcon fontSize='small' color='error' />
                                            </button>
                                        </div>
                                    </TableCell>
                                }

                                {
                                    table_action === 4 &&
                                    <TableCell align='center'>
                                        <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-xl tw-p-1">
                                            <a className="action-button tw-w-fit tw-bg-primary3 tw-text-white tw-py-1 tw-px-4 tw-mx-auto tw-rounded-full tw-no-underline" href={pathDetail(item?.id)}>
                                                Detail
                                            </a>
                                            <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4", margin: "0 0.25rem" }} />
                                            <a className="action-button tw-w-fit tw-bg-yellow-500 tw-text-white tw-py-1 tw-px-4 tw-mx-auto tw-rounded-full" href={pathEdit(item?.id)}>
                                                Edit
                                            </a>
                                        </div>
                                    </TableCell>
                                }

                                {
                                    table_action === 5 &&
                                    <TableCell align='center'>
                                        <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-xl tw-p-1">
                                            {
                                                item?.started_at ?
                                                    <ActionButton title="End Exam" text="Apakah anda ingin mengakhiri ujian?" link={`/back-office/monitor/${item?.id}/end-exam`} buttonText="End Exam" colorButton="red" status={status} />
                                                    :
                                                    <ActionButton title="Start Exam" text="Apakah anda ingin memulai ujian?" link={`/back-office/monitor/${item?.id}/start-exam`} buttonText="Start Exam" colorButton="green" status={status} />
                                            }
                                            <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4", margin: "0 0.25rem" }} />
                                            <Link className="action-button tw-w-fit tw-bg-primary3 tw-text-white tw-py-1 tw-px-4 tw-mx-auto tw-rounded-full" href={pathDetail(item?.id)}>
                                                Detail
                                            </Link>
                                        </div>
                                    </TableCell>
                                }

                                {
                                    table_action === 6 &&
                                    <TableCell align='center'>
                                        <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-xl tw-p-1">
                                            {
                                                item?.detail?.status !== "banned" ?
                                                    <ActionButton title="Disqualify Participant" text="Apakah anda ingin mendiskualifikasi peserta?" link={`/back-office/monitor/${item?.detail?.exam_id}/start-exam/${item?.hash}`} buttonText="Disqualify" colorButton="red" status={status} disabled={table_data?.started_at ? false : true}/>
                                                    :
                                                    <ActionButton title="Qualify Participant" text="Apakah anda ingin mendiskualifikasi peserta?" link={`/back-office/monitor/${item?.detail?.exam_id}/start-exam/${item?.hash}/qualified`} buttonText="Qualify" colorButton="green" status={status} />
                                            }
                                            <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4", margin: "0 0.25rem" }} />
                                            <Badge color='secondary' badgeContent={item.detail.logs.length}>
                                                <button className="action-button tw-w-fit tw-bg-primary3 tw-text-white tw-py-1 tw-px-4 tw-mx-auto tw-rounded-full" onClick={() => handleOpenLogs(item?.name, item?.detail?.logs)}>
                                                    Logs
                                                </button>
                                            </Badge>
                                        </div>
                                    </TableCell>
                                }

                                {
                                    table_action === 7 && <>
                                        <TableCell align='center'>
                                            <Link className="action-button w-fit bg-primary3 text-white py-1 px-4 mx-auto rounded-full" >
                                                Detail
                                            </Link>
                                        </TableCell>
                                        <TableCell align='center'>
                                            <Link className="action-button w-fit bg-primary3 text-white py-1 px-4 mx-auto rounded-full">
                                                Apply
                                            </Link>
                                        </TableCell>
                                    </>
                                }
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </TableContainer>
        </>
    )
}

export default DraftTable