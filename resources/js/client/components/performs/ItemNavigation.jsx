// ItemNavigation.jsx
import React, { useEffect } from 'react';
import { Drawer, Collapse, Row, Col, Button } from 'antd';
import { CheckCircleOutlined } from '@ant-design/icons';
import { useSelector, useDispatch } from 'react-redux';
import { getSection, getActiveSection, getActiveItem, getItemDuration, getSectionItemsAnswered, change_active } from '../../slices/performSlice';

const { Panel } = Collapse;

const ItemNavigation = ({ visible, onClose }) => {
    const dispatch = useDispatch();

    const sections = useSelector(getSection)
    const activeSection = useSelector(getActiveSection)
    const activeItem = useSelector(getActiveItem)
    const itemDuration = useSelector(getItemDuration)
    const sectionItemsAnswered = useSelector(getSectionItemsAnswered)

    // Effect for watching activeSection/activeItem like Vue's watch
    useEffect(() => {
        if (activeSection && activeItem) {
            onClose?.();
        }
    }, [activeSection, activeItem]);

    const getAnsweredClass = (item) => {
        if (item && sectionItemsAnswered.includes(item.id)) {
            return 'answered';
        }
        return '';
    };

    const getTypeButton = (item) => {
        return item.id !== activeItem?.id ? 'default' : 'primary';
    };

    const changeActiveItem = (itemId) => {
        if (!itemDuration) {
            dispatch(change_active({ item: itemId }));
        }
    };

    // If null, don't render anything (v-if)
    if (!activeSection || !activeItem) return null;

    return (
        <Drawer
            placement="right"
            closable={false}
            open={visible}
            onClose={onClose}
            bodyStyle={{ padding: 0 }}
        >
            <Collapse
                accordion
                activeKey={activeSection.id}
                expandIconPosition="right"
                bordered={false}
            >
                {sections.map((section) => (
                    <Panel
                        header={section.config.title}
                        key={section.id}
                        disabled={section.id !== activeSection.id}
                    >
                        <Row justify="space-between">
                            {section.items.map((item) => (
                                <Col
                                    key={item.id}
                                    className="my-1"
                                    md={item.label.length > 4 ? 24 : 11}
                                    sm={24}
                                >
                                    <Button
                                        block
                                        onClick={() => changeActiveItem(item.id)}
                                        className={`w-full whitespace-normal text-base h-auto ${getAnsweredClass(item)}`}
                                        disabled={
                                            (itemDuration && item.id !== activeItem.id) ||
                                            (section.id !== activeSection.id)
                                        }
                                        type={getTypeButton(item)}
                                    >
                                        {item.label}
                                        {getAnsweredClass(item) && (
                                            <span className="ml-1">
                                                <CheckCircleOutlined />
                                            </span>
                                        )}
                                    </Button>
                                </Col>
                            ))}
                        </Row>
                    </Panel>
                ))}
            </Collapse>
        </Drawer>
    );
};


export default ItemNavigation;
