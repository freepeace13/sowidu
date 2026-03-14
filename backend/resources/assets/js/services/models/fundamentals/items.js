/** @flow */

import { GenericModel } from '~/support/wrappers';
import { ModelCollection as Collection } from '~/support/wrappers';

export class Unit extends GenericModel {
    static collection(collection: Array<Object>): Collection<Unit> {
        return new Collection(collection.map((v) => Unit.create(v)));
    }
}

export class Type extends GenericModel {
    static collection(collection: Array<Object>): Collection<Type> {
        return new Collection(collection.map((v) => Type.create(v)));
    }
}