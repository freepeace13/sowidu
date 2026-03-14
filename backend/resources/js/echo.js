import Echo from 'laravel-echo'

const defaultOptions = {
    broadcaster: 'pusher',
    authEndpoint: '/apps/broadcasting/auth',

    key: import.meta.env.VITE_PUSHER_APP_KEY,

    wsHost: window.location.hostname,

    wsPort: parseInt(import.meta.env.VITE_PUSHER_APP_PORT),
    wssPort: parseInt(import.meta.env.VITE_PUSHER_APP_PORT),

    // wsPath: import.meta.env.VITE_PUSHER_APP_PATH,
    // wssPath: import.meta.env.VITE_PUSHER_APP_PATH,

    disableStats: true,
    forceTLS: import.meta.env.VITE_PUSHER_FORCE_TLS?.toLowerCase() === 'true',
    enabledTransports: ['ws', 'wss'],
}

window.Echo = new Echo(defaultOptions)
