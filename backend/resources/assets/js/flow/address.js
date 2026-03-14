declare module 'address-api-payload' {
    declare export type APIPayload = {
        street_id: ?number,
        houseNumber_id: ?number,
        zipcode_id: ?number,
        city_id: ?number,
        state_id: ?number,
        country_id: ?number,
    }
}

declare module 'address-service-payload' {
    declare export type ServicePayload = {
        streetId: ?number,
        houseNumberId: ?number,
        zipcodeId: ?number,
        cityId: ?number,
        stateId: ?number,
        countryId: ?number,
    }
}

declare module 'address-prop-types' {
    import type { ServicePayload as ReferenceType } from 'address-service-payload';

    declare export type PropTypes = {
        id?: ?number,
        label?: ?string,
        isActive?: boolean,
        street?: ?string,
        houseNumber?: ?string,
        zipcode?: ?string,
        city?: ?string,
        state?: ?string,
        country?: ?string,
        createdAt?: ?string,
        updatedAt?: ?string,
        $alias?: ?string,
        reference?: ?ReferenceType
    }
}