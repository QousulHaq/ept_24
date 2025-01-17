import React, { useState } from 'react'

import { useEditor, EditorContent } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'

import "../../../../../public/css/back-office/multiple-choices.css"

const Intros = ({ content_data, question_code, handleIntroChange }) => {

    const editor = useEditor({
        extensions: [StarterKit, Underline],
        content: content_data,
        onUpdate: ({ editor }) => {
            const html_content = editor.getHTML();
            handleIntroChange(html_content)
        }
    })

    return (
        <div className="multiple-choices-card mb-3">
            <div className="multiple-choice-header">
                <div className="title-section">
                    <h5>Introduction {question_code}</h5>
                    <hr className='stroke-in-question' />
                    <div className="text-tools mt-2">
                        <button
                            onClick={() => editor.chain().focus().toggleHeading({ level: 5 }).run()}
                            style={editor.isActive('heading', { level: 5 }) ? { backgroundColor: "#2B7FD4", color: "white" } : {}}
                        >
                            <b style={{ fontSize: "x-small" }}>H5</b>
                        </button>
                        <button
                            onClick={() => editor.chain().focus().toggleBold().run()}
                            style={editor.isActive('bold') ? { backgroundColor: "#2B7FD4", color: "white" } : {}}
                        >
                            <i className="fas fa-solid fa-bold"></i>
                        </button>
                        <button
                            onClick={() => editor.chain().focus().toggleItalic().run()}
                            style={editor.isActive('italic') ? { backgroundColor: "#2B7FD4", color: "white" } : {}}
                        >
                            <i className="fas fa-solid fa-italic"></i>
                        </button>
                        <button
                            onClick={() => editor.chain().focus().toggleStrike().run()}
                            style={editor.isActive('strike') ? { backgroundColor: "#2B7FD4", color: "white" } : {}}
                        >
                            <i className="fas fa-solid fa-strikethrough"></i>
                        </button>
                        <button
                            onClick={() => editor.chain().focus().toggleUnderline().run()}
                            style={editor.isActive('underline') ? { backgroundColor: "#2B7FD4", color: "white" } : {}}
                        >
                            <i className="fas fa-solid fa-underline"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div className="editor-input my-3">
                <EditorContent editor={editor} />
            </div>
        </div>
    )
}

export default Intros