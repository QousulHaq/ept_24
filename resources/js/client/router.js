import Vue from 'vue'
import VueRouter from 'vue-router'

import ExamList from "./views/exam/ExamList";
import ExamDetail from "./views/exam/ExamDetail";
import AuthCallback from "./views/auth/AuthCallback";
import Perform from "./views/perform/Perform";
import PerformWaiting from "./views/perform/PerformWaiting";
import PerformTackle from "./views/perform/PerformTackle";
import PerformGoodbye from "./views/perform/PerformGoodbye";

Vue.use(VueRouter);

const route = {
	routes: [
		{
			path: '/access_token*',
			component: AuthCallback,
			meta: { auth: false }
		},
		{
			path: '/',
			component: { render: h => h() },
			name: 'root',
			redirect: { name: 'exam.list' },
		},
		{
			path: '/list',
			component: ExamList,
			name: 'exam.list',
			meta: { auth: true }
		},
		{
			path: '/exam/:id',
			component: ExamDetail,
			name: 'exam.detail',
			meta: { auth: true }
		},
		{
			path: '/perform',
			component: Perform,
			name: 'perform',
			meta: { auth: true },
			redirect: { name: 'perform.index' },
			children: [
				{
					path: '',
					name: 'perform.index',
					component: Perform
				},
				{
					path: 'waiting',
					name: 'perform.waiting',
					component: PerformWaiting
				},
				{
					path: 'tackle',
					name: 'perform.tackle',
					component: PerformTackle
				},
				{
					path: 'bye',
					name: 'perform.bye',
					component: PerformGoodbye
				}
			]
		}
	]
};

export default new VueRouter(route)
