import React, { useEffect } from 'react'
import { useSelector } from 'react-redux'
import { useNavigate } from 'react-router-dom'
import { CircularProgress } from '@mui/material'

import { getIsStarted, getActiveExam } from '../../slices/examSlice'

const PerformWaiting = () => {
  const navigate = useNavigate()
  const isStarted = useSelector(getIsStarted)
  const activeExam = useSelector(getActiveExam)

  useEffect(() => {
    if (isStarted) {
      navigate("/client/perform/tackle")
    }
  }, [isStarted, navigate])

  return (
    <div className="flex items-center">
      <div className="w-1/2">
        <h1 className="text-2xl font-bold">{activeExam.name}</h1>
        <h1 className="text-xl">WAITING <b>PROCTOR</b> TO START AN EXAM</h1>
      </div>
      <div className="w-1/2 flex justify-center">
        <CircularProgress />
      </div>
    </div>
  )
}

export default PerformWaiting