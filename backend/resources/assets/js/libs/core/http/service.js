export default class Service {
    options: Object = {
        baseURL: '/api'
    };

    constructor(options: Object = {}) {
        this.options = Object.assign(this.options, options);
    }

    route(prefix: string) {
        return this.options.baseURL.concat(prefix);
    }
}