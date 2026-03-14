/** @flow */

import SecureLS from 'secure-ls';

type Options = {
    isCompression: boolean,
    // expires: number
}

export default (options: Options) => {
    options = Object.assign({ isCompression: false }, options);

    const track = (key: string) => `expires=${key}`;

    const storage = new SecureLS({
        isComplression: Boolean(options.isCompression)
    });

    return {
        setItem: (key: string, value: any): void => {
            // storage.set(track(key), new Date);
            storage.set(key, value);
        },

        getItem: (key: string): any => {
            return storage.get(key);
            // let elapsed = storage.get(track(key));

            // if (typeof elapsed !== 'undefined') {
            //     elapsed = new Date(elapsed);
            //     elapsed.setMinutes(elapsed.getMinutes() + options.expires);

            //     if (elapsed <= new Date) {
            //         console.log('elapsed exeeded. persisted state removed', key);
            //         storage.remove(key);
            //     }
            // }

            // return value;
        },

        removeItem: (key: string): void => {
            storage.remove(key);
        }
    }
};