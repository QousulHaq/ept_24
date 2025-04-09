import React, { useEffect, useRef, useState } from 'react'
import { useEditor, EditorContent } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Underline from '@tiptap/extension-underline'
import MusicIcon from './part/MusicIcon'
import LineCount from './part/LineCount'
import ArrowNavigation from '../performs/ArrowNavigation'
import { usePlugins } from '../../hooks/usePlugins'
import classNames from 'classnames'

const extensions = [StarterKit, Link, Underline]

const MultiChoiceSingle = ({ item, disablePlugin = false, musicIcon = { width: 300, height: 200, hidden: false }, onCountdownFreezeChange, children }) => {

  const {
    pluginData,
    contentRef,
    getExtra,
    resetPluginData,
    bootPlugins
  } = usePlugins({ item, onCountdownFreezeChange })

  const [audioPlayed, setAudioPlayed] = useState(false)
  const contentEditor = useEditor({
    extensions,
    editable: false,
    content: item.content?.replaceAll(' ', '\u00A0') || '',
  })
  const subContentEditor = useEditor({
    extensions,
    editable: false,
    content: item.sub_content
      ? ((!isNaN(item.label) ? `${item.label}. ` : '') + item.sub_content)?.replaceAll(' ', '\u00A0')
      : '',
  })

  const lineCountContext = getExtra(item, 'line_count_context')

  useEffect(() => {
    if (contentEditor && item.content) {
      contentEditor.commands.setContent(item.content.replaceAll(' ', '\u00A0'))
    }
    if (subContentEditor && item.sub_content) {
      subContentEditor.commands.setContent(
        ((!isNaN(item.label) ? `${item.label}. ` : '') + item.sub_content)?.replaceAll(' ', '\u00A0')
      )
    }

    if (disablePlugin) {
      resetPluginData()
    } else {
      bootPlugins()
    }
  }, [item, disablePlugin])

  const hasSubContent = item.sub_content !== null

  return (
    <div className="flex flex-col md:flex-row justify-between">
      <div
        className={classNames(
          'px-2 overflow-x-auto',
          hasSubContent ? 'md:w-7/12' : 'w-full'
        )}
      >
        {!musicIcon.hidden && getExtra(item, 'audio') && (
          <MusicIcon played={audioPlayed} width={musicIcon.width} height={musicIcon.height} />
        )}

        {lineCountContext && (
          <LineCount leap={getExtra('line_count')} context={lineCountContext} />
        )}

        {contentEditor && (
          <EditorContent
            editor={contentEditor}
            className={classNames('prose', {
              'alphabet_counter': getExtra(item, 'alphabet_counter_underline'),
            })}
          />
        )}

        <hr className="my-4" />

        {!hasSubContent && (
          <>
            {children}
            <hr className="my-4" />
            <ArrowNavigation />
          </>
        )}
      </div>

      {hasSubContent && (
        <>
          <div className="mx-4 border-l h-auto hidden md:block" />
          <div className="md:w-4/12 w-full mt-4 md:mt-0">
            {subContentEditor && (
              <EditorContent editor={subContentEditor} className="prose" />
            )}
            <hr className="my-4" />
            {children}
            <hr className="my-4" />
            <ArrowNavigation />
          </div>
        </>
      )}
    </div>
  )
}

export default MultiChoiceSingle
