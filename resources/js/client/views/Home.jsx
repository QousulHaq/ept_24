import React, { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom';

import { Grid2, InputBase, Skeleton } from '@mui/material'
import SearchIcon from '@mui/icons-material/Search';

import { useDispatch, useSelector } from 'react-redux';
import { fetchExam, changeParams } from '../slices/examSlice';

import CardStudentHome from '../components/CardStudentHome';
import UpcomingTest from '../components/UpcomingTest';
// import CalendarShow from '@components/CalendarShow';
import buku from "../public/img/buku.png";

function Home() {
  const dispatch = useDispatch()
  const [loaded, setLoaded] = useState(false);

  const loading = useSelector((state) => state.exam.status === "fetching")
  const time = useSelector((state) => state.exam.params?.state)
  const exams = useSelector((state) => state.exam.matter?.data)
  const username = useSelector((state) => state.auth.user?.name)

  const tagChange = (value) => {
    dispatch(changeParams({ state: value }))
  }

  useEffect(() => {
    dispatch(fetchExam())
  }, [])

  useEffect(() => {
    if (exams) {
      console.log({
        exams,
        time,
        loading
      })
    }
  }, [exams])

  return (
    <div className='Home'>
      {/* <button onClick={() => tagChange("future")}>future</button>
      <button onClick={() => tagChange("running")}>running</button>
      <button onClick={() => tagChange("past")}>past</button> */}
      <div className={`home-wrap tw-overflow-y-auto tw-bg-white tw-p-10 tw-pb-32`} style={{ width: "100%", height: "100vh" }}>
        <div className="greeter-box tw-w-full tw-py-3 tw-px-5 tw-mb-7 tw-flex tw-justify-between tw-items-center tw-bg-secondary7 tw-rounded-xl">
          {
            loading ?
              <div className="greeter-content">
                <h1 className='tw-text-3xl tw-font-bold tw-text-primary3 tw-mb-2 tw-w-60'>
                  <Skeleton variant='h6' width={"100%"} />
                </h1>
                <p className='tw-font-semibold tw-text-primary3 tw-w-80'>
                  <Skeleton variant='caption' width={"100%"} />
                </p>
              </div>
              :
              <div className="greeter-content">
                <h1 className='tw-text-3xl tw-font-bold tw-text-primary3 tw-mb-1'>Halo, {username}</h1>
                <p className='tw-font-semibold tw-text-primary3'>Siapkah kamu untuk test hari ini?</p>
              </div>
          }

          <img
            src={buku}
            onLoad={() => setLoaded(true)}
          />
          {!loaded && <Skeleton variant="circular" width={100} height={100} sx={{margin : "1rem"}} />}
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
            <CardStudentHome exams={exams} loading={loading} />
          </Grid2>

          <Grid2 size={4}>
            <div className="user-home-calendar tw-rounded-lg tw-shadow-lg tw-border tw-border-neutral3 tw-mb-5 tw-p-5 tw-overflow-hidden">
              {/* <CalendarShow /> */}
            </div>
            <UpcomingTest />
          </Grid2>
        </Grid2>
      </div>
    </div>
  )
}

export default Home