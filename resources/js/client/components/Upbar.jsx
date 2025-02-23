import React from 'react'

import SearchIcon from '@mui/icons-material/Search';
import { Avatar, InputBase, Grid2 } from '@mui/material'

function Upbar() {
  
  return (
    <div className="tw-border-b tw-border-neutral4 tw-p-10 tw-pt-3 tw-pb-2">
      <Grid2 container columnSpacing={4}>
        <Grid2 size={5}>
          <div className="upbar-search tw-bg-neutral5 tw-rounded-lg">
            <div className="tw-flex tw-items-center tw-pe-5">
              <SearchIcon fontSize='small' className='tw-mx-2' />
              <InputBase
                placeholder='Search Your Bank Question, Draft or Attachment'
                className='tw-w-full'
                fullWidth={true}
                sx={{
                  "& input::placeholder": {
                    fontSize: "0.8rem",
                    fontFamily: "'Poppins', 'sans-serif'"
                  },
                }}
              />
            </div>
          </div>
        </Grid2>
        <Grid2 size={7}>
          <div className="account-wrap tw-flex tw-justify-end tw-items-center">
            <div className="avatar tw-me-2">
              <Avatar sx={{ width: 35, height: 35 }} >F</Avatar>
            </div>
            <div className="account-name">
              <p className='tw-text-sm tw-font-semibold '>John Doe</p>
              <p className='tw-text-xs tw-font-light'>Student</p>
            </div>
          </div>
        </Grid2>
      </Grid2>
    </div>
  )
}

export default Upbar