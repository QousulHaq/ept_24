import React, { useState, useEffect } from 'react'
import InputQuestionCode from '../InputQuestionCode'
import MultipleChoice from './MultipleChoice'
import Intros from './Intros'

const Bundle = ({ onDataChange, itemTemplateData, isAudio }) => {

    const [itemData, setItemData] = useState(itemTemplateData)

    const [newChildItem, setNewChildItem] = useState(itemTemplateData.children)
    const [newIntroduction, setNewIntroduction] = useState(itemTemplateData.content)
    const [newQuestionCode, setNewQuestionCode] = useState(itemTemplateData.code)
    const [newAttachment, setNewAttachment] = useState(itemTemplateData.attachment)

    const handleContentChange = (data, data_index) => {
        const updatedChild = newChildItem.map((item, index) => (
            index === data_index ? ({
                ...item,
                content: data
            }) : (
                {
                    ...item
                }
            )
        ))
        setNewChildItem(updatedChild)
    }

    const handleAnsContentChange = (value, question_index, answer_index) => {
        const updatedChild = newChildItem.map((item, index) => (
            index === question_index ? ({
                ...item,
                answers: item.answers.map((data, ans_index) => (
                    ans_index === answer_index ? (
                        {
                            ...data,
                            content: value
                        }
                    ) : (
                        {
                            ...data
                        }
                    )
                ))
            }) : (
                {
                    ...item
                }
            )
        ))
        setNewChildItem(updatedChild)
    }

    const handleAnswerChange = (question_index, answer_index) => {
        const updatedChild = newChildItem.map((item, index) => (
            index === question_index ? ({
                ...item,
                answers: item.answers.map((data, ans_index) => (
                    ans_index === answer_index ? (
                        {
                            ...data,
                            correct_answer: 1
                        }
                    ) : (
                        {
                            ...data,
                            correct_answer: 0
                        }
                    )
                ))
            }) : (
                {
                    ...item
                }
            )
        ))
        setNewChildItem(updatedChild)
    }

    const handlePushQuestion = () => {
        setNewChildItem((prevItem) => ([...prevItem, {
            answer_order_random: true,
            answers: [{
                content: "",
                correct_answer: 0,
                order: 0
            }],
            attachment: undefined,
            content: "<p></p>",
            order: newChildItem.length,
            type: "multi_choice_single"
        }]))
    }

    const handleDeleteQuestion = (question_index) => {
        const updatedChildren = newChildItem.filter((_, index) => index !== question_index)
        setNewChildItem(updatedChildren)
    }

    const handlePushAnswer = (question_index, answer_index) => {
        const updatedChild = newChildItem.map((item, index) =>
            index === question_index && item.answers[answer_index].content !== ""
                ? {
                    ...item,
                    answers: [
                        ...item.answers,
                        {
                            content: "",
                            correct_answer: 0,
                            order: item.answers.length
                        }
                    ]
                }
                : { ...item }
        );
        setNewChildItem(updatedChild);
    };

    const handleDeleteAnswer = (question_index, answer_index) => {
        const updatedAnswerList = newChildItem[question_index].answers.filter((_, index) => index !== answer_index)
        const updatedChild = newChildItem.map((item, index) => (
            index === question_index
                ? {
                    ...item,
                    answers: updatedAnswerList
                }
                : { ...item }
        ))
        setNewChildItem(updatedChild)
    }

    const handleAudioChange = (data, index) => {
        const updatedChild = newChildItem.map((item, data_index) => (
            data_index === index ? ({
                ...item,
                attachment: data
            }) : (
                {
                    ...item
                }
            )
        ))

        setNewChildItem(updatedChild)
    }

    const updateItemData = () => {
        setItemData((prevData) => ({
            ...prevData,
            ...(newChildItem && { children: newChildItem }),
            content: newIntroduction,
            code: newQuestionCode,
            attachment: newAttachment,
        }))
    }

    useEffect(() => {
        updateItemData()
    }, [newChildItem, newIntroduction, newQuestionCode, newAttachment])

    useEffect(() => {
        onDataChange(itemData)
    }, [itemData])

    return (
        <>
            <InputQuestionCode content_data={newQuestionCode} handleQuestionCodeChange={(data) => setNewQuestionCode(data)} />
            <Intros content_data={newIntroduction} handleIntroChange={(data) => setNewIntroduction(data)} question_code={itemTemplateData.code} />
            {
                newChildItem.map((data, index) => (
                    <MultipleChoice
                        content_data={data}
                        number={index}
                        key={index}
                        isAudio={isAudio}
                        handleContentChange={(data, id) => handleContentChange(data, id)}
                        handleAnsContentChange={(value, question_index, answer_index) => handleAnsContentChange(value, question_index, answer_index)}
                        handleAnswerChange={(question_index, answer_index) => handleAnswerChange(question_index, answer_index)}
                        handlePushAnswer={(index, answer_index) => handlePushAnswer(index, answer_index)}
                        handleDeleteQuestion={(question_index) => handleDeleteQuestion(question_index)}
                        handleDeleteAnswer={(question_index, answer_index) => handleDeleteAnswer(question_index, answer_index)}
                        handleAudioChange={(data) => handleAudioChange(data, index)}
                    />
                ))
            }
            <button onClick={handlePushQuestion}>Add Question</button>
        </>
    )
}

export default Bundle