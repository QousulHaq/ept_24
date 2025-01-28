import React, { useEffect, useState } from 'react'

import { Grid2 } from '@mui/material'
// import { InputBase } from '@mui/material'
// import SearchIcon from '@mui/icons-material/Search';

// import { drafts, examData } from '@data/drafts';

import Card from '../ReactComponents/Card';
import DraftTable from '../ReactComponents/DraftTable';

// import CardStudentHome from '@components/CardStudentHome';
// import UpcomingTest from '@components/UpcomingTest';
// import CalendarShow from '@components/CalendarShow';

// import { useAuth } from '@context/AuthContext';

function Home({ totalStudent, totalFutureExam, totalPastExam, totalPresentExam, packages }) {
  // const { user } = useAuth()
  // const [namaTipeUjian, setNamaTipeUjian] = useState('Professional')

  // function onSetNamaTipe(nama) {
  //   setNamaTipeUjian(nama)
  // }

  useEffect(() => {
    console.log(packages)
  })

  const pathDetailPackage = (package_id, subpackage_id) => `/back-office/package/${package_id}?subpackage=${subpackage_id}`

  return (
    <div className='Home'>
      <div className={`home-wrap tw-pt-5`} style={{ width: "100%", height: "100%" }}>
        {/* <div className={`home-wrap overflow-y-auto ${user.role === 'student' ? 'bg-white' : 'bg-neutral5'} p-10 pb-32`} style={{ width: "100%", height: "100vh" }}> */}
        {/* {user.role === 'student' ?
          <>
            <div className="greeter-box w-full py-3 px-5 mb-7 flex justify-between items-center bg-secondary7 rounded-xl">
              <div className="greeter-content">
                <h1 className='text-3xl font-bold text-primary3 mb-1'>Halo, {user.username}</h1>
                <p className='font-semibold text-primary3'>Siapkah kamu untuk test hari ini?</p>
              </div>
              <img src="/img/buku.png" alt="" />
            </div>
            <Grid2 container spacing={2}>
              <Grid2 size={8}>
                <div className="page-search border border-neutral3 rounded-full mb-7">
                  <div className="flex items-center p-2">
                    <SearchIcon fontSize='small' className='mx-2' sx={{ color: "#ACACAD" }} />
                    <InputBase
                      placeholder='Search'
                      className='w-full'
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
                <div className="user-home-calendar rounded-lg shadow-lg border border-neutral3 mb-5 p-5 overflow-hidden">
                  <CalendarShow />
                </div>
                <UpcomingTest namaTipe={namaTipeUjian} />
              </Grid2>
            </Grid2>
          </>
          : */}
        <>
          <div className="page-title tw-flex tw-justify-between tw-items-center">
            <h1 className='tw-text-3xl tw-font-bold tw-text-black tw-m-0'>Recent Bank Question</h1>
            <a href="/" className='tw-text-primary3 tw-text-lg tw-font-semibold'>See All</a>
          </div>
          <div className="card-content-container tw-my-6">
            <Grid2 container columnSpacing={4}>
              <Card card_data={packages?.data} />
            </Grid2>
          </div>
          <div className="card-table-content">
            <div className="card-table-wrapper tw-py-5 tw-px-8 tw-bg-white tw-rounded-xl">
              <div className="table-title tw-flex tw-justify-between tw-items-center tw-mb-5">
                <h2 className='tw-text-2xl tw-font-bold tw-text-black tw-m-0'>Draft Details</h2>
                <button className='tw-bg-primary3 tw-rounded-md tw-px-12 tw-py-2 tw-text-white tw-text-xs tw-font-light'>See all</button>
              </div>
              <div className="table-content">
                {/* {
                    user.role === 'admin' ?
                      <DraftTable table_data={packages?.data} table_style={1} />
                      :
                      <DraftTable table_data={examData} table_style={2} color='secondary5' />
                  } */}
                <DraftTable table_data={packages} showed_data={["title", "code", "created_at"]} table_action={1} pathDetail={pathDetailPackage}/>
              </div>
            </div>
          </div>
        </>
        {/* } */}
      </div>
    </div>
  )
}

export default Home