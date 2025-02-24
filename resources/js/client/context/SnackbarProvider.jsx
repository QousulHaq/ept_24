import React, { useState, createContext, useContext, useCallback } from 'react'
import { Snackbar, Alert } from '@mui/material'

const SnackbarContext = createContext(null);

export const useSnackbar = () => useContext(SnackbarContext);

const SnackbarProvider = ({ children }) => {
    const [snackbar, setSnackbar] = useState({ open: false, message: '', severity: 'info' });

    const showSnackbar = useCallback((message, severity = 'info') => {
        setSnackbar({ open: true, message, severity });
    }, []);

    const handleClose = () => setSnackbar({ ...snackbar, open: false });

    return (
        <SnackbarContext.Provider value={showSnackbar}>
            {children}
            <Snackbar
                open={snackbar.open}
                autoHideDuration={2000}
                onClose={handleClose}
            >
                <Alert
                    onClose={handleClose}
                    variant='filled'
                    severity={snackbar.severity}
                    sx={{ width: '100%' }}
                >
                    {snackbar.message}
                </Alert>
            </Snackbar>
        </SnackbarContext.Provider>
    );
};

export default SnackbarProvider