import React, { useState } from 'react'

import AudioForm from './AudioForm'

import { useEditor, EditorContent } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'

import "../../../../../public/css/back-office/multiple-choices.css"

const MultipleChoice = ({ content_data, number, isBundle = true, isAudio, handleContentChange, handleAnsContentChange, handleAnswerChange, handlePushAnswer, handleDeleteQuestion, handleDeleteAnswer, handleAudioChange }) => {
    const editor = useEditor({
        extensions: [StarterKit, Underline],
        content: content_data.content,
        onUpdate: ({ editor }) => {
            const html_content = editor.getHTML();
            handleContentChange(html_content, number)
        }
    })
    return (
        <div className="multiple-choices-card mb-3">
            <div className="multiple-choice-header">
                <div className="title-section tw-w-full">
                    <h5>Question Number {number + 1}</h5>
                    <hr className='stroke-in-question' />
                    {
                        isAudio ?
                            <AudioForm value={content_data.attachment} onChange={(data) => handleAudioChange(data)}/>
                            :
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
                    }

                </div>
                {/* <div className="question-type">
                    {isBundle && <button className="delete-question p-3" onClick={() => handleDeleteQuestion(number)}>X</button>}
                    <div className="button-type-wrapper">
                        <button>single answer</button>
                        <button>multiple choices</button>
                    </div>
                </div> */}
            </div>
            {
                !isAudio &&
                <div className="editor-input my-3">
                    <EditorContent editor={editor} />
                </div>
            }
            <div className="choices-input">
                {
                    content_data.answers.map((data, index) => (
                        <React.Fragment key={index}>
                            <label key={index} className='answer-poin'>
                                <input
                                    type="radio"
                                    value={data.content}
                                    checked={data.correct_answer === 1}
                                    onChange={() => handleAnswerChange(number, index)}
                                />
                                &nbsp;
                                <input
                                    type="text"
                                    name={`answer-${index}`}
                                    id={`answer-${index}`}
                                    value={data.content}
                                    className='input-answer'
                                    onChange={(e) => handleAnsContentChange(e.target.value, number, index)}
                                />
                                {<button className={`delete-answer ${index + 1 == 1 && 'tw-opacity-50'}`} onClick={index + 1 !== 1 ? () => handleDeleteAnswer(number, index) : null}>X</button>}
                            </label>
                            {index + 1 === content_data.answers.length && <button onClick={() => handlePushAnswer(number, index)}>Add Answer</button>}
                        </React.Fragment>
                    ))
                }
            </div>
        </div>
    )
}

export default MultipleChoice