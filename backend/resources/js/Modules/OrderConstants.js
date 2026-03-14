/**
 * @NOTE: Changes on this file should be reflected on the backend
 * @see \App\Enums\DeliveryTicketType
 */
export const deliveryTicketTypes = {
    incoming: 1,
    outgoing: 2,
}

export function deliveryTicketIsIncoming(type) {
    return type === deliveryTicketTypes.incoming
}

export function deliveryTicketIsOutgoing(type) {
    return type === deliveryTicketTypes.outgoing
}
