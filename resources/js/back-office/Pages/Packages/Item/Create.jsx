import React, { useEffect, useState } from 'react'
import InputQuestionCode from '../../../ReactComponents/InputQuestionCode'
import MultipleChoice from '../../../ReactComponents/QuizBuilder/MultipleChoice'
import SelectClassification from '../../../ReactComponents/QuizBuilder/SelectClassification'
import Bundle from '../../../ReactComponents/QuizBuilder/Bundle'

import { useForm } from '@inertiajs/inertia-react'

const itemBundleChildrenObject = {
    answer_order_random: true,
    answers: [{
        content: "",
        correct_answer: false,
        order: 0,
    }],
    attachment: undefined,
    content: "<p></p>",
    order: 0,
    type: "multi_choice_single",
}

const itemBundleObject = {
    answer_order_random: true,
    attachment: undefined,
    category: null,
    children: [itemBundleChildrenObject],
    code: "",
    content: "<p></p>",
    duration: 0,
    type: "bundle",
}

const itemSingleObject = {
    answer_order_random: false,
    answers: [{
        content: "",
        correct_answer: false,
        order: 0,
    }],
    attachment: undefined,
    category: "",
    code: "",
    content: "<p></p>",
    type: "multi_choice_single",
}

const Create = ({ package_id, subpackage_id, config, categories }) => {

    const [singleQuestion, setSingleQuestion] = useState(itemSingleObject)

    const { data, setData, post, processing, errors } = useForm()

    useEffect(() => {
        console.log(errors)
    }, [errors])

    useEffect(() => {
        console.log({ package_id, subpackage_id, config, categories })
    }, [])

    useEffect(() => {
        setData(singleQuestion)
    }, [singleQuestion])

    const SaveEditData = () => {
        // console.log("data di create", data)
        post(`/back-office/package/${package_id}/item/store?subpackage=${subpackage_id}`)
    }

    const handleContentChange = (data, data_index) => {
        setSingleQuestion((prev) => ({
            ...prev,
            content: data
        }))
    }

    const handleAnsContentChange = (value, question_index, answer_index) => {
        setSingleQuestion((prev) => ({
            ...prev,
            answers: prev.answers.map((item, index) => (
                answer_index === index ? ({
                    ...item,
                    content: value
                }) : ({
                    ...item
                })
            ))
        }))
    }

    const handleAnswerChange = (question_index, answer_index) => {
        setSingleQuestion((prev) => ({
            ...prev,
            answers: prev.answers.map((item, index) => (
                index === answer_index ? ({
                    ...item,
                    correct_answer: 1
                }) : ({
                    ...item,
                    correct_answer: 0
                })
            ))
        }))
    }

    const handlePushAnswer = (question_index, answer_index) => {
        setSingleQuestion((prev) => ({
            ...prev,
            answers: [
                ...prev.answers,
                {
                    content: "",
                    correct_answer: false,
                    order: answer_index + 1,
                }
            ]
        }))
    };

    const handleDeleteAnswer = (question_index, answer_index) => {
        setSingleQuestion((prev) => ({
            ...prev,
            answers: prev.answers.filter((_, index) => index !== answer_index)
        }))
    }

    return (
        <>
            {
                config.item.type !== "multi_choice_single" ?
                    <>
                        <Bundle onDataChange={(data) => setData(data)} itemTemplateData={itemBundleObject} isAudio={config.item?.extra?.includes('audio')}/>
                    </>
                    :
                    <>
                        <SelectClassification classifications={categories} value={singleQuestion.category} onChange={(data) => setSingleQuestion((prev) => ({ ...prev, category: data }))} />
                        <InputQuestionCode content_data={singleQuestion.code} handleQuestionCodeChange={(data) => setSingleQuestion((prev) => ({ ...prev, code: data }))} />
                        <MultipleChoice
                            content_data={singleQuestion}
                            number={0}
                            isBundle={false}
                            isAudio={config.item?.extra?.includes('audio')}
                            handleContentChange={(data, id) => handleContentChange(data, id)}
                            handleAnsContentChange={(value, question_index, answer_index) => handleAnsContentChange(value, question_index, answer_index)}
                            handleAnswerChange={(question_index, answer_index) => handleAnswerChange(question_index, answer_index)}
                            handlePushAnswer={(index, answer_index) => handlePushAnswer(index, answer_index)}
                            handleDeleteAnswer={(question_index, answer_index) => handleDeleteAnswer(question_index, answer_index)}
                            handleAudioChange={(data) => setSingleQuestion((prev) => ({ ...prev, attachment: data }))}
                        />
                    </>
            }
            <button onClick={SaveEditData}>{processing ? "Menyimpan" : "Save Data"}</button>
        </>
    )
}

export default Create