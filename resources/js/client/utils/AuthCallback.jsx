import { useEffect } from "react";
import { useNavigate, useLocation } from "react-router-dom";

const AuthCallback = () => {
    const navigate = useNavigate();
    const location = useLocation();

    useEffect(() => {
        const params = new URLSearchParams(location.search);
        const accessToken = params.get("access_token");

        if (accessToken) {
            console.log("Access Token:", accessToken);
            localStorage.setItem("access_token", accessToken);
        }

        navigate("/client/list");
    }, [location, navigate]);

    return <h1>Authenticating...</h1>;
};

export default AuthCallback;
