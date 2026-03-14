/** @flow */

import { camelKeys } from '~/support/helpers';
import { ModelCollection as Collection } from '~/support/wrappers';
import { Company } from '..';

type ContactRequestProps = {
    id: number,
    sender: Authorizable,
    requestedAt: string,
    status: 'rejected' | 'pending',
}

export class ContactRequest {
    id: number;
    sender: Authorizable;
    requestedAt: string;
    status: 'rejected' | 'pending';

    constructor(props: ContactRequestProps): void {
        this.id = props.id;
        this.sender = props.sender;
        this.requestedAt = props.requestedAt;
        this.status = props.status;
    }

    static create(attrs: Object): ContactRequest {
        const props: ContactRequestProps = camelKeys(attrs);
        return new ContactRequest(props);
    }
}

type EmploymentRequestProps = {
    id: number,
    company: Company,
    requestedAt: string,
    status: 'rejected' | 'pending',
}

export class EmploymentRequest {
    id: number;
    company: Company;
    requestedAt: string;
    status: 'rejected' | 'pending';

    constructor(props: EmploymentRequestProps): void {
        this.id = props.id;
        this.company = props.company;
        this.requestedAt = props.requestedAt;
        this.status = props.status;
    }

    static create(attrs: Object): EmploymentRequest {
        const props: EmploymentRequestProps = camelKeys(attrs);
        return new EmploymentRequest(props);
    }
}