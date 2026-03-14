import { CONTACT_TYPES } from '~/support/constants';

declare type ContactType =
    CONTACT_TYPES.USER |
    CONTACT_TYPES.COMPANY |
    CONTACT_TYPES.EMPLOYEE;

declare module 'contact-api-payload' {
    declare export type APIPayload = {

    }
}

declare module 'contact-service-payload' {
    declare export type ServicePayload = {

    }
}

declare module 'contact-prop-types' {
    declare export type PropTypes = {
        id: number,
        uuid: string,
        avatar: string,
        isUser: boolean,
        everyone: boolean,
        isCompany: boolean,
        isEmployee: boolean,
        registered: boolean,
        addressbooked: boolean,
        waitingForApproval: boolean,
        canAssignToCompany: boolean,
        contactableType: ContactType,
        originalInfo: ContactableType,
        preferredInfo: ContactableType,
    }
}