export const APP = 'order';

export const PERMISSIONS = {
    CREATE_INCOMING_ORDER: 'create incoming order',
    CREATE_OUTGOING_ORDER: 'create outgoing order',
    VIEW_INCOMING_ORDERS: 'view incoming orders',
    VIEW_OUTGOING_ORDERS: 'view outgoing orders',
    CONFIRM_ORDER: 'confirm orders',
    UPDATE_ORDER: 'update orders',
    UPDATE_ORDER_MEMBERS: 'update order members',
    UPDATE_ORDER_ITEMS: 'update order items',
}

export const FILTER_TYPES = {
    STATE: {
        DEFAULT: 'pending',
        ONGOING: 'final',
        UNCONFIRMED: 'completed',
        DRAFTS: 'preparation',
        DONE: 'done',
        CANCELLED: 'cancelled'
    }
}