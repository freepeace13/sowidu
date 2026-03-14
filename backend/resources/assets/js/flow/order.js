import { ORDER_STATES } from '~/support/constanst';

declare type OrderStateType = Preparation | Completed | Pending | Final | Done;
declare type OrderType = 'incoming' | 'outgoing';
declare type OrderCreator = User | Employee;
declare type OrderStates = Array<{ name: string, color: string, value: boolean }>;
declare type OrderReferenceType = {
    states: OrderStates,
    authorizations?: ?{
        editable: boolean,
        confirmable: boolean
    }
}

declare module 'order-state-types' {
    declare export type Preparation = ORDER_STATES.PREPARATION;
    declare export type Completed = ORDER_STATES.COMPLETED;
    declare export type Pending = ORDER_STATES.PENDING;
    declare export type Final = ORDER_STATES.FINAL;
    declare export type Done = ORDER_STATES.DONE;
}

declare module 'order-api-payload' {
    declare export type APIPayload = {

    }
}

declare module 'order-service-payload' {
    declare export type ServicePayload = {

    }
}

declare module 'order-prop-types' {
    declare export type PropTypes = {
        id: number,
        uuid: string,
        orderDate: string,
        description: string,
        orderNumber: number,
        state: OrderStateType,
        customer?: ?Customer,
        creator?: ?Authorizable,
        type: OrderStateType,
        contractor?: ?Authenticatable,
        reference: OrderReferenceType,
        members: Array<Employee>,
        tasks: Array<Task>,
        media: Array<Media>,
        items: Array<Item>,
        deliveries: Array<Delivery>,
        formattedDates: {
            orderDate: string,
            deliveryDate: string,
        },
        states: Object,
        createdAt: string,
        updatedAt: string,
        $alias: string,
    }
}