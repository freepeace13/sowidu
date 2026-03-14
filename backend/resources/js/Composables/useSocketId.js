/**
 * Add X-Socket-Id on headers used for Laravel broadcasting
 *
 * @return Object
 */
export const socketId = ({ 'X-Socket-Id': window.Echo.socketId() })

export const socketIdHeader = { headers: socketId }
