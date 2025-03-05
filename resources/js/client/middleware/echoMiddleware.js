import { save_active_users, add_active_user, remove_active_user, change_connection_state } from "../slices/authSlice";
import { addNotification } from "../slices/notificationSlice";
import { checkQualifiedStatus, fetchExam } from "../slices/examSlice";
import { getEchoInstance } from "../utils/echoServices";
import { showSnackbar } from "../slices/snackbarSlice";

const echoMiddleware = ({ dispatch, getState }) => (next) => (action) => {
    const state = getState();
    const authToken = state.auth.credential?.access_token;

    if (!authToken) {
        console.warn("Auth token belum tersedia.");
        return next(action);
    }
    
    const echo = getEchoInstance(authToken);
    
    // echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${authToken}`
    // echo.connector.pusher.config.auth.headers['Accept'] = 'application/json'
    
    switch (action.type) {
        case "echo/auth/listenAttendance":
            
            const channel = echo.join("attendance");

            channel
                .here((users) => dispatch(save_active_users(users)))
                .joining((user) => dispatch(add_active_user(user)))
                .leaving((user) => dispatch(remove_active_user(user)));

            echo.connector.pusher.connection.bind("state_change", (states) =>
                dispatch(change_connection_state(states.current))
            );

            break;

        case "echo/notification/listen":
            echo.private('notification.' + state.auth.user.username).notification((notification) => {
                console.log(notification)
                dispatch(addNotification(notification))
            })
            break;

        case "echo/exam/listenToExamChannel":
            const qualifyChange = (data) => dispatch(checkQualifiedStatus(data))

            const room = echo.join(`exam.${state.exam.chosenExam}`)

            room.listen('Exam\\ExamStarted', () => dispatch(fetchExam()))
            room.listen('Exam\\Participant\\ParticipantDisqualified', qualifyChange)
            room.listen('Exam\\Participant\\ParticipantQualified', qualifyChange)
            break;

        case "echo/exam/windowLeavingCheckerInit":
            const room_leaving_checker = echo.connector.channels['presence-exam.' + state.exam.chosenExam]

            document.documentElement.onmouseleave = () => {

                dispatch(showSnackbar({message : "you\'re opening another window.", severity : "warning"}))

                room_leaving_checker.whisper('security', {
                    hash: state.auth.user.hash,
                    type: 'mouseleave'
                });
            };

            break;

        default:
            break;
    }

    return next(action);
};

export default echoMiddleware;
