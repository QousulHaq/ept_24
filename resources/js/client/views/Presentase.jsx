import React, { useState } from 'react'

import { Grid2, InputBase, FormControl, Select, MenuItem, InputLabel, Card, CardContent, CardActionArea, Typography, Box, CircularProgress } from '@mui/material'
import SearchIcon from '@mui/icons-material/Search';

// import LineChart from '@components/Chart/LineChart';
// import PieChart from '@components/Chart/PieChart';
// import CalendarShow from '@components/CalendarShow';

function Presentase() {
  return (
    <div className='presentase'>
      <div className="presentase-wrapper tw-overflow-y-auto tw-bg-white tw-p-10 tw-pb-32" style={{ width: "100%", height: "100vh" }}>
        <Grid2 container spacing={3}>
          <Grid2 size={8}>
            <div className="page-search tw-border tw-border-neutral3 tw-rounded-full tw-mb-5">
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
            <div className="page-title-wrapper tw-mb-5">
              <Grid2 container spacing={3}>
                <Grid2 size={5}>
                  <div className="tw-size-full tw-flex tw-items-center">
                    <h1 className='tw-text-2xl tw-font-bold tw-text-primary3'>Grafik Hasil Ujian</h1>
                  </div>
                </Grid2>
                <Grid2 size={7}>
                  <FormControl fullWidth size='small'>
                    <Select
                      id="demo-simple-select"
                      displayEmpty
                      // value={age}
                      // onChange={handleChange}
                      defaultValue={""}
                      sx={{ borderRadius: "100rem", backgroundColor: "hsla(210, 66%, 50%, 0.3)", color: "#2B7FD4" }}
                    >
                      <MenuItem value={""}>Pilih Kategori</MenuItem>
                      <MenuItem value={"Pelajar"}>Pelajar</MenuItem>
                      <MenuItem value={"Mahasiswa"}>Mahasiswa</MenuItem>
                      <MenuItem value={"Professional"}>Professional</MenuItem>
                    </Select>
                  </FormControl>
                </Grid2>
              </Grid2>
            </div>

            <div className="visual-grafik-ujian tw-mb-8">
              <div className="line-chart">
                <Grid2 container spacing={3}>
                  <Grid2 size={8}>
                    <div className="linechart-wrapper tw-bg-secondary7_2 tw-px-1 tw-pt-4 tw-pb-2 tw-rounded-xl">
                      {/* <LineChart /> */}
                      isinya grafik
                    </div>
                  </Grid2>
                  <Grid2 size={4}>
                    <div className="piechart-wrapper">
                      {/* <PieChart /> */}
                      isinya grafik
                    </div>
                  </Grid2>
                </Grid2>
              </div>
            </div>

            <div className="hasil-ujian-content tw-flex tw-flex-col tw-gap-5">
              <Card sx={{ borderRadius: "1.5rem" }}>
                <CardActionArea >
                  <CardContent>
                    <div className="tw-p-2 tw-flex tw-justify-between tw-items-center">
                      <div className="info-ujian">
                        <div className="judul-ujian-wrap">
                          <h2 className='tw-text-xl tw-font-bold tw-text-primary3'>Judul Ujian</h2>
                          <p className='tw-text-base tw-font-medium tw-text-primary3'>kode-ujian/k0103uj14n</p>
                        </div>
                      </div>
                      <div className="statistik-nilai">
                        <Box position="relative" display="inline-flex">
                          <CircularProgress variant="determinate" value={70} size={50} />
                          <Box
                            top={0}
                            left={0}
                            bottom={0}
                            right={0}
                            position="absolute"
                            display="flex"
                            alignItems="center"
                            justifyContent="center"
                          >
                            <p className='tw-font-bold tw-text-primary3'>70</p>
                          </Box>
                        </Box>
                      </div>
                    </div>
                  </CardContent>
                </CardActionArea>
              </Card>
            </div>
          </Grid2>
          <Grid2 size={4}>
            <div className="user-home-calendar tw-rounded-lg tw-shadow-lg tw-border tw-border-neutral3 tw-mb-5 tw-p-5 tw-overflow-hidden">
              {/* <CalendarShow /> */}
            </div>

            <h3 className='tw-text-base tw-font-bold tw-text-primary3 tw-text-center tw-mb-5'>Progress Ujian</h3>

            <Card sx={{ borderRadius: "0.5rem" }}>
              <CardActionArea >
                <CardContent>
                  <div className="progres-ujian-wrapper tw-flex tw-justify-between tw-items-center tw-px-3">
                    <div className="statistik-nilai">
                      <Box position="relative" display="flex">
                        <CircularProgress variant="determinate" value={70} size={40} />
                        <Box
                          top={0}
                          left={0}
                          bottom={0}
                          right={0}
                          position="absolute"
                          display="flex"
                          alignItems="center"
                          justifyContent="center"
                        >
                          <Typography variant="caption" component="div" color="textSecondary">
                            {`70`}
                          </Typography>
                        </Box>
                      </Box>
                    </div>
                    <div className="text-content">
                      <p className='tw-text-sm tw-font-semibold tw-text-primary3'>Judul Ujian</p>
                      <p className='tw-text-xs tw-font-medium tw-text-primary3'>100 ujian</p>
                    </div>
                  </div>
                </CardContent>
              </CardActionArea>
            </Card>
          </Grid2>
        </Grid2>
      </div>
    </div>
  )
}

export default Presentase