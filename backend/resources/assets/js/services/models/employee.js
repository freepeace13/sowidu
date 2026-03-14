/** @flow */

import {  ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import { camelKeys, arrwrap } from '~/support/helpers';
import type { PropTypes, ReferenceType, AbilitiesType } from 'employee-prop-types';
import Authenticatable from './mixins/authenticatable';
import { Company, Address } from '.';
import ReferenceMutator from './mixins/referenceMutator';
import { Role, Permission } from './fundamentals/authorization';

export default class Employee extends ReferenceMutator(Authenticatable(Model)) {
    uuid: ?string;
    email: ?string;
    firstName: ?string;
    lastName: ?string;
    address: Address;
    employer: Company;
    specialization: ?string;
    abilities: AbilitiesType = {
        roles: [],
        permissions: []
    }
    reference: ReferenceType = {
        userId: null,
        userUuid: null,
        specializationId: null,
        accessRoles: []
    }

    constructor(props: PropTypes) {
        super(props);

        this.uuid = props.uuid;
        this.email = props.email;
        this.firstName = props.firstName;
        this.lastName = props.lastName;
        this.address = props.address;
        this.employer = props.employer;
        this.specialization = props.specialization;
        this.abilities = {
            ...this.abilities,
            ...Object.assign({}, props.abilities)
        };

        this.reference = {
            ...this.reference,
            ...Object.assign({}, props.reference)
        };
    }

    isFounder() {
        return this.reference.userId === this.employer.founder.id;
    }

    isPermissionViaRoles(value: Permission): boolean {
        const roles = arrwrap(this.abilities ? this.abilities.roles : []);
        return roles.some((v) => v.permissions.some((i) => i.id === value.id));
    }

    toggleRole(value: Role) {
        const roles = arrwrap(this.abilities ? this.abilities.roles : []);
        const collection = Role.collection(roles);

        if (collection.includes(value)) {
            collection.remove(value);
        } else {
            collection.insert(value);
        }

        this.abilities.roles = collection.all();
    }

    togglePermission(value: Permission) {
        const permissions = arrwrap(this.abilities ? this.abilities.permissions : []);
        const collection = Permission.collection(permissions);

        if (collection.includes(value)) {
            collection.remove(value);
        } else {
            collection.insert(value);
        }

        this.abilities.permissions = collection.all();
    }

    hasRole(value: Role): boolean {
        const roles = arrwrap(this.abilities.roles);
        console.log(roles);
        return roles.some((v) => v.id === value.id);
    }

    hasPermission(value: Permission): boolean {
        const roles = arrwrap(this.abilities.permissions);
        return roles.some((v) => v.id === value.id);
    }

    get name() {
        return [this.firstName, this.lastName].join(' ');
    }

    get title() {
        return [this.specialization, 'of', this.employer.name].join(' ');
    }

    static create(attrs: Object): Employee {
        const props: PropTypes = camelKeys(attrs);

        const roles = arrwrap(props.abilities ? props.abilities.roles : [])
        const permissions = arrwrap(props.abilities ? props.abilities.permissions : []);

        if (props.reference && Array.isArray(props.reference.accessRoles)) {
            props.reference = {
                ...props.reference,
                accessRoles: props.reference.accessRoles.map((v) => Role.create(v))
            };
        }
        
        return new Employee({
            ...props,
            address: Address.create(props.address),
            employer: Company.create(props.employer),
            abilities: props.abilities && {
                roles: roles.map((v) => Role.create(v)),
                permissions: permissions.map((v) => Permission.create(v))
            }
        });
    }

    static collection(collection: Array<Object>): Collection<Employee> {
        return new Collection(collection.map((v) => Employee.create(v)));
    }
}
