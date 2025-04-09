// hooks/usePlugins.js
import { useCallback, useRef, useState } from 'react'
import { useSelector } from "react-redux";
import howler from "../utils/howlerClient";
import { useExtra } from './useExtra'

const DEFAULT_DATA = {
    lineCountContext: null,
    audioContextPlayed: false,
    audioContextInstance: null
}

export function usePlugins({ item, onCountdownFreezeChange }) {
    const isConnected = useSelector((state) => state.auth.connection_state === "connected");
    const [pluginData, setPluginData] = useState(DEFAULT_DATA)
    const contentRef = useRef()
    const { getExtra } = useExtra(item)

    const resetPluginData = useCallback(() => {
        setPluginData(DEFAULT_DATA)
    }, [])

    const lineCount = useCallback(() => {
        const line_count = getExtra('line_count')
        if (line_count && contentRef.current) {
            const editorRoot = contentRef.current?.querySelector('.ProseMirror')?.children[0]
            const items = []

            if (editorRoot?.children) {
                let i = 1
                for (const el of editorRoot.children) {
                    items.push({
                        height: el.clientHeight + 'px',
                        color: i % Number(line_count) === 0 ? '#000' : '#9d9b9b',
                    })
                    i++
                }
            }

            setPluginData(prev => ({ ...prev, lineCountContext: { items } }))
        } else {
            setPluginData(prev => ({ ...prev, lineCountContext: null }))
        }
    }, [item])

    const audio = useCallback(() => {
        const hasAudio = getExtra('audio')
        console.log(`Item ID: ${item.id}, Has Audio: ${hasAudio}`)
        if (!hasAudio) return

        onCountdownFreezeChange(true)
        const instance = howler.getInstance(item.id)
        console.log(`Audio instance for item ${item.id}:`, instance)

        if (!instance) {
            console.warn(`howler instance for ${item.id} not found`)
            onCountdownFreezeChange(false)
            return
        }

        howler.pauseAll()
        
        if (isConnected) {
            let half = Math.floor(parseInt(item.remaining_time) / 2)
            if (getExtra('time_audio_split') && half > 3) {
                // Remove this line that unfreezes the countdown
                // onCountdownFreezeChange(false)
                setPluginData(prev => ({ ...prev, audioContextPlayed: false }))

                setTimeout(() => {
                    onCountdownFreezeChange(true)
                    setPluginData(prev => ({ ...prev, audioContextPlayed: true }))
                    instance.play().then(() => {
                        onCountdownFreezeChange(false)
                        setPluginData(prev => ({ ...prev, audioContextPlayed: false }))
                    })
                }, half * 1000)
            } else {
                setPluginData(prev => ({ ...prev, audioContextPlayed: true }))
                instance.play().then(() => {
                    onCountdownFreezeChange(false)
                    setPluginData(prev => ({ ...prev, audioContextPlayed: false }))
                })
            }
        }
    }, [item, isConnected])

    const bootPlugins = useCallback(() => {
        lineCount()
        audio()
    }, [lineCount, audio])

    return {
        pluginData,
        contentRef,
        getExtra,
        resetPluginData,
        bootPlugins
    }
}
