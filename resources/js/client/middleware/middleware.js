import { useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import { getAuthenticated, getTokenExpired, getCode } from '../slices/authSlice';

const useAuthMiddleware = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const dispatch = useDispatch();

    const isAuthenticated = useSelector(getAuthenticated);
    const tokenExpired = useSelector(getTokenExpired);

    useEffect(() => {
        if (location.pathname.includes('/client')) {
            if (!isAuthenticated || tokenExpired) {
                dispatch(getCode());
            }
        }
        navigate("/client")
    }, []);
};

const usePresenceMiddleware = () => {
    const dispatch = useDispatch();
    const isAuthenticated = useSelector(getAuthenticated);

    useEffect(() => {
        if (isAuthenticated) {
            //   dispatch({ type: 'presence/checkPresence' });
        }
    }, []);
};

export { useAuthMiddleware, usePresenceMiddleware };
