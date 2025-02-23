import { useEffect, useState } from "react";

const useEcho = () => {
  const [echo, setEcho] = useState(null);

  useEffect(() => {
    if (!window.Echo) {
      const Echo = require("laravel-echo");
      window.Pusher = require("pusher-js");

      const echo = new Echo({
        broadcaster: 'pusher',
        enabledTransports: ['ws', 'wss'],
        key: process.env.MIX_PUSHER_APP_KEY,
        cluster: process.env.MIX_PUSHER_APP_CLUSTER,
        wsHost: process.env.MIX_PUSHER_HOST ?? window.location.hostname,
        wsPort: process.env.MIX_PUSHER_PORT ?? window.location.port,
        httpHost: process.env.MIX_PUSHER_HOST,
        forceTLS: false,
        disableStats: true,
      })

      window.Echo = newEcho;
      setEcho(newEcho);
    } else {
      setEcho(window.Echo);
    }
  }, []);

  return echo;
};

export default useEcho;
