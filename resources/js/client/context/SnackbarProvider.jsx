// components/SnackbarProvider.tsx
import React from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { hideSnackbar } from '../slices/snackbarSlice';
import { Snackbar, Alert } from '@mui/material';

const SnackbarProvider = () => {
    const dispatch = useDispatch();
    const { open, message, severity } = useSelector((state) => state.snackbar);

    const handleClose = () => {
        dispatch(hideSnackbar());
    };

    return (
        <Snackbar open={open} autoHideDuration={3000} onClose={handleClose}>
            <Alert onClose={handleClose} severity={severity} variant="filled" sx={{ width: '100%' }}>
                {message}
            </Alert>
        </Snackbar>
    );
};

export default SnackbarProvider;
