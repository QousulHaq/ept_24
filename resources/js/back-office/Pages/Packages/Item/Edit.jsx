import React, { useEffect, useState } from 'react'
import InputQuestionCode from '../../../ReactComponents/InputQuestionCode'
import MultipleChoice from '../../../ReactComponents/QuizBuilder/MultipleChoice'
import Bundle from '../../../ReactComponents/QuizBuilder/Bundle'
import SelectClassification from '../../../ReactComponents/QuizBuilder/SelectClassification'
import Intros from '../../../ReactComponents/QuizBuilder/Intros'
import AudioQuestion from '../../../ReactComponents/QuizBuilder/AudioForm'
import IntrosAudioQuestion from '../../../ReactComponents/QuizBuilder/IntrosAudioQuestion'

import { useForm } from '@inertiajs/inertia-react'

const Edit = ({ item, isIntro, package_id, config, categories }) => {
  const [singleQuestion, setSingleQuestion] = useState(item)

  const { data, setData, put, processing, errors } = useForm()

  useEffect(() => {
    console.log(errors)
  }, [errors])

  useEffect(() => {
    console.log({
      item,
      categories,
      package_id,
      config
    })
  }, [errors])

  useEffect(() => {
    setData(singleQuestion)
  }, [singleQuestion])

  const SaveEditData = () => {
    put(`/back-office/package/${package_id}/item/${item.id}/update`)
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
        item?.type !== "multi_choice_single" ?
          <>
            <Bundle onDataChange={(data) => setData(data)} itemTemplateData={item} isAudio={config.item?.extra?.includes('audio')}/>
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

export default Edit