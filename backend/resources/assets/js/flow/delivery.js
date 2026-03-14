declare type DeliveryType = 'incoming' | 'outgoing';

declare module 'delivery-api-payload' {
    declare export type APIPayload = {

    }
}

declare module 'delivery-service-payload' {
    declare export type ServicePayload = {

    }
}

declare module 'delivery-prop-types' {
    declare export type StatusType = {
        deliveryDateOverdue: boolean,
        deliveryTimeOverdue: boolean
    }

    declare export type PropTypes = {
        id: number,
        uuid: string,
        title: string,
        remarks: string,
        customer?: ?Customer,
        contractor?: ?Authenticatable,
        deliveryDate?: ?string,
        deliveryTime?: ?string,
        items: Array<Item>,
        type?: ?DeliveryType,
        status?: ?StatusType,
        members: Array<Employee>,
        tasks: Array<Task>,
        orders: Array<Order>,
        media: Array<Media>,
        createdAt?: ?string,
        updatedAt?: ?string,
        $alias?: ?string,
    }
}