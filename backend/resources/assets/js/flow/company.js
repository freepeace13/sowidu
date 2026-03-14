declare module 'company-api-payload' {
    declare export type APIPayload = {

    }
}

declare module 'company-service-payload' {
    declare export type ServicePayload = {

    }
}

declare module 'company-prop-types' {
    import type { AuthGroupStatus, RelationsType } from 'auth-prop-types';

    declare export type ReferenceType = {
        institutionTypeId: ?number,
        legalFormId: ?number,
    }

    declare export type PropTypes = {
        id?: ?number,
        uuid?: ?string,
        name?: ?string,
        institutionType?: ?string,
        avatar?: ?string,
        founder: User,
        legalForm?: ?string,
        address: Address,
        status?: ?AuthGroupStatus,
        authEmployeeId?: ?number,
        relations?: ?RelationsType,
        reference?: ?ReferenceType,
        createdAt?: ?string,
        updatedAt?: ?string,
        $alias?: ?string,
    }
}