import { useEffect, useRef, useState, forwardRef, useImperativeHandle } from 'react'

const Countdown = forwardRef(({ availableTime, gap = 5, freeze = false, onEvery, onTimeout }, ref) => {
    const [time, setTime] = useState(availableTime)

    // Track values using refs for stable callbacks
    const freezeRef = useRef(freeze)
    const timeRef = useRef(time)
    const availableTimeRef = useRef(availableTime)
    const timeoutCalledRef = useRef(false) // Tambahkan ref untuk melacak apakah timeout sudah dipanggil

    useEffect(() => {
        freezeRef.current = freeze
    }, [freeze])

    useEffect(() => {
        timeRef.current = time
    }, [time])

    useEffect(() => {
        availableTimeRef.current = availableTime
        setTime(availableTime)
        timeoutCalledRef.current = false // Reset flag saat availableTime berubah
    }, [availableTime])

    // Expose reset() to parent via ref
    useImperativeHandle(ref, () => ({
        reset: () => {
            setTime(availableTimeRef.current)
            timeoutCalledRef.current = false // Reset flag saat reset dipanggil
        }
    }))

    // mm:ss formatter
    const timeFormatted = (() => {
        const pad = (num, size) => ('000' + num).slice(size * -1)
        const minutes = Math.floor(time / 60) % 60
        const seconds = time - minutes * 60
        return `${pad(minutes, 2)}:${pad(seconds, 2)}`
    })()

    useEffect(() => {
        let timeoutId

        const tick = () => {
            if (!freezeRef.current) {
                setTime(prev => {
                    if (prev > 0) {
                        const next = prev - 1;

                        if (next % gap === 0 && onEvery) {
                            setTimeout(() => onEvery(gap), 0);
                        }

                        // Cek apakah timeout sudah dipanggil sebelumnya
                        if (next <= 0 && onTimeout && !timeoutCalledRef.current) {
                            timeoutCalledRef.current = true; // Set flag bahwa timeout sudah dipanggil
                            setTimeout(() => onTimeout(), 0);
                        }

                        return next;
                    }
                    return prev;
                });
            }

            timeoutId = setTimeout(tick, 1000);
        }

        timeoutId = setTimeout(tick, 1000)

        return () => clearTimeout(timeoutId)
    }, [gap, onEvery, onTimeout])

    return <span>{timeFormatted}</span>
})

Countdown.displayName = 'Countdown'

export default Countdown
