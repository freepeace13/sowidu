export default {
    props: {
        unreadNotifications: {
            type: Number,
            default: 0,
        },

        appName: {
            type: String,
            default: '',
        },

        user: {
            type: Object,
            required: false,
            default: () => ({
                locked: false,
                impersonating: false,
                photo: null,
                tenant: {
                    name: '',
                    photo: null,
                },
                isGuest: true,
            }),
        },

        flash: {
            required: false,
            type: [Object, Array],
            default: () => ({}),
        },

        companies: {
            required: false,
            type: Array,
            default: () => [],
        },

        locale: {
            type: String,
            default: 'en',
            required: false,
        },

        locales: {
            type: Object,
            default: () => ({}),
        },
    },
}
