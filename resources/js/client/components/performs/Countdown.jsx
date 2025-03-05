import React, { useEffect, useState } from 'react';

const Countdown = ({ availableTime, gap = 5, freeze = false }) => {
    const [time, setTime] = useState(availableTime);

    const sec2time = (timeInSeconds) => {
        const pad = (num, size) => ('000' + num).slice(size * -1);
        const minutes = Math.floor(timeInSeconds / 60) % 60;
        const seconds = Math.floor(timeInSeconds - minutes * 60);
        return `${pad(minutes, 2)}:${pad(seconds, 2)}`;
    };

    useEffect(() => {
        setTime(availableTime);
    }, [availableTime]);

    useEffect(() => {
        if (time > 0) {
            const timer = setTimeout(() => {
                if (!freeze) {
                    setTime((prevTime) => {
                        if (prevTime % gap === 0) {
                            // Emit 'every' event
                            console.log('every', gap);
                        }
                        if (prevTime <= 1) {
                            // Emit 'timeout' event
                            console.log('timeout');
                        }
                        return prevTime - 1;
                    });
                }
            }, 1000);
            return () => clearTimeout(timer);
        }
    }, [time, freeze, gap]);

    const reset = () => {
        setTime(availableTime);
    };

    return <span>{sec2time(time)}</span>;
};

export default Countdown;