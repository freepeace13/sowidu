/** @flow */

export default class Cache {
    storage: Object = {};

    has(key: string) {
        return Object.keys(this.storage).includes(key);
    }

    set(key: string, value: any) {
        this.storage[key] = value;
    }

    get(key: string, defval: any = null) {
        return this.storage[key] || defval;
    }

    remove(key: string) {
        if (this.has(key)) {
            delete this.storage[key];
        }
    }
}