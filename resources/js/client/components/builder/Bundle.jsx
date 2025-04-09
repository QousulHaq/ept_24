// Bundle.jsx
import React, { useEffect, useRef, useState } from 'react';
import { Row, Col, Divider, Button } from 'antd';
import { EditorContent, useEditor } from '@tiptap/react';
import { useSelector } from 'react-redux';
import { extensions } from './tiptapExtensions'; // move the TipTap extensions here
import _ from 'lodash';

import MusicIcon from './part/MusicIcon';
import ArrowNavigation from '../performs/ArrowNavigation';

const Bundle = ({ item, children }) => {
  const activeItemId = useSelector((state) => state.perform.active.item);
  const section = useSelector((state) => state.perform.activeSection);

  const childrenWrapperRef = useRef(null);
  const activeDividerRef = useRef({});

  const [subContentEditor, setSubContentEditor] = useState(null);

  const contentEditor = useEditor({
    extensions,
    content: item?.content?.replaceAll(' ', '\u00a0'),
    editable: false,
  });

  // Utility Functions
  const getExtra = (key) => item?.extras?.[key] ?? '';

  const groupLabelBy = () => {
    const sign = getExtra('group_by') || '';
    return sign.includes(',') ? sign.split(',') : [sign];
  };

  const isGroupParent = (label) => groupLabelBy().some((sign) => _.startsWith(label, sign));

  const groupedItems = (() => {
    const groupByLabel = groupLabelBy();
    const items = section?.items ?? [];
    const activeItemIndex = items.findIndex((i) => i.id === item.id);
    const results = [];

    for (let i = activeItemIndex; i < items.length; i++) {
      if (isGroupParent(items[i].label) && i !== activeItemIndex) break;
      results.push(items[i]);
    }

    if (!isGroupParent(item.label)) {
      let i = activeItemIndex;
      do {
        i--;
        results.push(items[i]);
      } while (!isGroupParent(items[i]?.label));
    }

    return results.sort((a, b) => a.order - b.order);
  })();

  const itemParent = groupedItems.find((i) => isGroupParent(i.label));
  const subItems = groupedItems.filter((i) => !isGroupParent(i.label));
  const disablePlugin = itemParent?.id !== item.id;

  const note = subItems.length
    ? subItems.length === 1
      ? `numbers : ${subItems[0].label}`
      : `numbers : ${subItems[0].label} - ${subItems[subItems.length - 1].label}`
    : '';

  const resolveValue = (subItem) => {
    const attempt = (subItem?.attempts ?? []).find(
      (a) => a.attempt_number === section?.attempts
    );
    return attempt?.answer ?? null;
  };

  const scrollToActive = () => {
    const dividerRef = activeDividerRef.current?.[activeItemId];
    if (dividerRef && childrenWrapperRef.current) {
      childrenWrapperRef.current.scrollTo({
        top: dividerRef.offsetTop,
        left: 0,
        behavior: 'smooth',
      });
    }
  };

  useEffect(() => {
    if (!disablePlugin) {
      scrollToActive();
    }
    const subContent = ((!isNaN(item.label) ? item.label + '. ' : '') + item.sub_content)?.replaceAll(' ', '\u00a0');
    const editor = useEditor({
      extensions,
      content: subContent,
      editable: false,
    });
    setSubContentEditor(editor);
  }, [item]);

  return (
    <Row justify="space-between" className="overflow-hidden">
      <Col md={11} sm={24}>
        {getExtra('audio') && <MusicIcon played={false} width={300} height={300} />}
        {contentEditor && <EditorContent editor={contentEditor} className="prose" />}
        <br />
        <span className="text-sm text-gray-700">{note}</span>
      </Col>

      <Divider type="vertical" orientation="center" style={{ height: 'auto' }} />

      <Col
        md={11}
        sm={24}
        className="overflow-y-auto max-h-[65vh] pr-2"
        ref={childrenWrapperRef}
      >
        {subItems.map((subItem, i) => (
          <div key={i}>
            <Divider
              type="horizontal"
              ref={(el) => (activeDividerRef.current[subItem.id] = el)}
            />
            <Button
              block
              type={activeItemId === subItem.id ? 'primary' : 'dashed'}
              danger={activeItemId === subItem.id}
              className="mb-2"
            >
              <b>{subItem.label}</b>
            </Button>
            {children?.({ item: subItem, value: resolveValue(subItem) })}
          </div>
        ))}
        <Divider type="horizontal" />
        <ArrowNavigation />
      </Col>
    </Row>
  );
};

export default Bundle;