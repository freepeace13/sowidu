/** @flow */

import { Model, Collection as Base } from '.';
import collect from 'collect.js';
import { isObject, isFunction } from '~/support/helpers';

export default class ModelCollection<T: Model> extends Base {
    constructor(collection: Array<T>): void {
        super(collection);
    }

    only(...props: Array<string>): this {
        return this.map((v) => collect(v).only(...props));
    }

    includes(v: T | number): boolean {
        return this.findIndex(v) !== false;
    }

    findIndex(v: T | number): number | boolean {
        return this.search((item) => {
            if (isObject(v)) {
                //$FlowFixMe
                return (isFunction(v.equals) && isFunction(item.equals))
                    ? item.equals(v)
                    //$FlowFixMe
                    : v.id === item.id
            }

            return item.id === v;
        });
    }

    find(id: number): T {
        return this.first((v) => v.id === id);
    }

    insert(v: T): this {
        !this.includes(v) && this.push(v);
        return this;
    }

    remove(v: T | number): this {
        this.includes(v) && this.splice(this.findIndex(v), 1);
        return this;
    }

    update(v: T): this {
        this.includes(v) && this.splice(this.findIndex(v), 1, [v]);
        return this;
    }

    updateOrInsert(v: T): this {
        return this.includes(v) ? this.update(v) : this.insert(v);
    }
}