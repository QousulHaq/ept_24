import Vue from 'vue'
import { stringify } from 'querystring'
import { AxiosResponse } from 'axios'

/**
 * @type {null|Promise<>}
 */
let request = null;

/**
 * @type {null|Array}
 */
let routes = null;

class Router {
	/**
	 * @param name : String
	 * @param data : Object
	 * @returns {Promise<Object>}
	 */
	async get (name, data = {}) {
		if (request === null) {
			request = $axios.get('/api')
		}

		if (routes === null) {
			const { data } = await request;
			routes = data
		}

		let result = routes.find(_ => _.name === name);

		if (! result) {
			throw new Error('Laravel route not found')
		}

		result = { ...result };

		// processing uri for get or variable that laravel route wanted
		const queryCandidates = {};
		for (let key in data) {
			if (data.hasOwnProperty(key)) {
				if (result.uri.indexOf('{' + key + '}') !== -1) {
					Object.assign(result, {
						uri: result.uri.replace('{' + key + '}', data[key])
					});
					delete data[key]
				} else if (result.method === 'HEAD' || result.method === 'GET') {
					queryCandidates[key] = data[key];
					delete data[key]
				}
			}
		}

		if (Object.values(queryCandidates).length > 0) {
			Object.assign(result, {
				uri: result.uri + '?' + stringify(queryCandidates)
			})
		}

		return result
	}
}

const router = new Router();

const laravel  = {
	router,
	/**
	 * broker that translate laravel route into request
	 *
	 * @param routeName
	 * @param data
	 * @param config
	 * @return {Promise<AxiosResponse<any>>}
	 */
	request: async (routeName, data = {}, config = {}) => {
		data = Object.assign({}, data);

		try {
			const route = await router.get(routeName, data);

			return await $axios.request(Object.assign({}, {
				method: route.method,
				url: (route.domain ?? '') + '/' + route.uri,
				data: data
			}, config));
		} catch (e) {
			throw e
		}
	}
};

class Laravel extends Plugin {
	static install () {
		Vue.laravel = laravel;
		Object.defineProperties(Vue.prototype, {
			laravel: {
				get () {
					return laravel
				}
			}
		})
	}
}

Vue.use(Laravel);
