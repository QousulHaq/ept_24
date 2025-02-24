import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { useDispatch } from "react-redux";
import { login } from "../slices/authSlice";

const AuthCallback = () => {
    const navigate = useNavigate();
    const dispatch = useDispatch()

    useEffect(() => {
        const hash = window.location.hash;
        const params = new URLSearchParams(hash.substring(1));

        const credentials = {
            access_token: params.get('access_token'),
            token_type: params.get('token_type'),
            expires_in: params.get('expires_in'),
            state: params.get('state'),
        };

        dispatch(login(credentials)).then(() => navigate("/client"))

        console.log({
            access_token: params.get('access_token'),
            token_type: params.get('token_type'),
            expires_in: params.get('expires_in'),
            state: params.get('state'),
        });

    }, [navigate]);

    return <h1>Authenticating...</h1>;
};

export default AuthCallback;
