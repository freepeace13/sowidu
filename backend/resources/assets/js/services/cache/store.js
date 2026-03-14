/** @flow */

import Vue from 'vue';
import { isJson } from '~/support/helpers';

interface Storage {
    setItem(key: string, valye: any): void;
    getItem(key: string): any;
    removeItem(key: string): void;
}

type Attributes = {
    key: string,
    value: any,
    seconds: number,
    created?: ?Date,
}

class CacheItem {
    key: string;
    seconds: number;
    created: Date;
    value: any;

    constructor(props: Attributes): void {
        this.key = props.key;
        this.value = props.value;
        this.seconds = props.seconds;
        this.created = props.created || new Date;
    }

    valid(): bool {
        return this.validity() > new Date;
    }

    validity(): Date {
        const created = new Date(this.created);
        created.setSeconds(created.getSeconds() + this.seconds);
        return created;
    }
}

const retrieve = (name: string, storage: Storage) => {
    let value = storage.getItem(name);
    
    if (isJson(value)) {
        value = (JSON.parse(value)).map((v) => new CacheItem(v));
    }

    return [ ...new Set(value) ];
}

function store(name: string, storage: Storage) {
    const $shared = Vue.observable({ items: retrieve(name, storage) });

    const $vm = new Vue({ created() {
        this.$watch(() => $shared.items, (value) => {
            storage.setItem(name, JSON.stringify(value));
        });
    } });

    return {
        clearAll(): void {
            $shared.items = [];
        },

        get(key: string): CacheItem {
            return $shared.items.find((v) => v.key === key);
        },

        search(key: string): number {
            return $shared.items.findIndex((v) => v.key === key);
        },

        replace(key: string, seconds: number, value: any): void {
            const index = this.search(key);
            const cache = new CacheItem({ key, seconds, value });

            $shared.items.splice(index, 1, cache);
        },

        forget(key: string): void {
            $shared.items.splice(this.search(key), 1);
        },

        has(key: string): bool {
            return this.search(key) !== -1;
        },

        put(key: string, seconds: number, value: any): void {
            $shared.items.push(new CacheItem({ key, seconds, value }));
        },

        replaceOrPut(key: string, seconds: number, value: any): void {
            if (this.has(key)) {
                this.replace(key, seconds, value);
            } else {
                this.put(key, seconds, value);
            }
        }
    }
}

export default (...options: Array<any>) => store(...options);