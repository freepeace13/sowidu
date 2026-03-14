export const STORE_MODULES = {
    AUTH: 'auth',
    BUSINESS: 'business',
    CONTACT: 'contact'
}

export const AUTH_GUARDS = {
    COMPANY: 'company',
    USER: 'user'
}

export const DATE_TIME_FORMAT = 'YYYY-MM-DD HH:mm:ss'

export const DOCTYPES = {
    ORDER: '.order',
    INVOICE: '.invoice',
    DELIVERY: '.delivery',
    TASK: '.task'
}

export const CONTACT_TYPES = {
    USER: 'users',
    COMPANY: 'companies',
    EMPLOYEE: 'employees'
}

export const CONTACT_RESOURCES = {
    ADDRESSBOOK: 'addressbook',
    EVERYONE: 'everyone'
}

export const ORDER_STATES = {
    PREPARATION: 'preparation',
    COMPLETED: 'completed',
    PENDING: 'pending',
    FINAL: 'final',
    DONE: 'done'
}

export const ORDER_STATES_ROUTE_MAP = {
    [ORDER_STATES.PREPARATION]: 'prepare',
    [ORDER_STATES.COMPLETED]: 'complete',
    [ORDER_STATES.PENDING]: 'confirm',
    [ORDER_STATES.FINAL]: 'process',
    [ORDER_STATES.DONE]: 'finish',
}

export const ORDER_TYPES = {
    INCOMING: 'incoming',
    OUTGOING: 'outgoing'
}

export const DELIVERY_TYPES = {
    INCOMING: 'incoming',
    OUTGOING: 'outgoing'
}
