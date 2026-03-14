import { Media } from '~/services/models';

declare type Unit = {
    id: number,
    name: string
}

declare type ItemType = {
    id: number,
    name: string
}

declare module 'item-api-payload' {
    declare export type APIPayload = {
        id?: ?number,
        name: string,
        long_description: ?string,
        offered_price: number,
        fix_traded_price: number,
        retail_price: number,
        item_type_id: number,
        unit_id: number,
    }
}

declare module 'item-service-payload' {
    declare export type ServicePayload = {
        id?: ?number,
        name: string,
        longDescription: ?string,
        offeredPrice: number,
        fixTradedPrice: number,
        retailPrice: number,
        type: string,
        unit: string,
        media: Array<Media>,
        reference: ReferenceType
    }
}

declare module 'item-prop-types' {
    declare export type ReferenceType = {
        unitId: ?number,
        typeId: ?number
    }

    declare export type PropTypes = {
        id: ?number,
        name: ?string,
        longDescription: ?string,
        offeredPrice: ?number,
        fixTradedPrice: ?number,
        retailPrice: ?number,
        type: ?string,
        unit: ?string,
        media?: ?Array<Media>,
        quantity?: ?number,
        reference?: ?ReferenceType,
        createdAt?: ?string,
        updatedAt?: ?string,
        $alias?: ?string,
    }
}