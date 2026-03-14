const auth = [
    {   path: '/register',
            name: 'auth.register',
            component: require('~/views/Register').default
    },

    {   path: '/forgot-password',
            name: 'auth.password.forgot',
            component: require('~/views/ForgotPassword').default
    },
]

export default auth
