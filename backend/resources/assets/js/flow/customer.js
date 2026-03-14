declare module 'customer-api-payload' {
    declare export type APIPayload = {

    }
}

declare module 'customer-service-payload' {
    declare export type ServicePayload = {

    }
}

declare module 'customer-prop-types' {
    import type { RelationsType } from 'auth-prop-types';

    declare export type PropTypes = {
        id: number,
        profile: Authenticatable,
        relations?: ?RelationsType,
        createdAt?: ?string,
        updatedAt?: ?string,
        $alias?: ?string,
    }
}