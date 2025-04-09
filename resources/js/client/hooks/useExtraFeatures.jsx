import { useState, useEffect, useMemo, useCallback, useRef } from "react";
import { useSelector } from "react-redux";
import { howler } from "../utils/howlerClient";
import _ from "lodash";

// Default data
const DEFAULT_DATA = {
  line_count_context: null,
  audioContext_played: false,
  audioContext_instance: null,
};

const useExtraFeatures = (item, onCountdownFreezeChange) => {
  // Redux State
  const isConnected = useSelector((state) => state.auth.connection_state === "connected");

  // State
  const [lineCountContext, setLineCountContext] = useState(DEFAULT_DATA.line_count_context);
  const [audioContextPlayed, setAudioContextPlayed] = useState(DEFAULT_DATA.audioContext_played);
  const [audioContextInstance, setAudioContextInstance] = useState(DEFAULT_DATA.audioContext_instance);

  // Ref untuk content
  const contentRef = useRef(null);

  // 🔹 Computed: extra (memproses item config)
  const extra = useMemo(() => {
    if (!item) return {};
    return _.fromPairs(
      _.map(
        _.merge(_.get(item, "config.extra", []), _.get(item, "config.sub-item.extra")),
        (value) => {
          const split = value.split(":");
          return split.length === 1 ? [split, true] : split;
        }
      )
    );
  }, [item]);

  // 🔹 Computed: contentStyle
  const contentStyle = useMemo(() => {
    if (!item) return {};

    return {
      ...(extra.width ? { width: extra.width } : {}),
      ...(extra.no_content ? { display: "none" } : {}),
      ...(extra.line_count ? { transform: "translateX(26px)" } : {}),
    };
  }, [extra]);

  // 🔹 Watch: isConnected (Pause/Play audio berdasarkan koneksi)
  useEffect(() => {
    if (!isConnected && audioContextPlayed) {
      audioContextInstance?.pause();
    } else if (audioContextPlayed) {
      audioContextInstance?.play();
    }
  }, [isConnected, audioContextPlayed, audioContextInstance]);

  // 🔹 Reset Data
  const resetPluginData = useCallback(() => {
    setLineCountContext(DEFAULT_DATA.line_count_context);
    setAudioContextPlayed(DEFAULT_DATA.audioContext_played);
    setAudioContextInstance(DEFAULT_DATA.audioContext_instance);
  }, []);

  // 🔹 Boot Plugins
  const bootPlugins = useCallback(() => {
    lineCount();
    audio();
  }, [lineCount, audio]);

  // 🔹 Get Extra Config
  const getExtra = useCallback(
    (name) => {
      return extra[name] ?? false;
    },
    [extra]
  );

  // 🔹 Line Count
  const lineCount = useCallback(
    _.debounce(() => {
      const line_count = getExtra("line_count");
      const refContent = contentRef.current;

      if (line_count && refContent) {
        const context = { items: [] };

        let i = 1;
        for (const el of refContent.children[0]?.children || []) {
          context.items.push({
            height: `${el.clientHeight}px`,
            color: i % Number(line_count) === 0 ? "#000" : "#9d9b9b",
          });
          i++;
        }

        setLineCountContext(context);
      } else {
        setLineCountContext(null);
      }
    }, 300),
    [getExtra]
  );

  // 🔹 Audio Control
  const audio = useCallback(async (readOf = "item") => {
    console.log("Fungsi audio() dipanggil...");

    const hasAudio = getExtra("audio");
    if (!hasAudio) return;

    onCountdownFreezeChange(true);
    const instance = howler?.getInstance(item?.id);

    if (!instance) {
      console.warn(`Instance of howler with id ${item?.id} not found!`);
      onCountdownFreezeChange(false);
      return;
    }

    howler.pauseAll();
    setAudioContextInstance(instance);

    if (isConnected) {
      let halfRemainingTime = Math.floor(parseInt(item?.remaining_time || 0) / 2);

      if (getExtra("time_audio_split") && halfRemainingTime > 3) {
        console.log("Menunda pemutaran audio hingga setengah waktu tersisa...");
        onCountdownFreezeChange(false);
        setAudioContextPlayed(false);

        setTimeout(async () => {
          console.log("Memainkan audio setelah jeda...");
          onCountdownFreezeChange(true);
          setAudioContextPlayed(true);
          await instance.play();
          console.log("Audio selesai setelah timeout, countdown kembali berjalan.");
          setAudioContextPlayed(false);
          onCountdownFreezeChange(false);
        }, halfRemainingTime * 1000);
      } else {
        setAudioContextPlayed(true);
        console.log("Memainkan audio langsung...");
        await instance.play();
        console.log("Audio selesai, countdown kembali berjalan.");
        setAudioContextPlayed(false);
        onCountdownFreezeChange(false);
      }
    }
  }, [getExtra, isConnected, item, howler, onCountdownFreezeChange]);


  return {
    contentRef,
    contentStyle,
    extra,
    bootPlugins,
    resetPluginData,
    getExtra,
    lineCount,
    audio,
    audioContext_played: audioContextPlayed,
    audioContext_instance: audioContextInstance,
    line_count_context: lineCountContext,
  };
};

export default useExtraFeatures;
