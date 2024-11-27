
const authMiddleware = (router) => {
	router.beforeEach(((to, from, next) => {
		if (to.matched.some(route => route.meta?.auth)) {
			if (! router.app.$store.getters['auth/authenticated']) {
				// noinspection JSIgnoredPromiseFromCall
				router.app.$store.dispatch('auth/getCode');
			}

			if (router.app.$store.getters['auth/tokenExpired']) {
				router.app.$store.dispatch('auth/getCode');
			}
		}
		next()
	}))
};

const presenceMiddleware = (router) => {
		router.afterEach((() => {
			if (router.app.$store.getters['auth/authenticated']) {

			}
		}))
};

export {
	authMiddleware,
	presenceMiddleware
}
