import React, { useEffect } from 'react';

import DetailExam from '../../ReactComponents/DetailExam';

import { utils } from '../../app';
import Swal from 'sweetalert2';

function MonitoringDetails({ exam, flash }) {

    useEffect(() => {
        console.log(exam)
    }, [])

    return (<DetailExam examId={exam.id} examData={exam} />)
}

export default MonitoringDetails