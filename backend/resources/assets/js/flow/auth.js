import { ContactRequest, EmploymentRequest } from '~/services/models/misc';

declare module 'auth-access-token-type' {
    declare export type AccessTokenType = {
        accessToken: string
    }
}

declare module 'auth-login-payload' {
    declare export type LoginCredentials = {
        username: ?string,
        password: ?string
    }
}

declare module 'auth-role-permission-payload' {
    declare export type RolePermissionPayload = {
        roles: Array<number>,
        permissions: Array<number>
    }
}

declare module 'auth-permission-types' {
    declare export type PermissionTypes = {
        id: ?number,
        name: ?string
    }
}

declare module 'auth-prop-types' {
    declare export type AuthGroupStatus = {
        confirmed?: boolean,
        authStatus?: AuthStatus,
        skippedAddressAt?: any
    }

    declare export type ContactRelations = {
        'contact:id': number,
        'contact:applicable': boolean,
        'contact:connected': boolean,
        'contact:invited': boolean
    }

    declare export type EmploymentRelations = {
        'employment:id': number,
        'employment:applicable': boolean,
        'employment:employed': boolean,
        'employment:invited': boolean
    }

    declare export type RelationsType = ContactRelations & ?EmpoymentRelations;
}