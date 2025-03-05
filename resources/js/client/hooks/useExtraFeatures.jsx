import { useState, useEffect, useCallback, useMemo } from 'react';
import { useSelector } from 'react-redux';
import { howler } from '../utils/howlerClient';
import _ from 'lodash';

const DEFAULT_DATA = {
  line_count_context: null,
  audioContext_played: false,
  audioContext_instance: null,
};

const useExtraFeatures = (item, onCountdownFreezeChange = () => {}) => {
  const [data, setData] = useState(_.cloneDeep({ ...DEFAULT_DATA }));
  const isConnected = useSelector((state) => state.auth.connection_state === 'connected');

  const extra = useCallback(() => {
    return _.fromPairs(
      _.map(
        _.merge(_.get(item, 'config.extra', []), _.get(item, 'config.sub-item.extra')), (value) => {
          let split = value.split(':');
          return split.length === 1 ? [split, true] : split;
        })
    )
  }, [item]);

  const getExtra = useCallback((name) => {
    return extra()[name] ?? false;
  }, [extra]);

  const contentStyle = useMemo(() => {
    const width = getExtra('width');
    return {
      ...(width ? { width } : {}),
      ...(getExtra('no_content') ? { display: 'none' } : {}),
      ...(getExtra('line_count') ? { transform: 'translateX(26px)' } : {}),
    };
  }, [getExtra]);  

  const resetPluginData = useCallback(() => {
    setData((prevData) => ({ ...prevData, ...DEFAULT_DATA }));
  }, []);

  const lineCount = useCallback(
    _.debounce(() => {
      const line_count = getExtra('line_count');
      const refContent = document.querySelector('.content');

      if (line_count && refContent) {
        const context = { items: [] };
        let i = 1;
        for (const el of refContent.children) {
          context.items.push({
            height: `${el.clientHeight}px`,
            color: i % Number(line_count) === 0 ? '#000' : '#9d9b9b',
          });
          i++;
        }
        setData((prevData) => ({ ...prevData, line_count_context: context }));
      } else {
        setData((prevData) => ({ ...prevData, line_count_context: null }));
      }
    }, 300),
    [getExtra]
  );

  const audio = useCallback(
    (readOf = 'item') => {
      const hasAudio = getExtra('audio');
      if (hasAudio) {
        onCountdownFreezeChange(true);
        const audioContextInstance = howler.getInstance(item[readOf]?.id);

        if (!audioContextInstance) {
          console.warn(`Instance of howler with id ${item[readOf]?.id} not found!.`);
          onCountdownFreezeChange(false);
          return;
        }

        howler.pauseAll();

        if (isConnected) {
          const halfRemainingTime = Math.floor(parseInt(item['remaining_time']) / 2);

          if (getExtra('time_audio_split') && halfRemainingTime > 3) {
            onCountdownFreezeChange(false);
            setData((prevData) => ({ ...prevData, audioContext_played: false }));

            setTimeout(() => {
              onCountdownFreezeChange(true);
              setData((prevData) => ({ ...prevData, audioContext_played: true }));

              audioContextInstance.play().then(() => {
                onCountdownFreezeChange(false);
                setData((prevData) => ({ ...prevData, audioContext_played: false }));
              });
            }, halfRemainingTime * 1000);
          } else {
            setData((prevData) => ({ ...prevData, audioContext_played: true }));

            audioContextInstance.play().then(() => {
              onCountdownFreezeChange(false);
              setData((prevData) => ({ ...prevData, audioContext_played: false }));
            });
          }
        }
      }
    },
    [getExtra, howler, isConnected, item, onCountdownFreezeChange]
  );

  const bootPlugins = () => {
    lineCount()
    audio()
  }

  useEffect(() => {
    if (!isConnected && data.audioContext_played) {
      data.audioContext_instance?.pause();
    } else if (data.audioContext_played) {
      data.audioContext_instance?.play();
    }
  }, [isConnected, data.audioContext_played, data.audioContext_instance]);

  useEffect(() => {
    lineCount();
    audio();
  }, [item, lineCount, audio]);

  return { 
    resetPluginData, 
    getExtra, 
    lineCount, 
    audio,
    bootPlugins,
    audioContext_played : data.audioContext_played, 
    audioContext_instance: data.audioContext_instance, 
    line_count_context : data.line_count_context,
    contentStyle
  };
};

export default useExtraFeatures;
