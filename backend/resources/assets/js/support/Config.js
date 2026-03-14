import { otherwise } from './helpers';

export { default as Types } from './propTypes';

export default class Config {
    constructor(configs = {}) {
        if (configs instanceof Config) {
            return configs;
        }

        this.configs = Object.assign({}, configs);
    }

    merge(options = {}) {
        this.configs = Object.assign(this.configs, options);
        return this;
    }

    clone(options = {}) {
        return new Config(this.configs).merge(options);
    }

    all() {
        return this.configs;
    }

    get(dotted = "", fallback = null) {
        let value;

        dotted.split('.').forEach((path) => {
            value = otherwise(value, this.configs[path]);
        });

        return otherwise(value, fallback);
    }
}