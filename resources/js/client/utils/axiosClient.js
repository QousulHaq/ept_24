// import axios from "axios";

// const axiosInstance = axios.create({
//     headers: {
//         Accept: "application/json",
//     },
// });

// // Request Interceptor
// axiosInstance.interceptors.request.use((config) => {
//     console.log("Interceptor triggered!"); // ✅ Debug di sini

//     const store = require("../slices/store").default;
//     const state = store.getState();

//     const token = state.auth?.credential?.access_token;
//     const signature = state.exam?.token?.signature;

//     console.log("Access Token dari axios:", token);
//     console.log("Signature dari axios:", signature);

//     if (token) {
//         config.headers.Authorization = `Bearer ${token}`;
//     }

//     if (signature) {
//         config.headers["X-Signature-Enroll"] = signature;
//     }

//     return config;
// });

// // Response Interceptor
// axiosInstance.interceptors.response.use(
//     (response) => response,
//     async (error) => {
//         console.error("API Error:", error.response?.data?.message || "Unknown error");
//         console.log("API Error:", error.response?.data?.message || "Unknown error");
//         throw error;
//     }
// );

// export default axiosInstance;

import axios from 'axios'
import store from '../slices/store'

const axiosInstance = axios.create({
    headers: {
        'Accept': 'application/json'
    }
})

axiosInstance.interceptors.request.use(config => {
    const state = store.getState()

    const isAuthenticated = !!state.auth.credential?.access_token
    const isHasEnrolledExam = !!state.exam.chosenExam

    if (isAuthenticated) {
        const token = state.auth.credential.access_token
        config.headers['Authorization'] = 'Bearer ' + token
    }

    if (isHasEnrolledExam) {
        config.headers['X-Signature-Enroll'] = state.exam.token.signature
    }

    return config
})

axiosInstance.interceptors.response.use(_ => _, async error => {
    // await Vue.swal('ERROR', _.get(error, 'response.data.message', ''), 'error')

    throw error
})

export default axiosInstance