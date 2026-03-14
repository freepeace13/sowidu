/** @flow */

import { ModelCollection as Collection } from '~/support/wrappers';
import { GenericModel } from '~/support/wrappers';

export class Country extends GenericModel {
    static collection(collection: Array<Object>): Collection<Country> {
        return new Collection(collection.map((v) => Country.create(v)));
    }
}

export class State extends GenericModel {
    static collection(collection: Array<Object>): Collection<State> {
        return new Collection(collection.map((v) => State.create(v)));
    }
}

export class City extends GenericModel {
    static collection(collection: Array<Object>): Collection<City> {
        return new Collection(collection.map((v) => City.create(v)));
    }
}

export class Street extends GenericModel {
    static collection(collection: Array<Object>): Collection<City> {
        return new Collection(collection.map((v) => City.create(v)));
    }
}

export class HouseNumber extends GenericModel {
    static collection(collection: Array<Object>): Collection<HouseNumber> {
        return new Collection(collection.map((v) => HouseNumber.create(v)));
    }
}

export class Zipcode extends GenericModel {
    static collection(collection: Array<Object>): Collection<Zipcode> {
        return new Collection(collection.map((v) => Zipcode.create(v)));
    }
}