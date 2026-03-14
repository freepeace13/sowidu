/** @flow */

import { CONTACT_TYPES } from '../../support/constants';
import { camelKeys } from '../../support/helpers';
import {  ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import type { PropTypes } from 'user-prop-types';
import Authenticatable from './mixins/authenticatable';
import { Address } from '.';

export default class User extends Authenticatable(Model) {
    uuid: ?string;
    email: ?string;
    mobile: ?string;
    lastName: ?string;
    firstName: ?string;
    address: Address;

    constructor(props: PropTypes) {
        super(props);

        this.uuid = props.uuid;
        this.email = props.email;
        this.mobile = props.mobile;
        this.lastName = props.lastName;
        this.firstName = props.firstName;
        this.address = props.address;
    }

    get name() {
        return [this.firstName, this.lastName].join(' ');
    }

    get title() {
        return 'Private Users';
    }

    static create(attrs: Object): User {
        const props: PropTypes = camelKeys(attrs);
        return new User({
            ...props,
            address: Address.create(props.address),
        });
    }

    static collection(collection: Array<Object>): Collection<User> {
        return new Collection(collection.map((v) => User.create(v)));
    }
}