const routes = [
    // {
    //     path: '/employees',
    //     name: 'employees',
    //     component: require('~/views/Company/Employees').default
    // },
    {
        path: '/employees/:uuid/edit',
        name: 'employees.edit',
        component: require('~/views/Company/Employee').default
    }
]

export default routes
