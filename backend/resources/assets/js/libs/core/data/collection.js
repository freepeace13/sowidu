/** @flow */

import { Collection as BaseCollection } from 'collect.js';

class Collection<T> extends BaseCollection {
    constructor(collection: Array<T> | T) {
        super(collection);
    }

    // $FlowFixMe
    [Symbol.iterator]() {
        let index = -1;
        let value;
        let keys = Object.keys(this.items);

        return {
            next: () => {
                index += 1;
                value = this.items[keys[index]];

                return {
                    value: !Array.isArray(this.items) ? [keys[index], value] : value,
                    done: index >= keys.length
                }
            }
        }
    }
}

export const collect = function(collection: Array<any> | Object): Collection<any> {
    return new Collection<any>(collection);
};

export default Collection;