import React, { useEffect, useRef, useState } from 'react';
import { useSelector } from 'react-redux';

import MusicIcon from './part/MusicIcon';
import ArrowNavigation from '../performs/ArrowNavigation';
import useExtraFeatures from '../../hooks/useExtraFeatures';
import { getActiveSection } from '../../slices/performSlice';

import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Underline from '@tiptap/extension-underline';
import Subscript from '@tiptap/extension-subscript';
import Superscript from '@tiptap/extension-superscript';
import TaskList from '@tiptap/extension-task-list';
import TaskItem from '@tiptap/extension-task-item';

import { Row, Col, Divider, Button } from 'antd';
import _ from 'lodash';

const extensions = [
    StarterKit.configure({
        heading: {
            levels: [1, 2, 3, 4, 5],
        },
    }),
    Link,
    Underline,
    Subscript,
    Superscript,
    TaskList,
    TaskItem.configure({
        nested: true, // Mengizinkan daftar tugas bersarang
    }),
];

const Bundle = ({ item, children }) => {
    const [tiptap, setTiptap] = useState({
        content: null,
        subContent: null,
    });

    const {
        getExtra,
        audioContext_played,
        audioContext_instance,
        line_count_context,
        resetPluginData,
        bootPlugins,
        contentStyle
    } = useExtraFeatures(item);

    const activeItemId = useSelector(state => state.perform.active.item);
    const section = useSelector(getActiveSection);
    const childrenWrapperRef = useRef(null);

    const groupLabelBy = () => {
        const sign = getExtra('group_by');
        return sign.includes(',') ? sign.split(',') : [sign];
    };

    const isGroupParent = (label) => {
        return groupLabelBy().some(sign => _.startsWith(label, sign));
    };

    const groupedItems = () => {
        const results = [];
        const items = section.items;
        const activeItemIndex = items.findIndex(i => i.id === item.id);

        for (let i = activeItemIndex; i < items.length; i++) {
            if (isGroupParent(items[i].label) && i !== activeItemIndex)
                break;
            results.push(items[i]);
        }
        if (!isGroupParent(item.label)) {
            let i = activeItemIndex;
            do {
                i--;
                results.push(items[i]);
            } while (!isGroupParent(items[i].label));
        }
        return results.sort((a, b) => a.order - b.order);
    };

    const itemParent = () => groupedItems().find(i => isGroupParent(i.label));
    const subItems = () => groupedItems().filter(i => !isGroupParent(i.label));
    const disablePlugin = () => itemParent()?.id !== item.id;

    const editor1 = useEditor({
        extensions,
        content: ((!isNaN(item.label) ? item.label + '. ' : '') + item['sub_content'])?.replaceAll(' ', '&nbsp;'),
        editable: false,
    })

    const editor2 = useEditor({
        extensions,
        content: item.content?.replaceAll(' ', '&nbsp;'),
        editable: false,
    })

    const editor3 = useEditor({
        extensions,
        content: item['sub_content']?.replaceAll(' ', '&nbsp;'),
        editable: false,
    })

    const note = () => {
        if (!subItems() || subItems().length === 0)
            return '';
        if (subItems().length === 1)
            return 'numbers : ' + subItems()[0].label;
        return 'numbers : ' + subItems()[0].label + ' - ' + subItems()[subItems().length - 1].label;
    };

    const bootBundle = () => {
        if (tiptap.content !== null) {
            tiptap.content.commands.setContent(item.content?.replaceAll(' ', '&nbsp;'));
        }
        if (tiptap.subContent !== null) {
            tiptap.subContent.commands.setContent(((!isNaN(item.label) ? item.label + '. ' : '') + item['sub_content'])?.replaceAll(' ', '&nbsp;'));
        } else {
            setTiptap(prevState => ({
                ...prevState,
                subContent: editor1,
            }));
        }
        disablePlugin() ? resetPluginData() : bootPlugins();
        scrollToActive();
    };

    const resolveValue = (item) => {
        return _.get(
            _.get(item, 'attempts', []).find(a => _.get(a, 'attempt_number', -1) === _.get(section, 'attempts')),
            'answer',
            null
        );
    };

    const scrollToActive = () => {
        let offsetTop = 0;
        if (childrenWrapperRef.current && childrenWrapperRef.current[`child-active-${activeItemId}`]) {
            offsetTop = childrenWrapperRef.current[`child-active-${activeItemId}`].offsetTop;
        }
        childrenWrapperRef.current.scroll({
            top: offsetTop,
            left: 0,
            behavior: 'smooth',
        });
    };

    useEffect(() => {
        if (item.content !== null) {
            setTiptap(prevState => ({
                ...prevState,
                content: editor2,
            }));
        }
        if (item.subContent !== null) {
            setTiptap(prevState => ({
                ...prevState,
                subContent: editor3,
            }));
        }
        bootBundle();
    }, [item]);

    return (
        <Row type="flex" justify="space-between" className="overflow-hidden">
            <Col md={11} sm={24}>
                {getExtra('audio') && <MusicIcon played={audioContext_played} width={300} height={300} />}
                {tiptap.content && <EditorContent editor={tiptap.content} className="editor__content" ref={contentRef} style={contentStyle} />}
                <br />
                <span>{note()}</span>
            </Col>
            <Divider type="vertical" orientation="center" style={{ height: 'auto' }} />
            <Col md={11} sm={24} style={{ overflowY: 'auto', maxHeight: '65vh' }} ref={childrenWrapperRef}>
                {subItems().map((subItem, i) => (
                    <div key={i}>
                        <Divider type="horizontal" ref={`child-active-${subItem.id}`} />
                        <Button block type={activeItemId === subItem.id ? 'danger' : 'dashed'}><b>{subItem.label}</b></Button>
                        {children(resolveValue(subItem))}
                    </div>
                ))}
                <Divider type="horizontal" />
                <ArrowNavigation />
            </Col>
        </Row>
    );
};

export default Bundle;