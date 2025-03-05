import React, { useState } from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { getHasEnrolledExam, getActiveExam, getIsStarted, getIsBanned } from '../../slices/examSlice'
import { getSection, getItemLoadedPercentage } from '../../slices/performSlice'

const Perform = () => {
    return (
        <div>perform</div>
    )
}

export default Perform