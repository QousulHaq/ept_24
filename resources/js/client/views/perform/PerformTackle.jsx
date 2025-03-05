import React, { useState, useEffect } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import ItemNavigation from '../../components/performs/ItemNavigation';
import QuizBuilder from '../../components/performs/QuizBuilder';
import Countdown from '../../components/performs/Countdown';
import { saveAnswer, saveTime, next, change_active } from '../../slices/performSlice';
import { getActiveSection, getActiveItem, getActiveAttempt, getItemDuration } from '../../slices/performSlice';
import { message, Row, Col, Tooltip, Button, Layout } from 'antd';
import FormatListBulletedIcon from '@mui/icons-material/FormatListBulleted';

const PerformTackle = () => {
  const [visible, setVisible] = useState(false);
  const [disabled, setDisabled] = useState(false);
  const [countdown, setCountdown] = useState({
    gap: 5,
    freeze: false,
    lock: false,
    watchingId: null,
    availableTime: Infinity,
  });

  const isConnected = useSelector(state => state.auth.connection_state === 'connected');

  const section = useSelector(getActiveSection);
  const item = useSelector(getActiveItem);
  const attempt = useSelector(getActiveAttempt);
  const itemDuration = useSelector(getItemDuration);

  const dispatch = useDispatch();

  useEffect(() => {
    processIsConnected();
  }, [isConnected]);

  useEffect(() => {
    if (!itemDuration) countdownParamsProcess(section);
  }, [section]);

  useEffect(() => {
    if (itemDuration) countdownParamsProcess(item);
  }, [item]);

  const processIsConnected = () => {
    if (!isConnected) {
      setCountdown(prev => ({ ...prev, lock: prev.freeze ? true : prev.lock }));
      if (!countdown.freeze) requestChangeFreeze(true);
    } else {
      setCountdown(prev => ({ ...prev, lock: false }));
      if (countdown.lock) requestChangeFreeze(false);
    }
  };

  const processChangeAnswer = ({ value, itemId = null }) => {
    switch (item.type) {
      case 'multi_choice_single':
      case 'bundle':
        dispatch(saveAnswer({ itemAnswerId: value, itemId }));
        break;
      default:
        break;
    }
  };

  const showDrawer = () => setVisible(true);
  const onClose = () => setVisible(false);
  const requestChangeFreeze = value => setCountdown(prev => ({ ...prev, freeze: value }));

  const countdownParamsProcess = ({ id = null, remaining_time = null }) => {
    if (id === null || remaining_time === null) {
      console.warn(`countdownParamsProcess: id or remaining_time is null\n id = ${id}\n remaining_time ${remaining_time}`);
      return null;
    }

    const needResetCountdown = countdown.watchingId !== id;
    setCountdown(prev => ({
      ...prev,
      watchingId: id,
      availableTime: remaining_time,
    }));

    if (needResetCountdown) {
      // Reset countdown logic
      if (!itemDuration) requestChangeFreeze(false);
    }
  };

  const handleEvery = () => {
    dispatch(saveTime({ gap: countdown.gap }));
  };

  const handleTimeout = () => {
    setDisabled(true);
    dispatch(saveTime({ gap: countdown.gap })).then(() => {
      dispatch(next()).then(() => setDisabled(false));
    });
  };

  const finishCurrentSection = (confirm) => {
    if (confirm) {
      if (0 < countdown.availableTime && countdown.availableTime < Infinity) {
        const key = 'finishing-section';
        message.loading({ content: 'Please wait ⏲...', key });
        dispatch(saveTime({ gap: countdown.availableTime + 10 })).then(() => {
          dispatch(next());
          message.success({ content: 'Okay 👋', key });
        });
      }
    } else {
      message.info('keep going 😃!');
    }
  };

  useEffect(() => {
    const tickAble = itemDuration ? item : section;
    if (tickAble) countdownParamsProcess(tickAble);

    setTimeout(() => {
      window.scrollTo({ top: document.querySelector('body > section')?.offsetTop ?? 0, left: 0, behavior: 'smooth' });
    }, 1000);
  }, [item, section, itemDuration]);

  return (
    <div>
      {item !== null && (
        <>
          <Row type="flex" justify="end">
            <Col md={item.label.length > 4 ? 5 : 4} xs={6}>
              <Tooltip title={<span>current number</span>} placement="top">
                <Button disabled block style={{ borderColor: 'firebrick' }}>
                  <b>{item.label}</b>
                </Button>
              </Tooltip>
            </Col>
            <Col md={2} xs={6}>
              <Tooltip title={<span>time</span>} placement="top">
                <Button disabled block>
                  <Countdown
                    availableTime={countdown.availableTime}
                    freeze={countdown.freeze}
                    onTimeout={handleTimeout}
                    onEvery={handleEvery}
                  />
                </Button>
              </Tooltip>
            </Col>
            <Col md={2} xs={6}>
              <Button onClick={showDrawer} block>
                <FormatListBulletedIcon />
              </Button>
            </Col>
          </Row>
          <ItemNavigation visible={visible} onClose={onClose} />
          <Layout className="layout" style={{ margin: '2.4em 0' }}>
            <Layout.Content>
              <div style={{ background: '#fff', padding: '24px', minHeight: '280px' }}>
                {item !== null ? (
                  <QuizBuilder
                    item={item}
                    value={attempt.answer}
                    disabled={disabled}
                    onChange={processChangeAnswer}
                    onCountdownFreezeChange={requestChangeFreeze}
                  />
                ) : (
                  <div>
                    <b>currently you're not connected to the server.</b>
                    the question will appear while you connected to server!
                  </div>
                )}
              </div>
            </Layout.Content>
          </Layout>
        </>
      )}
    </div>
  );
};

export default PerformTackle;