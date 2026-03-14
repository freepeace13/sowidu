/** @flow */

import {  ModelCollection as Collection } from '~/support/wrappers';
import { Model } from '~/support/wrappers';
import { camelKeys } from '~/support/helpers';
import type { PropTypes } from 'contact-prop-types';

export function createContact(attrs: Object): Contact {
    const props: PropTypes = camelKeys(attrs);

    return new Contact({
        id: props.id,
        uuid: props.uuid,
        avatar: props.avatar,
        isUser: props.isUser,
        everyone: props.everyone,
        isCompany: props.isCompany,
        isEmployee: props.isEmployee,
        registered: props.registered,
        addressbooked: props.addressbooked,
        waitingForApproval: props.waitingForApproval,
        canAssignToCompany: props.canAssignToCompany,
        contactableType: props.contactableType,
        originalInfo: props.originalInfo,
        preferredInfo: props.preferredInfo,
    });
}

export default class Contact extends Model {
    id: number;
    uuid: string;
    avatar: string;
    isUser: boolean;
    everyone: boolean;
    isCompany: boolean;
    isEmployee: boolean;
    registered: boolean;
    addressbooked: boolean;
    waitingForApproval: boolean;
    canAssignToCompany: boolean;
    contactableType: ContactType;
    originalInfo: ContactableType;
    preferredInfo: ContactableType;

    constructor(props: PropTypes) {
        super();

        this.id = props.id;
        this.uuid = props.uuid;
        this.avatar = props.avatar;
        this.isUser = props.isUser;
        this.everyone = props.everyone;
        this.isCompany = props.isCompany;
        this.isEmployee = props.isEmployee;
        this.registered = props.registered;
        this.addressbooked = props.addressbooked;
        this.waitingForApproval = props.waitingForApproval;
        this.canAssignToCompany = props.canAssignToCompany;
        this.contactableType = props.contactableType;
        this.originalInfo = props.originalInfo;
        this.preferredInfo = props.preferredInfo;
    }

    // set originalInfo(value) {
    //     if (isOneOf([Employee, Company, User], value)) {
    //         return value;
    //     }

    //     return resolveContactableResource(this.contactableType)(value);
    // }

    // set preferredInfo(value) {
    //     if (isOneOf([Employee, Company, User], value)) {
    //         return value;
    //     }

    //     return resolveContactableResource(this.contactableType)(value);
    // }

    // canInvite() {
    //     if (this.originalInfo instanceof User) {
    //         return this.canAssignToCompany
    //     }

    //     return false
    // }

    // set registered(value) {
    //     return Boolean(value);
    // }

    // get longAddress() {
    //     return this.originalInfo.longAddress
    // }

    // typeString() {
    //     if (this.originalInfo instanceof User) {
    //         return 'Private Person'
    //     } else if (this.originalInfo instanceof Employee) {
    //         return `Employee of ${this.originalInfo.employer.fullName}`
    //     } else if (this.originalInfo instanceof Company) {
    //         return 'Company'
    //     }
    // }

    // newInstance(cols) {
    //     return new Contact({ ...this, ...cols })
    // }

    static create(props: Object): Contact {
        return createContact(props);
    }

    static collection(collection: Array<Object>): Collection<Contact> {
        return new Collection(collection.map((v) => Contact.create(v)));
    }
}
