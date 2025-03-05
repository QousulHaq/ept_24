import React, { useEffect, useState } from 'react';
import { Row, Col, Divider } from 'antd';

import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Underline from '@tiptap/extension-underline';
import Subscript from '@tiptap/extension-subscript';
import Superscript from '@tiptap/extension-superscript';
import TaskList from '@tiptap/extension-task-list';
import TaskItem from '@tiptap/extension-task-item';

import LineCount from './part/LineCount';
import MusicIcon from "./part/MusicIcon";
import ArrowNavigation from '../performs/ArrowNavigation';
import useExtraFeatures from '../../hooks/useExtraFeatures';

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

const MultiChoiceSingle = ({ children, item, disablePlugin = false, musicIcon = { width: 300, height: 200, hidden: false } }) => {
  const [tiptap, setTiptap] = useState({ content: null, subContent: null });
  const {
    getExtra,
    audioContext_played,
    audioContext_instance,
    line_count_context,
    resetPluginData,
    bootPlugins,
  } = useExtraFeatures(item);

  const contentEditor = useEditor({
    extensions,
    content: item.content?.replaceAll(' ', '&nbsp;'),
    editable: false,
  });

  const subContentEditor = useEditor({
    extensions,
    content: ((!isNaN(item.label) ? item.label + '. ' : '') + item.sub_content)?.replaceAll(' ', '&nbsp;'),
    editable: false,
  });

  useEffect(() => {
    bootMultiChoiceSingle();
  }, [item, disablePlugin]);

  const bootMultiChoiceSingle = () => {
    if (tiptap.content !== null) {
      tiptap.content.commands.setContent(item.content?.replaceAll(' ', '&nbsp;'));
    } else {
      setTiptap(prev => ({ ...prev, content : contentEditor }));
    }

    if (tiptap.subContent !== null) {
      tiptap.subContent.commands.setContent(((!isNaN(item.label) ? item.label + '. ' : '') + item.sub_content)?.replaceAll(' ', '&nbsp;'));
    } else {
      setTiptap(prev => ({ ...prev, subContent: subContentEditor }));
    }

    if (disablePlugin) {
      resetPluginData();
    } else {
      bootPlugins();
    }
  };

  useEffect(() => {
    if (item.content) {
      setTiptap(prev => ({ ...prev, content: contentEditor }));
    }

    if (item.sub_content) {
      setTiptap(prev => ({ ...prev, subContent: contentEditor }));
    }

    bootMultiChoiceSingle();
  }, [item]);

  return (
    <Row type="flex" justify="space-between">
      <Col md={item.sub_content !== null ? 14 : 24} sm={24} style={{ overflowX: 'auto' }}>
        {!musicIcon.hidden && getExtra('audio') && <MusicIcon played={audioContext_played} width={musicIcon.width} height={musicIcon.height} />}
        {line_count_context && <LineCount leap={getExtra('line_count')} context={line_count_context} />}
        {tiptap.content && <EditorContent className={`editor__content ${getExtra('alphabet_counter_underline') ? 'alphabet_counter' : ''}`} editor={tiptap.content} />}
        <Divider type="horizontal" />
        {item.sub_content === null && <div><Divider type="horizontal" /><ArrowNavigation /></div>}
      </Col>
      {item.sub_content !== null && <Divider type="vertical" orientation="center" style={{ height: 'auto' }} />}
      {item.sub_content !== null && (
        <Col md={9} sm={24}>
          {tiptap.subContent && <EditorContent className="editor__content" editor={tiptap.subContent} />}
          <Divider type="horizontal" />
          <div>{/* Slot for other components */}</div>
          <div>
            <Divider type="horizontal" />
            {children}
            <ArrowNavigation />
          </div>
        </Col>
      )}
    </Row>
  );
};

export default MultiChoiceSingle;