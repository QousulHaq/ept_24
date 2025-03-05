import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Button, Row, Col, Tooltip, Popconfirm, Icon, message } from 'antd';
import { change_active, saveTime, next, getActiveSection, getActiveItem } from '../../slices/performSlice';
import ChevronLeftRoundedIcon from '@mui/icons-material/ChevronLeftRounded';
import ChevronRightRoundedIcon from '@mui/icons-material/ChevronRightRounded';
import AccessTimeRoundedIcon from '@mui/icons-material/AccessTimeRounded';

const ArrowNavigation = () => {
    const dispatch = useDispatch();

    const section = useSelector(getActiveSection);
    const item = useSelector(getActiveItem);

    const activeItemIndex = section && section.items ? section.items.findIndex(i => i.id === item.id) : -1;
    const hasPrev = activeItemIndex !== 0;
    const hasNext = section && section.items && section.items.length > 0 && section.items.length - 1 !== activeItemIndex;

    const prev = () => {
        if (hasPrev) {
            dispatch(change_active({ item: section.items[activeItemIndex - 1].id }));
        }
    };

    const nextItem = () => {
        if (hasNext) {
            dispatch(change_active({ item: section.items[activeItemIndex + 1].id }));
        }
    };

    const finishCurrentSection = (confirm) => {
        if (confirm) {
            const key = 'finishing-section';
            message.loading({ content: 'Please wait ⏲...', key });
            dispatch(saveTime({ gap: 86400 })).then(() => {
                dispatch(next());
                message.success({ content: 'Ok 👋', key });
            });
        } else {
            message.info('keep going 😃!');
        }
    };

    return (
        !section.item_duration && (
            <Row type="flex">
                <Col md={4} xs={6}>
                    <Button onClick={prev} block disabled={!hasPrev}>
                        <ChevronLeftRoundedIcon />
                    </Button>
                </Col>
                <Col md={4} xs={6}>
                    {hasNext ? (
                        <Button onClick={nextItem} block>
                            <ChevronRightRoundedIcon />
                        </Button>
                    ) : (
                        <Tooltip title="Finish" placement="top">
                            <Popconfirm
                                title="Are you sure want to finish this section now?"
                                okText="Yes"
                                cancelText="No"
                                onConfirm={() => finishCurrentSection(true)}
                                onCancel={() => finishCurrentSection(false)}
                            >
                                <Button block>
                                    <AccessTimeRoundedIcon />
                                </Button>
                            </Popconfirm>
                        </Tooltip>
                    )}
                </Col>
            </Row>
        )
    );
};

export default ArrowNavigation;