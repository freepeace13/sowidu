import axios from 'axios'

const client = axios.create({
    timeout: 1000 * 10,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-Internal-Json-Request': true,
    },
})

export default class JsonService {
    constructor() {
        this.client = client
    }

    route(...parameters) {
        if (typeof window.route === 'function') {
            return window.route(...parameters)
        }
    }

    static create(...parameters) {
        return new this(...parameters)
    }
}
