/** @flow */

import { ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import { camelKeys, isObject } from '~/support/helpers';
import type { PropTypes } from 'address-prop-types';
import type { ServicePayload as ReferenceType } from 'address-service-payload';
import ReferenceMutator from './mixins/referenceMutator';

export default class Address extends ReferenceMutator(Model) {
    label: ?string;
    isActive: ?boolean;
    street: ?string;
    houseNumber: ?string;
    zipcode: ?string;
    city: ?string;
    state: ?string;
    country: ?string;
    reference: ?ReferenceType = {
        streetId: null,
        houseNumberId: null,
        zipcodeId: null,
        cityId: null,
        stateId: null,
        countryId: null,
    };

    constructor(props: PropTypes) {
        super(props);

        this.label = props.label;
        this.isActive = Boolean(props.isActive);
        this.street = props.street;
        this.houseNumber = props.houseNumber;
        this.zipcode = props.zipcode;
        this.city = props.city;
        this.state = props.state;
        this.country = props.country;
        this.reference = {
            ...this.reference,
            ...Object.assign({}, props.reference)
        };
    }

    static create(attrs: Object): Address {
        const props: PropTypes = camelKeys(attrs);
        return new Address(props);
    }

    static collection(collection: Array<Object>): Collection<Address> {
        return new Collection<Address>(collection.map((v) => Address.create(v)));
    }
}