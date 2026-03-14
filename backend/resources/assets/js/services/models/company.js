/** @flow */

import {  ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import { camelKeys } from '../../support/helpers';
import type { PropTypes, ReferenceType } from 'company-prop-types';
import { Address, User } from '.';
import Authenticatable from './mixins/authenticatable';
import ReferenceMutator from './mixins/referenceMutator';

export default class Company extends ReferenceMutator(Authenticatable(Model)) {
    uuid: ?string;
    name: ?string;
    institutionType: ?string;
    legalForm: ?string;
    founder: User;
    address: Address;
    authEmployeeId: ?number;
    reference: ReferenceType = {
        institutionTypeId: null,
        legalFormId: null
    };

    constructor(props: PropTypes) {
        super(props);

        this.uuid = props.uuid;
        this.name = props.name;
        this.institutionType = props.institutionType;
        this.founder = props.founder;
        this.legalForm = props.legalForm;
        this.address = props.address;
        this.authEmployeeId = props.authEmployeeId;
        this.reference = {
            ...this.reference,
            ...Object.assign({}, props.reference)
        }
    }

    static create(attrs: Object): Company {
        const props: PropTypes = camelKeys(attrs);
        return new Company({
            ...props,
            founder: User.create(props.founder),
            address: Address.create(props.address),
        });
    }

    static collection(collection: Array<Object>): Collection<Company> {
        return new Collection(collection.map((v) => Company.create(v)));
    }
}