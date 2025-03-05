import React, { useState, useCallback, useEffect, useMemo } from 'react'
import { useDispatch, useSelector } from 'react-redux'
import store from '../slices/store'
import { Outlet, useNavigate } from 'react-router-dom'

import { Alert, CircularProgress, LinearProgress, Button } from '@mui/material'

import { getHasEnrolledExam, getActiveExam, getIsStarted, getIsBanned } from '../slices/examSlice'
import { getSection, getItemLoadedPercentage, fetchSections } from '../slices/performSlice'

import UpbarPerform from '../components/UpbarPerform'
import unloadedImage from "../public/img/unloaded-image.svg"

function GradientCircularProgress() {
    return (
        <React.Fragment>
            <svg width={0} height={0}>
                <defs>
                    <linearGradient id="my_gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stopColor="#2B7FD4" />
                        <stop offset="100%" stopColor="#64398B" />
                    </linearGradient>
                </defs>
            </svg>
            <CircularProgress sx={{ 'svg circle': { stroke: 'url(#my_gradient)' } }} />
        </React.Fragment>
    );
}

function PerformLayout() {

    const dispatch = useDispatch()
    const navigate = useNavigate()

    const hasEnrolledExam = useSelector(getHasEnrolledExam)
    const activeExam = useSelector(getActiveExam)
    const isStarted = useSelector(getIsStarted)
    const isBanned = useSelector(getIsBanned)

    const sections = useSelector(getSection)
    const itemLoadedPercentage = useSelector(getItemLoadedPercentage)

    const [state, setState] = useState("unloaded")

    const isExamEnded = useMemo(() => {
        return sections.every((s) => s.ended_at !== null);
    }, [sections]);

    const bannedText = {
        message: "You have been disqualified!",
        description:
            "If you feel you didn't do anything wrong, ask the proctor to let you continue your exam...",
    };

    const toGoodbyePage = () => navigate("/client/perform/bye");
    const toWaitingPage = () => navigate("/client/perform/waiting");
    const toTacklePage = () => navigate("/client/perform/tackle");

    const boot = async () => {
        setState("loading");
        dispatch(fetchSections()).then(() => {
            setState("loaded");
            if (isExamEnded) {
                toGoodbyePage();
            } else if (isStarted) {
                toTacklePage();
            } else {
                toWaitingPage();
            }
        }).catch((error) => {
            console.error("Failed to load sections:", error);
            setState("unloaded");
        })
    };

    useEffect(() => {
        if (!hasEnrolledExam) {
            navigate("/exam/list");
            return;
        }

        // $nextTick equivalent: Jalankan boot setelah render pertama selesai
        setTimeout(() => {
            if (!isBanned) {
                boot();
            }
        }, 0);
    }, [hasEnrolledExam, isBanned, dispatch]);

    useEffect(() => {
        if (isExamEnded) {
            toGoodbyePage();
        }
    }, [isExamEnded]);

    useEffect(() => {
        if (isBanned) {
            console.log({
                message: bannedText.message,
                description: bannedText.description,
                placement: "bottomLeft",
            });
        }
    }, [isBanned]);

    useEffect(() => {
        console.log({
            hasEnrolledExam,
            activeExam,
            isStarted,
            isBanned,
            sections,
            itemLoadedPercentage,
        })
    }, [isBanned]);

    return (
        <div className="tw-w-screen tw-h-screen">
            <UpbarPerform />
            <div className="perform-exam-content">
                {
                    isBanned ? (
                        <div className="banned-element">
                            <Alert severity="error" sx={{ mb: 2 }}>
                                <strong>{bannedText.message}</strong>
                                <p>{bannedText.description}</p>
                            </Alert>
                        </div>
                    ) : state === "loading" ? (
                        <div className="loading-element">
                            <div className="loading-element-wrapper tw-flex tw-flex-col tw-justify-center tw-items-center tw-mt-40">
                                <GradientCircularProgress />
                                <div className="loading-bar-wrapper tw-mt-10">
                                    <div style={{ padding: '30px', border: '1px solid #91d5ff', backgroundColor: '#e6f7ff' }}>
                                        Downloading content. Please wait...
                                    </div>
                                    <LinearProgress variant="determinate" value={itemLoadedPercentage} />
                                </div>
                            </div>
                        </div>
                    ) : state === "unloaded" ? (
                        <div className="unloaded-element">
                            <div className="unloaded-wrapper tw-flex tw-flex-col tw-justify-center tw-items-center tw-mt-20">
                                <img src={unloadedImage} style={{ width: "14rem", height: "auto" }} />
                                <p className='tw-my-6'>Your exam properties have not loaded yet. Click the button below to reload.</p>
                                <Button variant="contained" color="primary" onClick={() => boot()}>
                                    Reload
                                </Button>
                            </div>
                        </div>
                    ) : state === "loaded" ?
                        <Outlet />
                        :
                        ""
                }
            </div>
        </div>
    )
}

export default PerformLayout