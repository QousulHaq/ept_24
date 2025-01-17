import React, { useState } from 'react'

import { useEditor, EditorContent } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'

import "../../../../../public/css/back-office/multiple-choices.css"

const AudioQuestion = () => {
    const editor = useEditor({
        extensions: [StarterKit, Underline],
        content: 'Tuliskan soal anda disini'
    })

    const [selectedAnswer, setSelectedAnswer] = useState("")

    const handleAnswerChange = (e) => {
        setSelectedAnswer(e.target.value)
    }

    const getEditorState = () => {
        if (editor) {
            const json = editor.getHTML();
            console.log('Editor State:', json);
        }
    };

    return (
        <div className="multiple-choices-card">
            <div className="multiple-choice-header">
                <div className="title-section">
                    <h5>Audio Section</h5>
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
                            <i class="fas fa-solid fa-strikethrough"></i>
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
            <div className="audio-section mt-3">
                <audio src="https://www.zapsplat.com/wp-content/uploads/2015/sound-effects-61905/zapsplat_multimedia_alert_chime_short_musical_notification_cute_child_like_001_64918.mp3?_=1" controls="true" class="audio-1"></audio>
            </div>
            <div className="choices-input mt-3">
                <label>
                    <input
                        type="radio"
                        value="Option 1"
                        checked={selectedAnswer === "Option 1"}
                        onChange={handleAnswerChange}
                    />
                    &nbsp;
                    Option 1
                </label>
                <label>
                    <input
                        type="radio"
                        value="Option 2"
                        checked={selectedAnswer === "Option 2"}
                        onChange={handleAnswerChange}
                    />
                    &nbsp;
                    Option 2
                </label>
                <label>
                    <input
                        type="radio"
                        value="Option 3"
                        checked={selectedAnswer === "Option 3"}
                        onChange={handleAnswerChange}
                    />
                    &nbsp;
                    Option 3
                </label>
            </div>
        </div>
    )
}

export default AudioQuestion