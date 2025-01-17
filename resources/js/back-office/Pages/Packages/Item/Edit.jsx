import React, { useEffect, useState } from 'react'
import InputQuestionCode from '../../../ReactComponents/InputQuestionCode'
import MultipleChoice from '../../../ReactComponents/QuizBuilder/MultipleChoice'
import Intros from '../../../ReactComponents/QuizBuilder/Intros'
import AudioQuestion from '../../../ReactComponents/QuizBuilder/AudioQuestion'
import IntrosAudioQuestion from '../../../ReactComponents/QuizBuilder/IntrosAudioQuestion'

import { useForm } from '@inertiajs/inertia-react'

const Edit = ({ item, isIntro, package: package_detail}) => {
  const [itemData, setItemData] = useState(item)

  const { data, setData, put, processing, errors } = useForm()

  const [newChildItem, setNewChildItem] = useState(item.children)

  const handleContentChange = (data, id) => {
    const updatedChild = newChildItem.map((item) => (
      item.id === id ? ({
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

  const [newIntroduction, setNewIntroduction] = useState(item.content)
  const [newQuestionCode, setNewQuestionCode] = useState(item.code)

  const handlePushQuestion = () => {
    setNewChildItem((prevItem) => ([...prevItem, {
      answer_order_random: "",
      answers: [{
        content: "",
        correct_answer: 0,
        order: 0
      }],
      attachment: undefined,
      content: "",
      order: 0,
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

  const updateItemData = () => {
    setItemData((prevData) => ({
      ...prevData,
      children: newChildItem,
      content: newIntroduction,
      code: newQuestionCode,
    }))
  }

  const SaveEditData = () => {
    console.log({
      data
    })
    put(`/back-office/package/${package_detail.id}/item/${item.id}/update `)
  }

  useEffect(() => {
    console.log("item", item)
    console.log(isIntro)
    console.log("package", package_detail)
  }, [])

  useEffect(() => {
    updateItemData()
  }, [newChildItem, newIntroduction, newQuestionCode])

  useEffect(() => {
    setData(itemData)
  }, [itemData])

  useEffect(() => {
    console.log(errors)
  }, [errors])

  return (
    <>
      {
        item.type !== "multi_choice_single" ?
          <>
            <InputQuestionCode content_data={newQuestionCode} handleQuestionCodeChange={(data) => setNewQuestionCode(data)} />
            <Intros content_data={newIntroduction} handleIntroChange={(data) => setNewIntroduction(data)} question_code={item.code} />
            {
              newChildItem.map((data, index) => (
                <MultipleChoice
                  content_data={data}
                  number={index}
                  key={index}
                  handleContentChange={(data, id) => handleContentChange(data, id)}
                  handleAnsContentChange={(value, question_index, answer_index) => handleAnsContentChange(value, question_index, answer_index)}
                  handleAnswerChange={(question_index, answer_index) => handleAnswerChange(question_index, answer_index)}
                  handlePushAnswer={(index, answer_index) => handlePushAnswer(index, answer_index)}
                  handleDeleteQuestion={(question_index) => handleDeleteQuestion(question_index)}
                  handleDeleteAnswer={(question_index, answer_index) => handleDeleteAnswer(question_index, answer_index)}
                />
              ))
            }
            <button onClick={handlePushQuestion}>Add Question</button>
            <button onClick={SaveEditData}>{processing ? "Menyimpan" : "Save Data"}</button>
          </>
          :
          <>
            <MultipleChoice content_data={item} number={0} />
          </>
      }
    </>
  )
}

export default Edit