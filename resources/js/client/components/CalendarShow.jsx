import React, { useState } from 'react' 
import Calendar from 'react-calendar'

import 'react-calendar/dist/Calendar.css'; 
import './CalendarStyle.css'; 

import KeyboardArrowRightRoundedIcon from '@mui/icons-material/KeyboardArrowRightRounded'; 
import KeyboardDoubleArrowRightRoundedIcon from '@mui/icons-material/KeyboardDoubleArrowRightRounded'; 
import KeyboardArrowLeftRoundedIcon from '@mui/icons-material/KeyboardArrowLeftRounded'; 
import KeyboardDoubleArrowLeftRoundedIcon from '@mui/icons-material/KeyboardDoubleArrowLeftRounded'; 

const CalendarShow = () => {
    const [value, setValue] = useState(new Date())

    return (
        <Calendar
            onChange={setValue}
            value={value}
            locale="id-ID"
            navigationLabel={({ date, label, locale, view }) => (
                <div className="navigation-wrapper tw-flex tw-flex-col tw-justify-center tw-items-center tw-size-full">
                    <span className='tw-text-primary3 tw-font-bold tw-text-lg tw-leading-none tw-font-sans'>{date.toLocaleDateString("id-ID", { month: "long" })}</span>
                    <span className='tw-text-primary3 tw-font-extrabold tw-text-sm tw-leading-none tw-font-sans'>{date.toLocaleDateString("id-ID", { year: "numeric" })}</span>
                </div>
            )}

            nextLabel={<div className='tw-size-full tw-flex tw-justify-center tw-items-center'><div className='tw-bg-primary3 tw-size-fit tw-rounded-full'><KeyboardArrowRightRoundedIcon sx={{ color: "white" }} /></div></div>}
            next2Label={<div className='tw-size-full tw-flex tw-justify-center tw-items-center'><div className='tw-bg-primary3 tw-size-fit tw-rounded-full'><KeyboardDoubleArrowRightRoundedIcon sx={{ color: "white" }} /></div></div>}
            prevLabel={<div className='tw-size-full tw-flex tw-justify-center tw-items-center'><div className='tw-bg-primary3 tw-size-fit tw-rounded-full'><KeyboardArrowLeftRoundedIcon sx={{ color: "white" }} /></div></div>}
            prev2Label={<div className='tw-size-full tw-flex tw-justify-center tw-items-center'><div className='tw-bg-primary3 tw-size-fit tw-rounded-full'><KeyboardDoubleArrowLeftRoundedIcon sx={{ color: "white" }} /></div></div>}

            showNeighboringMonth={false}
        />
    )
}

export default CalendarShow 