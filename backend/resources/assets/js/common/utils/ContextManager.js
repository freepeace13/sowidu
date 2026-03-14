/** @flow */

import Vue from 'vue';
import MessageBag from './MessageBag';

const isFunction = (value) => typeof value === 'function';

interface ContextManager<E> {
    $errors: E;
    get $loading(): boolean;
}

export default function createContext<I = any, E = any>(
    methods: Object
): ContextManager<E> {
    const $ref = Vue.observable({
        $$errors: new MessageBag,
        $$loading: false
    });

    const wrap = (method: Function): Function => (...i: I) => {
        const result = method(...i);

        if ((result instanceof Promise) === false) {
            return result;
        }

        $ref.$$errors = new MessageBag;
        $ref.$$loading = true;

        return result
            .then((response) => {
                $ref.$$loading = false;
                return response;
            })
            .catch((error) => {
                if (error instanceof MessageBag) {
                    $ref.$$errors = error;
                }

                $ref.$$loading = false;

                throw error;
            });
    }

    methods = Object.keys(methods)
        .reduce((acc: Object, val: string) => {
            const method = methods[val];
            if (isFunction(method)) {
                acc[val] = wrap(method);
            }
            return acc;
        }, {});

    const contextManager: ContextManager<E> = {
        ...methods,
        get $errors(): E {
            return $ref.$$errors;
        },
        set $errors(v: E) {
            $ref.$$errors = v;
        },
        get $loading(): boolean {
            return $ref.$$loading;
        }
    }

    return contextManager;
}