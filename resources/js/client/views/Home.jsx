import React, { useState } from 'react'

import { Grid2 } from '@mui/material'
import { InputBase } from '@mui/material'
import SearchIcon from '@mui/icons-material/Search';

import CardStudentHome from '../components/CardStudentHome';
import UpcomingTest from '../components/UpcomingTest';
// import CalendarShow from '@components/CalendarShow';
import buku from "../public/img/buku.png";

function Home() {
  const [namaTipeUjian, setNamaTipeUjian] = useState('Professional')

  function onSetNamaTipe(nama) {
    setNamaTipeUjian(nama)
  }

  return (
    <div className='Home'>
      <div className={`home-wrap tw-overflow-y-auto tw-bg-white tw-p-10 tw-pb-32`} style={{ width: "100%", height: "100vh" }}>
        <div className="greeter-box tw-w-full tw-py-3 tw-px-5 tw-mb-7 tw-flex tw-justify-between tw-items-center tw-bg-secondary7 tw-rounded-xl">
          <div className="greeter-content">
            <h1 className='tw-text-3xl tw-font-bold tw-text-primary3 tw-mb-1'>Halo, John Doe</h1>
            <p className='tw-font-semibold tw-text-primary3'>Siapkah kamu untuk test hari ini?</p>
          </div>
          <img src={buku} alt="" />
        </div>
        <Grid2 container spacing={2}>
          <Grid2 size={8}>
            <div className="page-search tw-border tw-border-neutral3 tw-rounded-full tw-mb-7">
              <div className="tw-flex tw-items-center tw-p-2">
                <SearchIcon fontSize='small' className='tw-mx-2' sx={{ color: "#ACACAD" }} />
                <InputBase
                  placeholder='Search'
                  className='tw-w-full'
                  fullWidth={true}
                  sx={{
                    "& input::placeholder": {
                      fontSize: "1rem",
                      fontFamily: "'Poppins', 'sans-serif'"
                    },
                  }}
                />
              </div>
            </div>
            <CardStudentHome onSetNamaTipe={onSetNamaTipe} />
          </Grid2>

          <Grid2 size={4}>
            <div className="user-home-calendar tw-rounded-lg tw-shadow-lg tw-border tw-border-neutral3 tw-mb-5 tw-p-5 tw-overflow-hidden">
              {/* <CalendarShow /> */}
            </div>
            <UpcomingTest namaTipe={namaTipeUjian} />
          </Grid2>
        </Grid2>
      </div>
    </div>
  )
}

export default Home