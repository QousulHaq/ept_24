import React, { useState, useCallback, useEffect } from 'react'

import SearchIcon from '@mui/icons-material/Search';

import { debounce } from 'lodash';
import Axios from 'axios';

const SearchBar = ({ handleLoadingData, handleSearchedData }) => {

    const debounceUpdate = useCallback(
        debounce((value) => {
            Axios.get(`/api/back-office/user/participant${value ? '?keyword='+value : ''}`).then((response) => {
                handleLoadingData(false)
                handleSearchedData(response.data)
            })
        }, 800), []
    );

    const handleSearchChange = (e) => {
        debounceUpdate(e.target.value)
        handleLoadingData(true)
    }

    return (
        <div className="search-bar tw-mb-5 tw-w-full tw-py-2 tw-px-3 tw-border tw-border-neutral3 tw-rounded-lg tw-text-sm tw-flex">
            <SearchIcon />
            <input type="text" name="search" id="search" className='tw-text-sm tw-ml-3 tw- w-full' placeholder='Search student...' onInput={handleSearchChange} />
        </div>
    )
}

export default SearchBar