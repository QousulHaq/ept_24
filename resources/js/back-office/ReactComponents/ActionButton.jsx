import React, { useState, useEffect } from 'react'

import { Backdrop, CircularProgress } from '@mui/material';
import { Inertia } from '@inertiajs/inertia'
import Swal from 'sweetalert2'

function LoadingDialog({ open }) {
    return (
        <Backdrop
            sx={(theme) => ({ color: '#fff', zIndex: theme.zIndex.drawer + 1 })}
            open={open}
        >
            <CircularProgress color="inherit" />
        </Backdrop>
    )
}

const ActionButton = ({ title, text, link, buttonText, colorButton, disabled = false, status, method = "get", data }) => {

    const [loading, setLoading] = useState(false)

    const sweetAlertHandler = () => {
        Swal.fire({
            title,
            text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, lanjutkan!',
            confirmButtonColor: '#2B7FD4',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                setLoading(true)
                switch (method) {
                    case "get":
                        Inertia.visit(link)
                        break;
                    case "patch":
                        Inertia.patch(link, data)
                        break;
                    case "post":
                        Inertia.post(link, data)
                        break;
                    default:
                        break;
                }
            }
        });
    }

    useEffect(() => {
        if (status) {
            console.log("success message", status)
            setLoading(false)
            Swal.fire({
                text: status,
                icon: 'success',
            })
            Inertia.replace(window.location.href, { preserveState: true, preserveScroll: true });
        }
    }, [status])

    return (
        <>
            <LoadingDialog open={loading} />
            <button disabled={disabled ? disabled : loading ? true : false} className={`tw-bg-${colorButton}-500 tw-text-white tw-rounded-full tw-px-3 tw-py-1 ${disabled && "tw-opacity-40"}`} style={{ textDecoration: "none" }} onClick={() => sweetAlertHandler()}>{buttonText}</button>
        </>
    )
}

export default ActionButton