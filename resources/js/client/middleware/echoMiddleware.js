import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { save_active_users, add_active_user, remove_active_user, change_connection_state } from "../slices/authSlice";
import { addNotification } from "../slices/notificationSlice";
import { checkQualifiedStatus, fetchExam } from "../slices/examSlice";

const echoMiddleware = ({ dispatch, getState }) => {
    const state = getState();

    window.Pusher = Pusher;

    const echo = new Echo({
        broadcaster: 'pusher',
        enabledTransports: ['ws', 'wss'],
        key: process.env.MIX_PUSHER_APP_KEY,
        cluster: process.env.MIX_PUSHER_APP_CLUSTER,
        wsHost: process.env.MIX_PUSHER_HOST ?? window.location.hostname,
        wsPort: process.env.MIX_PUSHER_PORT ?? window.location.port,
        httpHost: process.env.MIX_PUSHER_HOST,
        forceTLS: false,
        disableStats: true,
    })

    window.Echo = echo;

    return (next) => (action) => {

        switch (action.type) {
            case "auth/login/fulfilled":
                const channel = echo.join("attendance");

                channel
                    .here((users) => dispatch(save_active_users(users)))
                    .joining((user) => dispatch(add_active_user(user)))
                    .leaving((user) => dispatch(remove_active_user(user)));

                echo.connector.pusher.connection.bind("state_change", (states) =>
                    dispatch(change_connection_state(states.current))
                );

                echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${state.auth.credential.access_token}`
                echo.connector.pusher.config.auth.headers['Accept'] = 'application/json'
                break;

            case "echo/notification/listen":
                echo.private('notification.' + state.auth.user.username).notification((notification) => {
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
                const room_leaving_checker = echo.connector.channels['private:exam.' + state.exam.chosenExam]

                document
                    .getElementsByTagName('html')[0]
                    .onmouseleave = () => {
                        //kalau dah bikin element notif kasih ke itu
                        console.log({
                            message: 'you\'re open another window.',
                            description: 'proctor notice it.',
                            placement: 'bottomLeft'
                        })

                        room_leaving_checker.whisper('security', {
                            hash: state.auth.user.hash,
                            type: 'mouseleave'
                        })
                    }
                break;

            default:
                break;
        }

        return next(action);
    };
};

export default echoMiddleware;
