declare module 'employee-api-payload' {
    declare export type APIPayload = {

    }
}

declare module 'employee-service-payload' {
    declare export type ServicePayload = {

    }
}

declare module 'employee-prop-types' {
    import type { AuthGroupStatus, RelationsType } from 'auth-prop-types';

    declare export type AbilitiesType = {
        roles?: ?Array<Role>,
        permissions?: ?Array<Permission>
    }

    declare export type ReferenceType = {
        userId?: ?number,
        userUuid?: ?string,
        specializationId?: ?number,
        accessRoles: Array<Object>
    }

    declare export type PropTypes = {
        id?: ?number,
        uuid?: ?string,
        email?: ?string,
        avatar?: ?string,
        firstName?: ?string,
        lastName?: ?string,
        address: Address,
        status?: ?AuthGroupStatus,
        relations?: ?RelationsType,
        specialization?: ?string,
        employer?: ?Company,
        reference: ReferenceType,
        abilities: AbilitiesType,
        createdAt?: ?string,
        updatedAt?: ?string,
        $alias?: ?string,
    }
}