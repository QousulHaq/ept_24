import axios from "axios";
import queryString from "query-string";
import axiosInstance from "./axiosClient";

let request = null;
let routes = null;

class LaravelRouter {
    async get(name, data = {}) {
        if (request === null) {
            request = axios.get("/api");
        }

        if (routes === null) {
            const { data: routeData } = await request;
            routes = routeData;
        }

        let result = routes.find((route) => route.name === name);

        if (!result) {
            throw new Error("Laravel route not found");
        }

        result = { ...result };

        const queryCandidates = {};
        for (let key in data) {
            if (Object.prototype.hasOwnProperty.call(data, key)) {
                if (result.uri.includes(`{${key}}`)) {
                    result.uri = result.uri.replace(`{${key}}`, data[key]);
                    delete data[key];
                } else if (result.method === "HEAD" || result.method === "GET") {
                    queryCandidates[key] = data[key];
                    delete data[key];
                }
            }
        }

        if (Object.keys(queryCandidates).length > 0) {
            result.uri += "?" + queryString.stringify(queryCandidates);
        }

        return result;
    }
}

const router = new LaravelRouter();

const laravelClient = {
    router,
    request: async (routeName, data = {}, config = {}) => {
        data = { ...data };
        try {
            const route = await router.get(routeName, data);

            return axiosInstance.request({
                method: route.method,
                url: (route.domain ?? "") + "/" + route.uri,
                data: data,
                ...config,
            });
        } catch (e) {
            throw e;
        }
    },
};

export default laravelClient;