import React from 'react'

const Card = ({cardData}) => {
    return (
        <>
            <div className="bg-card-home">
                <div className="card-home-wrapper">
                    <div className="card-home-title">
                        <p className='card-home-p text-white'>Title</p>
                        <h6 className='text-white'>{cardData.title}</h6>
                    </div>
                    <div className="card-home-type text-white">
                        <div className="card-home-type-bg">
                            English
                        </div>
                    </div>
                    <div className="card-home-code">
                        <p className='card-home-p text-white'>Code</p>
                        <h6 className='text-white'>{cardData.code}</h6>
                    </div>
                    <div className="card-home-total-question">
                        <p className='card-home-p text-white'>Total Question</p>
                        <h6 className='text-white'>120</h6>
                    </div>
                </div>
            </div>
        </>
    )
}

export default Card