import React, { useEffect, useState } from 'react'

import ActionButton from './ActionButton';
import SearchBar from './SearchBar';

import { Chip, Divider, Card, Badge, CircularProgress } from '@mui/material'
import { Table, TableCell, TableContainer, TableHead, TableBody, TableRow } from '@mui/material'

import ModeEditIcon from '@mui/icons-material/ModeEdit';
import DeleteOutlineIcon from '@mui/icons-material/DeleteOutline';
import VisibilityRoundedIcon from '@mui/icons-material/VisibilityRounded';

import { Link } from '@inertiajs/inertia-react'

import Swal from 'sweetalert2'
import moment from 'moment';

function DraftTable({ table_data, showed_data, color, action_button = false, searchBar = false, loadingSearch = false, participantsStatusData = false }) {

    const formattedTitle = (title) => {
        return title.replace(/_/g, ' ').toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    useEffect(() => {
        moment.locale('id');
    }, [])

    return (
        <>
            {searchBar && searchBar}
            <TableContainer sx={{ borderRadius: '1rem' }} component={Card} elevation={0}>
                <Table sx={{ minWidth: 650 }} aria-label="simple table">
                    <TableHead className={color ? `tw-bg-${color}` : `tw-bg-neutral5`}>
                        <TableRow>
                            <TableCell sx={{ fontWeight: 'bold' }}>No.</TableCell>
                            {table_data?.data.length !== 0
                                ? Object.keys(table_data?.data[0]).map((item, index) => (
                                    showed_data.includes(item) && (
                                        <TableCell
                                            sx={{ fontWeight: 'bold' }}
                                            align={item === "category" ? 'center' : 'left'}
                                            key={`header-${item}-${index}`}
                                        >
                                            {formattedTitle(item)}
                                        </TableCell>
                                    )
                                ))
                                : showed_data.map((item, index) => (
                                    <TableCell
                                        sx={{ fontWeight: 'bold' }}
                                        key={`header-empty-${item}-${index}`}
                                    >
                                        {formattedTitle(item)}
                                    </TableCell>
                                ))}

                            {participantsStatusData && (
                                <>
                                    <TableCell sx={{ fontWeight: 'bold' }} align='center' key="status-header">Status</TableCell>
                                    <TableCell sx={{ fontWeight: 'bold' }} align='center' key="connection-header">Connection</TableCell>
                                </>
                            )}

                            {action_button && (
                                <TableCell sx={{ fontWeight: 'bold' }} align='center' key="action-header">Actions</TableCell>
                            )}
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {table_data?.data.length === 0 && (
                            <TableRow>
                                <TableCell component="th" scope="row" colSpan={6} sx={{ textAlign: 'center' }}>
                                    no data found
                                </TableCell>
                            </TableRow>
                        )}

                        {loadingSearch ? (
                            <TableRow>
                                <TableCell component="th" scope="row" colSpan={6} sx={{ textAlign: 'center' }}>
                                    <CircularProgress />
                                </TableCell>
                            </TableRow>
                        ) : (
                            table_data?.data.map((item, rowIndex) => (
                                <TableRow
                                    key={`row-${item.id || rowIndex}`}
                                    sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
                                >
                                    <TableCell component="th" scope="row">
                                        {table_data?.from + rowIndex}
                                    </TableCell>

                                    {Object.entries(item).map(([key, value], cellIndex) => (
                                        showed_data.includes(key) && (
                                            <TableCell
                                                component="th"
                                                scope="row"
                                                key={`cell-${item.id || rowIndex}-${key}-${cellIndex}`}
                                            >
                                                {moment(value, moment.ISO_8601, true).isValid()
                                                    ? moment(value).format('ddd, DD MMM YYYY')
                                                    : value}
                                            </TableCell>
                                        )
                                    ))}

                                    {participantsStatusData && (
                                        <>
                                            <TableCell
                                                component="th"
                                                scope="row"
                                                align='center'
                                                key={`status-${item.id || rowIndex}`}
                                            >
                                                {participantsStatusData.status(item)}
                                            </TableCell>
                                            <TableCell
                                                component="th"
                                                scope="row"
                                                align='center'
                                                key={`connection-${item.id || rowIndex}`}
                                            >
                                                {participantsStatusData.connection(item)}
                                            </TableCell>
                                        </>
                                    )}

                                    {action_button && (
                                        <TableCell
                                            component="th"
                                            scope="row"
                                            key={`action-${item.id || rowIndex}`}
                                        >
                                            {action_button(item)}
                                        </TableCell>
                                    )}
                                </TableRow>
                            ))
                        )}
                    </TableBody>
                </Table>
            </TableContainer>
        </>
    );
}

export default DraftTable