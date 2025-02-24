// utils/echoService.js
import Echo from "laravel-echo";
import Pusher from "pusher-js";

let echoInstance = null;

export const getEchoInstance = (authToken) => {
    if (!echoInstance) {
        window.Pusher = Pusher;
        echoInstance = new Echo({
            broadcaster: "pusher",
            enabledTransports: ["ws", "wss"],
            key: process.env.MIX_PUSHER_APP_KEY,
            cluster: process.env.MIX_PUSHER_APP_CLUSTER,
            wsHost: process.env.MIX_PUSHER_HOST || window.location.hostname,
            wsPort: process.env.MIX_PUSHER_PORT || window.location.port,
            forceTLS: false,
            disableStats: true,
            auth: {
                headers: {
                    Authorization: `Bearer ${authToken}`,
                    Accept: "application/json",
                },
            },
        });
    }else {
        // Update token jika Echo sudah diinisialisasi
        echoInstance.connector.pusher.config.auth.headers = {
            Authorization: `Bearer ${authToken}`,
            Accept: "application/json",
        };
    }
    return echoInstance;
};

export const destroyEchoInstance = () => {
    if (echoInstance) {
        echoInstance.disconnect();
        echoInstance = null;
    }
};
