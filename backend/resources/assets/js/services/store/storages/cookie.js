/** @flow */

import Cookie from 'js-cookie';

export type Options = {
    secure: bool,
    expires: number
}

export default (options: Options) => ({
    setItem: (key: string, value: any): void => {
        Cookie.set(key, value, options);
    },

    getItem: (key: string): any => {
        return Cookie.get(key);
    },

    removeItem: (key: string): void => {
        Cookie.remove(key);
    }
});