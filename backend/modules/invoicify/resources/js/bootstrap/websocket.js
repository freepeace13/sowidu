import Echo from 'laravel-echo'
import pusherjs from 'pusher-js'

window.Pusher = pusherjs

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY
const pusherPort = import.meta.env.VITE_PUSHER_APP_PORT || 6001
const pusherForceTLS =
    import.meta.env.VITE_PUSHER_FORCE_TLS?.toLowerCase() === 'true'

const config = {
    broadcaster: 'pusher',
    authEndpoint: '/apps/broadcasting/auth',
    key: pusherKey,
    wsHost: window.location.hostname,
    wsPort: parseInt(pusherPort),
    wssPort: parseInt(pusherPort),
    disableStats: true,
    forceTLS: pusherForceTLS,
    enabledTransports: ['ws', 'wss'],
}

window.Echo = new Echo(config)
