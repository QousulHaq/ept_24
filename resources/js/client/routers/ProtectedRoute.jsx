import { useSelector, useDispatch } from "react-redux";
import { Navigate, Outlet } from "react-router-dom";
import { getCode } from "../slices/authSlice";
import { useEffect } from "react";
import { isAfter, addSeconds } from "date-fns";
import _ from "lodash";

const ProtectedRoute = () => {
    const dispatch = useDispatch();
    const isAuthenticated = useSelector((state) => !!state.auth.credential?.access_token);
    const tokenExpired = useSelector((state) => !state.auth.lastFetched ?? isAfter(new Date(), addSeconds(state.lastFetched, _.get(state, 'credential.expires_in'))));

    useEffect(() => {
        if (!isAuthenticated || tokenExpired) {
            dispatch(getCode()); // Ambil kode autentikasi jika token expired
        }
    }, [isAuthenticated, tokenExpired, dispatch]);

    return isAuthenticated ? <Outlet /> : <Navigate to="/client/callback" />;
};

export default ProtectedRoute;
