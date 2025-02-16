import React, { useEffect, useState } from 'react'

import { Grid2 } from '@mui/material'
import VisibilityRoundedIcon from '@mui/icons-material/VisibilityRounded';

import Card from '../ReactComponents/Card';
import DraftTable from '../ReactComponents/DraftTable';

import { Link } from '@inertiajs/inertia-react'

function Home({ totalStudent, totalFutureExam, totalPastExam, totalPresentExam, packages }) {
  
  useEffect(() => {
    console.log(packages)
  })

  return (
    <div className='Home'>
      <div className={`home-wrap tw-pt-5`} style={{ width: "100%", height: "100%" }}>
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
                <DraftTable
                  table_data={packages}
                  showed_data={["title", "code", "created_at"]}
                  action_button={
                    (row) => (
                      <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-md">
                        <Link className='tw-px-3 tw-py-2 hover:tw-bg-slate-200' href={`/back-office/package/${row?.id}?subpackage=${row?.children[0].id}`}>
                          <VisibilityRoundedIcon fontSize='small' color='secondary' />
                        </Link>
                      </div>
                    )
                  }
                />
              </div>
            </div>
          </div>
        </>
      </div>
    </div>
  )
}

export default Home