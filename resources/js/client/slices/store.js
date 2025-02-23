import { configureStore } from "@reduxjs/toolkit";
import { persistReducer, persistStore } from "redux-persist";
import { combineReducers } from "@reduxjs/toolkit";
import localStorage from "redux-persist/lib/storage";

import authReducer from "./authSlice";
import examReducer from "./examSlice";
import notificationReducer from "./notificationSlice";
import performReducer from "./performSlice"

import echoMiddleware from "../middleware/echoMiddleware";

const persistConfig = {
    key: "root",
    storage: localStorage
};

const rootReducer = combineReducers({
    auth: authReducer,
    exam: examReducer,
    notification: notificationReducer,
    perform: performReducer
});

const persistedReducer = persistReducer(persistConfig, rootReducer);

const store = configureStore({
    reducer: persistedReducer,
    middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(echoMiddleware)
});

export const persistor = persistStore(store);
export default store;
