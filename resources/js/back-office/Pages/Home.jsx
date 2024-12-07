import React, { useEffect } from 'react'
import Card from '../ReactComponents/Card'
import DraftTable from '../ReactComponents/DraftTable'
import { Link } from '@inertiajs/inertia-react'

import "../../../../public/css/back-office/dashboard.css"

const Home = ({ totalStudent, totalFutureExam, totalPastExam, totalPresentExam, packages }) => {
  useEffect(()=>{
    console.log(packages)
    console.log(packages?.data.slice(0,3))
  },[])

  return (
    <>
      <div className='Home'>
        <div className="page-title-container">
          <h3 className='page-title text-black'>Recent Bank Question</h3>
          <a href="/" className='page-title-link'><h6>See All</h6></a>
        </div>
        <div className="card-content-container">
          {
            packages?.data.slice(0,3).map((data, index) => (
              <Card cardData={data} key={index}/>
            ))
          }
        </div>
        <div className="card-table">
          <div className="card-table-wrapper">
            <div className="table-title">
              <h4 className='table-title-text text-black'>Draft Details</h4>
              <Link href='/back-office/package' className='table-title-button'>See all</Link>
            </div>
            <div className="table-content">
              <DraftTable packages={packages}/>
            </div>
          </div>
        </div>
      </div>
    </>
  )
}

export default Home