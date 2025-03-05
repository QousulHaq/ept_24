import React, { useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Drawer, Collapse, Row, Col, Button, Icon } from 'antd';
import { getSection, getActiveSection, getActiveItem, getItemDuration, getSectionItemsAnswered, change_active } from '../../slices/performSlice';
import CheckCircleOutlineRoundedIcon from '@mui/icons-material/CheckCircleOutlineRounded';

const ItemNavigation = ({ visible, onClose }) => {
    const dispatch = useDispatch();

    const sections = useSelector(getSection)
    const activeSection = useSelector(getActiveSection)
    const activeItem = useSelector(getActiveItem)
    const itemDuration = useSelector(getItemDuration)
    const sectionItemsAnswered = useSelector(getSectionItemsAnswered)

    useEffect(() => {
        if (activeSection !== null) {
            close();
        }
    }, [activeSection]);

    useEffect(() => {
        if (activeItem !== null) {
            close();
        }
    }, [activeItem]);

    const getAnsweredClass = (item) => {
        return item !== null && sectionItemsAnswered.includes(item.id) ? ['answered'] : [];
    };

    const getTypeButton = (item) => {
        return item.id !== activeItem.id ? 'default' : 'primary';
    };

    const changeActiveItem = (itemId) => {
        if (!itemDuration) {
            dispatch(change_active({ item: itemId }));
        }
    };

    const close = () => {
        onClose();
    };

    return (
        <Drawer
            open={visible && activeSection !== null && activeItem !== null}
            placement="right"
            closable={false}
            onClose={close}
        >
            <Collapse accordion activeKey={activeSection?.id} expandIconPosition="end" bordered={false}>
                {sections.map(section => (
                    <Collapse.Panel header={section.config.title} key={section.id} collapsible={section.id !== activeSection.id ? "disabled" : ""}>
                        <Row justify="space-between">
                            {section.items.map(item => (
                                <Col key={item.id} md={item.label.length > 4 ? 24 : 11} sm={24}>
                                    <Button
                                        block
                                        onClick={() => changeActiveItem(item.id)}
                                        className={getAnsweredClass(item).join(' ')}
                                        disabled={(itemDuration && item.id !== activeItem.id) || (section.id !== activeSection.id)}
                                        type={getTypeButton(item)}
                                    >
                                        {item.label} {getAnsweredClass(item).length > 0 && <CheckCircleOutlineRoundedIcon />}
                                    </Button>
                                </Col>
                            ))}
                        </Row>
                    </Collapse.Panel>
                ))}
            </Collapse>
        </Drawer>
    );
};

export default ItemNavigation;