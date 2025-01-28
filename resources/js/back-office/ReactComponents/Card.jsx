import React from 'react'

import { Grid2, Chip } from '@mui/material'

function Card({ card_data }) {
    return (
        card_data.map((item, index) => (
            <Grid2 size={4} key={index}>
                <div className="card-wrapper tw-bg-primary3 tw-p-5 tw-rounded-xl">
                    <Grid2 container rowSpacing={3}>
                        <Grid2 size={6}>
                            <p className='tw-text-white tw-text-xs tw-font-light tw-m-0'>Title</p>
                            <h2
                                className='tw-text-white tw-text-lg tw-font-semibold tw-w-full tw-m-0'
                                style={{
                                    whiteSpace: 'nowrap',
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                }}
                            >{item.title}</h2>
                        </Grid2>
                        <Grid2 size={6} sx={{ display: 'flex', justifyContent: 'end' }}>
                            <div className="tw-flex tw-items-center">
                                {/* <Chip label={item.category.category_name} size='small' sx={{ bgcolor: `${item.category.color}`, color: 'white', padding: '0.5rem 1rem' }} /> */}
                                <Chip label={"English"} size='small' sx={{ bgcolor: `hsla(356, 58%, 53%, 0.3)`, color: 'white', padding: '0.5rem 1rem' }} />
                            </div>
                        </Grid2>
                        <Grid2 size={6}>
                            <p className='tw-text-white tw-text-xs tw-font-light tw-m-0'>Code</p>
                            <h3 className='tw-text-white tw-text-sm tw-font-semibold tw-m-0'>{item.code}</h3>
                        </Grid2>
                        <Grid2 size={6}>
                            <p className='tw-text-white tw-text-xs tw-font-light tw-m-0'>Total Question</p>
                            <h3 className='tw-text-white tw-text-sm tw-font-semibold tw-m-0'>{item.total_question}</h3>
                        </Grid2>
                    </Grid2>
                </div>
            </Grid2>
        ))
    )
}

export default Card