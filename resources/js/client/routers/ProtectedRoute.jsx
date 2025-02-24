import { useSelector, useDispatch } from "react-redux";
import { getCode, getAuthenticated, getTokenExpired } from "../slices/authSlice";
import { useEffect } from "react";

const ProtectedRoute = ({ children }) => {
    const dispatch = useDispatch();
    const isAuthenticated = useSelector(getAuthenticated);
    const tokenExpired = useSelector(getTokenExpired);

    useEffect(() => {
        if (!isAuthenticated || tokenExpired) {
            dispatch(getCode()); // Ambil kode autentikasi jika token expired
        }
    }, [isAuthenticated, tokenExpired]);

    return children;
};

export default ProtectedRoute;
