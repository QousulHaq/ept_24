import axios from "axios";

const axiosInstance = axios.create({
    headers: {
        Accept: "application/json",
    },
});

// Request Interceptor
axiosInstance.interceptors.request.use((config) => {
    const store = require("../slices/store").default;
    const state = store.getState();

    const token = state.auth?.credential?.access_token
    const signature = state.exam?.token?.signature

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    if (signature) {
        config.headers["X-Signature-Enroll"] = signature;
    }

    return config;
});

// Response Interceptor
axiosInstance.interceptors.response.use(
    (response) => response,
    async (error) => {
        console.error("API Error:", error.response?.data?.message || "Unknown error");
        throw error;
    }
);

export default axiosInstance;
