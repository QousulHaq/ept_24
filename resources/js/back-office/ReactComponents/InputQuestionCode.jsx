import React, { useState } from 'react'
import "../../../../public/css/back-office/multiple-choices.css"

const InputQuestionCode = ({ content_data, handleQuestionCodeChange }) => {

    return (
        <div className="bg-input-question-code multiple-choices-card mb-3">
            <h6>Question Code</h6>
            <input type="text" name="question-code" id="question-code" className='question-code-input' value={content_data} onChange={(e) => handleQuestionCodeChange(e.target.value)} />
        </div>
    )
}

export default InputQuestionCode