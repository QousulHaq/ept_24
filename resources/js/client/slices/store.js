import { configureStore, combineReducers } from "@reduxjs/toolkit";
import { persistReducer, persistStore } from "redux-persist";
import localStorage from "redux-persist/lib/storage";

import authReducer from "./authSlice";
import examReducer from "./examSlice";
import notificationReducer from "./notificationSlice";
import performReducer from "./performSlice";
import snackbarReducer from "./snackbarSlice"

import echoMiddleware from "../middleware/echoMiddleware";

// Konfigurasi persist
const persistConfig = {
    key: "root",
    storage: localStorage,
    whitelist: ["auth", "exam", "notification", "perform"], // Tentukan state yang ingin di-persist
};

// Gabungkan semua reducers
const rootReducer = combineReducers({
    auth: authReducer,
    exam: examReducer,
    notification: notificationReducer,
    perform: performReducer,
    snackbar : snackbarReducer
});

const persistedReducer = persistReducer(persistConfig, rootReducer);

// Konfigurasi store
const store = configureStore({
    reducer: persistedReducer,
    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware({
            serializableCheck: false,
        }).concat(echoMiddleware),
    devTools: process.env.NODE_ENV !== "production", // Aktifkan Redux DevTools di mode pengembangan
});

export const persistor = persistStore(store);
export default store;
