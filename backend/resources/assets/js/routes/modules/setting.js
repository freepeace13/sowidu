const setting = [
    {
        path: '/settings',
        name: 'settings',
        component: require('~/views/Settings/Master').default,
        redirect: { name: 'settings.account' },
        children: [
            {
                path: 'account',
                name: 'settings.account',
                component: require('~/views/Settings/Account').default
            },
            {
                path: 'permission',
                name: 'settings.permission',
                component: require('~/views/Settings/Permission').default,
                meta: {
                    permissions: ['manage permissions']
                }
            }
        ]
    }
]

export default setting
