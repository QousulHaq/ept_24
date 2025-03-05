import React from 'react'
import { useNavigate } from 'react-router-dom'
import { useSelector } from 'react-redux'
import { Button } from '@mui/material'

import { getActiveExam } from '../../slices/examSlice'

const PerformGoodbye = () => {

  const navigate = useNavigate()
  const activeExam = useSelector(getActiveExam)

  return (
    <div className="flex items-center">
      <div className="w-1/2">
        <h1 className="text-2xl font-bold">{activeExam.name}</h1>
        <h1 className="text-xl">
          <b>CONGRATULATIONS</b> YOU'VE COMPLETED YOUR EXAM
        </h1>
        <Button
          onClick={() => navigate("/client")}
          variant="contained"
          color="primary"
          fullWidth
        >
          BACK TO HOMEPAGE
        </Button>
      </div>
      <div className="w-1/2 flex justify-center">
        Loading Icon
      </div>
    </div>
  )
}

export default PerformGoodbye