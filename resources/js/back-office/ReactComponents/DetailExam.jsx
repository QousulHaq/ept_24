import React, { useState, useEffect } from 'react';

import { utils } from '../app';

import { differenceInSeconds } from 'date-fns';

import { Divider, Badge, Chip } from '@mui/material';
import WifiIcon from '@mui/icons-material/Wifi';
import WifiOffIcon from '@mui/icons-material/WifiOff';

import DraftTable from './DraftTable';

import Swal from 'sweetalert2';

const DetailExam = ({ examId, examData }) => {
    const [exam, setExam] = useState(examData);
    const [participants, setParticipants] = useState(new Set());
    const [inFuture, setInFuture] = useState(false);

    const state = {
        with: ['participants', 'participants.attachments', 'package']
    };

    useEffect(() => {
        // Setup interval untuk mengecek inFuture
        const interval = setInterval(() => {
            if (exam.scheduled_at) {
                setInFuture(differenceInSeconds(new Date(), new Date(exam.scheduled_at)) > 0);
            }
        }, 1000);

        return () => clearInterval(interval);
    }, []);

    useEffect(() => {
        if (exam.started_at !== null) {
            getListen();
        }
    }, [exam.started_at]);

    const handleOpenLogs = (studentName, logs, imgUser) => {
        Swal.fire({
            title: `${studentName} Logs`,
            html: `
            <div class="tw-flex tw-items-start tw-space-x-4 tw-text-left">
                <img 
                    src=${imgUser} 
                    alt="Student Image" 
                    class="tw-w-48 tw-h-auto tw-rounded-lg tw-object-cover tw-shadow-md" 
                    onerror="this.src='/assets/img/avatar/avatar-1.png'">
                    <div class="tw-flex-1 tw-overflow-y-scroll tw-max-h-[400px]"
                >
                ${logs.length > 0 ?
                    logs.map(log => `
                        <li class="list-unstyled">
                            ${log.content}
                            <small class="time text-primary">${log.diff_time}</small>
                        </li>
                    `).join('')
                    :
                    "No logs found"
                }
                </div>
            </div>
           
            `,
            width: '800px',
        });
    }

    const getExam = async (withLoading = true) => {
        try {
            const response = await utils.request('api.back-office.exam.show',
                { exam: examId },
                { params: state },
                withLoading
            );
            setExam(response.data);
            console.log(response.data)
            console.log(participants)
            console.log(new Date())
            console.log(new Date(response.data.scheduled_at))
            console.log(differenceInSeconds(new Date(), new Date(exam.scheduled_at)))
        } catch (error) {
            console.error("Error fetching exam:", error);
        }
    };

    const startAnExam = async () => {
        try {
            await utils.request('api.back-office.exam.start-exam', { exam: examId });
            await window.swal('Success', 'Exam was started!', 'success');
            await getExam();
            getListen();
        } catch (error) {
            console.error("Error starting exam:", error);
        }
    };

    const endExamNow = async () => {
        const result = await window.swal({
            title: 'Are you sure to end this exam now?',
            text: 'This action cannot be revert!',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        });

        if (result) {
            try {
                await utils.request('api.back-office.exam.end-exam', { exam: examId });
                await window.swal('Success', 'Exam was ended, you might need to wait for a minutes before exam appear on history page!', 'success');
                utils.redirect('/back-office/monitor');
            } catch (error) {
                console.error("Error ending exam:", error);
            }
        }
    };

    const disqualifiedParticipant = async (user) => {
        const result = await window.swal({
            title: 'Are you sure to disqualified this participant?',
            text: 'Reverting this action may causing unexpected incident !',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        });

        if (result) {
            try {
                await utils.request('api.back-office.exam.disqualified-participant',
                    { exam: examId, user: user }
                );
                await window.swal('Success', 'Participant was disqualified!', 'success');
                await getExam();
            } catch (error) {
                console.error("Error disqualifying participant:", error);
            }
        }
    };

    const qualifiedParticipant = async (user) => {
        const result = await window.swal({
            title: 'Are you sure to qualified this participant ?',
            text: 'Reverting this action may causing unexpected incident !',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        });

        if (result) {
            try {
                await utils.request('api.back-office.exam.qualified-participant',
                    { exam: examId, user: user }
                );
                await window.swal('Success', 'Participant was qualified!', 'success');
                await getExam();
            } catch (error) {
                console.error("Error qualifying participant:", error);
            }
        }
    };

    const getListen = () => {
        window.Echo.join(`exam.${examId}`)
            .here((response) => {
                const newParticipants = new Set();  // Buat Set baru
                response.forEach(e => {
                    if (e && e.hash) {  // Pastikan e dan e.hash ada
                        newParticipants.add(e.hash);
                    }
                });
                setParticipants(newParticipants);
            })
            .joining((response) => {
                if (response && response.hash) {  // Tambah pengecekan
                    const newParticipants = new Set(participants);
                    newParticipants.add(response.hash);
                    setParticipants(newParticipants);
                    sendLog(response.hash, 'connected to server!', ['connection', 'connected']);
                }
            })
            .leaving((response) => {
                if (response && response.hash) {  // Tambah pengecekan
                    const newParticipants = new Set(participants);
                    newParticipants.delete(response.hash);
                    setParticipants(newParticipants);
                    sendLog(response.hash, 'disconnected from server!', ['connection', 'disconnected']);
                }
            })
            .listen('Exam\\Participant\\ParticipantReady', response => {
                if (response && response.hash) {  // Tambah pengecekan
                    sendLog(response.hash, 'enter room', ['state', 'ready']);
                }
            })
            .listenForWhisper('security', (kind) => {
                if (kind && kind.type === 'mouseleave' && kind.hash) {  // Tambah pengecekan
                    sendLog(kind.hash, 'possible open another window!', ['security', 'mouseleave']);
                }
            });
    };

    const sendLog = async (userHash, content, tags = []) => {
        // Tambah validasi
        if (!userHash || !examId) {
            console.error("Missing required data for sendLog:", { userHash, examId: examId });
            return;
        }

        try {
            await utils.request('api.back-office.exam.participant.log', {
                exam: examId,
                user: userHash,
                content,
                tags,
            }, {}, false);
            await getExam(false);
        } catch (error) {
            console.error("Error sending log:", error);
        }
    };

    const initEncryptor = async () => {
        if (!exam.package?.distribution_options?.has_passphrase) {
            try {
                await utils.request('api.back-office.exam.decrypt', { exam: examId });
                utils.reload();
            } catch (error) {
                console.error("Error initializing encryptor:", error);
            }
        }
    };

    const findParticipant = (hash) => {
        return participants.has(hash);
    };

    return (
        <>
            <div>
                {exam.package?.is_encrypted && (
                    <div className="card">
                        <div className="card-body">
                            <div className="alert alert-dark">
                                This exam is using encrypted package! [{exam.package.title}]
                            </div>
                            {!exam.package.distribution_options?.encryptor_ready && (
                                <div className="alert alert-danger">
                                    Encryptor unavailable, <a href="#" onClick={initEncryptor}>click here to init encryptor.</a>
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </div>
            <div className='exam-details'>
                <div className="exam-details-wrap tw-pt-5" style={{ width: "100%", height: "100%" }}>
                    <div className="page-title tw-flex tw-justify-between tw-items-center">
                        <h1 className='tw-text-3xl tw-font-bold tw-text-black tw-m-0'>Detail Exam Running</h1>
                        {!inFuture ? (
                            <button className="tw-bg-gray-300 tw-text-black tw-font-bold tw-border tw-border-gray-600 tw-rounded-full tw-px-3 tw-py-1 tw-opacity-40" disabled>Scheduled</button>
                        ) : !exam.started_at ? (
                            <button className="tw-bg-green-500 tw-text-white tw-rounded-full tw-px-3 tw-py-1" onClick={startAnExam}>
                                Start Exam
                            </button>
                        ) : (
                            <button className="tw-bg-red-500 tw-text-white tw-rounded-full tw-px-3 tw-py-1" onClick={endExamNow}>
                                End Exam
                            </button>
                        )}
                    </div>
                    <div className="table-content tw-mt-5">
                        {
                            <DraftTable
                                table_data={{
                                    data: [exam],
                                    from: 1,
                                }}
                                showed_data={["name", "scheduled_at", "started_at", "updated_at", "created_at"]}
                                table_action={0}
                                color="secondary5"
                            />
                        }
                    </div>
                    <div className="page-title tw-flex tw-justify-between tw-items-center tw-mt-5">
                        <h1 className='tw-text-3xl tw-font-bold tw-text-black tw-m-0'>List Particiapant</h1>
                    </div>
                    <div className="table-content tw-mt-5">
                        {
                            <DraftTable
                                table_data={{
                                    data: exam.participants,
                                    from: 1,
                                }}
                                showed_data={["name", "username", "email"]}
                                color="secondary5"
                                action_button={
                                    (row) => (
                                        <div className="action-button-wrapper tw-w-fit tw-flex tw-justify-center tw-items-center tw-border tw-border-primary3 tw-mx-auto tw-rounded-xl tw-p-1">
                                            {row.detail.status !== 'banned' ? (
                                                <button
                                                    className={`tw-bg-red-500 tw-text-white tw-rounded-full tw-px-3 tw-py-1 ${!inFuture ? 'tw-opacity-40' : ''}`}
                                                    onClick={() => disqualifiedParticipant(row.hash)}
                                                    disabled={!inFuture}
                                                >
                                                    Disqualified
                                                </button>
                                            ) : (
                                                <button
                                                className={`tw-bg-green-500 tw-text-white tw-rounded-full tw-px-3 tw-py-1 ${!inFuture ? 'tw-opacity-40' : ''}`}
                                                    onClick={() => qualifiedParticipant(row.hash)}
                                                    disabled={!inFuture}
                                                >
                                                    Qualified
                                                </button>
                                            )}
                                            <Divider orientation="vertical" flexItem sx={{ borderWidth: "0.px", borderColor: "#2B7FD4", margin: "0 0.25rem" }} />
                                            <Badge color='secondary' badgeContent={row.detail.logs.length}>
                                                <button
                                                    className="action-button tw-w-fit tw-bg-primary3 tw-text-white tw-py-1 tw-px-4 tw-mx-auto tw-rounded-full"
                                                    onClick={() => {
                                                        handleOpenLogs(
                                                            row?.name,
                                                            row?.detail?.logs,
                                                            utils.checkImage(row?.attachemnts?.length > 0 ? row.attachemnts[0] : null)
                                                        )
                                                    }}
                                                >
                                                    Logs
                                                </button>
                                            </Badge>
                                        </div>
                                    )
                                }
                                participantsStatusData={
                                    {
                                        status: (item) => item.detail.status === 'banned' ? 'DISQUALIFIED' : item.detail.status.toUpperCase(),
                                        connection: (item) => (
                                            findParticipant(item.hash) ? (
                                                <Chip label="Online" color="success" icon={<WifiIcon />} />
                                            ) : (
                                                <Chip label="Offline" icon={<WifiOffIcon />} />
                                            )
                                        ),
                                    }
                                }
                            />
                        }
                    </div>
                </div>
            </div>
        </>
    );
};

export default DetailExam;