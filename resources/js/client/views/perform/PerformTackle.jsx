import React, { useEffect, useState, useRef, useContext } from 'react';
import { Row, Col, Tooltip, Button, Layout, message } from 'antd';
import { ProfileOutlined } from '@ant-design/icons';

import ItemNavigation from '../../components/performs/ItemNavigation';
import QuizBuilder from '../../components/performs/QuizBuilder';
import Countdown from '../../components/performs/Countdown';

// Assuming you use Redux or Context for state like Vuex
import { useSelector, useDispatch } from 'react-redux';
import { saveAnswer, saveTime, next as nextItemOrSection, change_active } from '../../slices/performSlice'
import { getActiveItem, getActiveSection, getActiveAttempt, getItemDuration } from '../../slices/performSlice';

const { Content } = Layout;

const PerformTackle = () => {
  // Local state (similar to Vue's `data`)
  const [visible, setVisible] = useState(false);
  const [disabled, setDisabled] = useState(false);
  const [countdown, setCountdown] = useState({
    gap: 5,
    freeze: false,
    lock: false,
    watchingId: null,
    availableTime: Infinity
  });

  const countdownRef = useRef(null);

  // Vuex-style selectors
  const isConnected = useSelector((state) => state.auth.connection_state === 'connected');
  const section = useSelector(getActiveSection);
  const item = useSelector(getActiveItem);
  const attempt = useSelector(getActiveAttempt);
  const itemDuration = useSelector(getItemDuration);

  const dispatch = useDispatch();

  const processIsConnected = () => {
    if (!isConnected) {
      if (countdown.freeze) {
        setCountdown(prev => ({ ...prev, lock: true }));
      } else {
        requestChangeFreeze(true);
      }
    } else {
      if (countdown.lock) {
        setCountdown(prev => ({ ...prev, lock: false }));
      } else {
        requestChangeFreeze(false);
      }
    }
  };

  const requestChangeFreeze = (value) => {
    setCountdown(prev => ({ ...prev, freeze: value }));
  };

  const countdownParamsProcess = ({ id = null, remaining_time = null }) => {
    if (id === null || remaining_time === null) {
      console.warn(`countdownParamsProcess : id or remaining_time is null\n id = ${id}\n remaining_time ${remaining_time}`);
      return;
    }

    const needResetCountdown = countdown.watchingId !== id;

    setCountdown(prev => ({
      ...prev,
      watchingId: id,
      availableTime: remaining_time
    }));

    if (needResetCountdown && countdownRef.current) {
      countdownRef.current.reset();
      if (!itemDuration) requestChangeFreeze(false);
    }
  };

  const processChangeAnswer = ({ value, itemId = null }) => {
    if (item?.type === 'multi_choice_single' || item?.type === 'bundle') {
      dispatch(saveAnswer({ itemAnswerId: value, itemId }));
    }
  };

  const showDrawer = () => setVisible(true);
  const onClose = () => setVisible(false);

  const every = () => {
    dispatch(saveTime({ gap: countdown.gap }));
  };

  const timeout = () => {
    if (disabled) return; // Prevent multiple calls
    setDisabled(true);
    
    // Gunakan async/await untuk lebih jelas
    (async () => {
      try {
        await dispatch(saveTime({ gap: countdown.gap }));
        await dispatch(nextItemOrSection({})); // Kirim objek kosong sebagai payload
      } finally {
        setDisabled(false);
      }
    })();
  };

  const finishCurrentSection = (confirm) => {
    if (confirm) {
      if (0 < countdown.availableTime && countdown.availableTime < Infinity) {
        const key = 'finishing-section';
        message.loading({ content: 'Please wait ⏲...', key });
        dispatch(saveTime({ gap: countdown.availableTime + 10 })).then(() => {
          dispatch(nextItemOrSection({}));
          message.success({ content: 'Okay 👋', key });
        });
      }
    } else {
      message.info('keep going 😃!');
    }
  };

  // When isConnected changes
  useEffect(() => {
    processIsConnected();
  }, [isConnected]);

  // When section changes
  useEffect(() => {
    if (!itemDuration && section) {
      countdownParamsProcess(section);
    }
  }, [section]);

  // When item changes
  useEffect(() => {
    if (itemDuration && item) {
      countdownParamsProcess(item);
    }
  }, [item]);

  useEffect(() => {
    processIsConnected();

    const tickAble = itemDuration ? item : section;
    if (tickAble) countdownParamsProcess(tickAble);

    setTimeout(() => {
      const el = document.querySelector('body > section');
      if (el) el.scroll({ top: el.offsetTop ?? 0, left: 0, behavior: 'smooth' });
    }, 1000);
  }, []);
  
  return item !== null && (
    <>
      <Row justify="end">
        <Col md={item.label.length > 4 ? 5 : 4} xs={6}>
          <Tooltip title="current number">
            <Button disabled block style={{ borderColor: 'firebrick' }}>
              <b>{item.label}</b>
            </Button>
          </Tooltip>
        </Col>
        <Col md={2} xs={6}>
          <Tooltip title="time">
            <Button disabled block>
              <Countdown
                ref={countdownRef}
                availableTime={countdown.availableTime}
                freeze={countdown.freeze}
                gap={countdown.gap}
                onTimeout={timeout}
                onEvery={every}
              />
            </Button>
          </Tooltip>
        </Col>
        <Col md={2} xs={6}>
          <Button onClick={showDrawer} block>
            <ProfileOutlined />
          </Button>
        </Col>
      </Row>

      <ItemNavigation visible={visible} onClose={onClose} />

      <Layout style={{ margin: '2.4em 0' }}>
        <Content>
          <div style={{ background: '#fff', padding: '24px', minHeight: '280px' }}>
            {isConnected ? (
              <QuizBuilder
                item={item}
                value={attempt.answer}
                disabled={disabled}
                onChange={processChangeAnswer}
                onCountdownFreezeChange={requestChangeFreeze}
              />
            ) : (
              <div>
                <b>currently you're not connected to the server.</b> the question will appear while you’re connected to server!
              </div>
            )}
          </div>
        </Content>
      </Layout>
    </>
  );
}

export default PerformTackle;