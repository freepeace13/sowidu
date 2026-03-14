/** @flow */

import {  ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import { camelKeys } from '~/support/helpers';
import type { PropTypes } from 'customer-prop-types';
import { Address, Company, User, resolveFromRaw } from '.';
import Relational from './mixins/relational';

export default class Customer extends Relational(Model) {
    profile: Authenticatable;

    constructor(props: PropTypes) {
        super(props);

        this.profile = props.profile;
    }

    billerIs(v: Model): boolean {
        if (this.profile instanceof Model) {
            return this.profile.equals(v);
        }

        return false;
    }

    get name() {
        return this.profile && this.profile.name;
    }

    get avatar() {
        return this.profile && this.profile.avatar;
    }

    get address() {
        return this.profile && this.profile.address;
    }

    // set biller(value) {
    //     return (value instanceof Somebody) ? value : new Somebody(value);
    // }

    // set contactPerson(value) {
    //     return (value instanceof ContactPerson) ? value : new ContactPerson(value);
    // }

    // get avatar() {
    //     return this.biller.avatar
    // }

    // get address() {
    //     return this.biller.address
    // }

    // get longAddress() {
    //     return this.address.longAddress || 'Not Specified'
    // }

    static create(attrs: Object): Customer {
        const props: PropTypes = camelKeys(attrs);

        return new Customer({
            ...props,
            profile: props.profile ? resolveFromRaw(props.profile) : null
        });
    }

    static collection(collection: Array<Object>): Collection<Customer> {
        return new Collection(collection.map((v) => Customer.create(v)));
    }
}