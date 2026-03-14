import store from '~/services/store';

export default function menu() {
    return [
        {
            name: 'Desktop',
            route: { name: 'desktop' },
            icon: 'home',
            visible: true
        },
        {
            name: 'Offers',
            route: { name: null },
            icon: 'attach_money',
            visible: false
        },
        {
            name: 'Employee',
            route: { name: 'employees' },
            icon: 'supervisor_account',
            visible: store.getters['auth/authorize']('view employees')
        },
        {
            name: 'Invoices',
            route: { name: null },
            icon: 'receipt',
            visible: false
        },
        {
            name: 'Orders',
            route: { name: 'orders' },
            icon: 'reorder',
            visible: true
        },
        {
            name: 'Delivery',
            route: { name: 'deliveries' },
            icon: 'directions_bus',
            visible: true
        },
        {
            name: 'Task',
            route: { name: 'tasks' },
            icon: 'done',
            visible: true
        },
        {
            name: 'Contact',
            route: { name: 'contacts' },
            icon: 'library_books',
            visible: true
        },
        {
            name: 'Map',
            route: { name: null },
            icon: 'map',
            visible: false
        },
        {
            name: 'Media',
            route: { name: 'media' },
            icon: 'image',
            visible: true
        },
        {
            name: 'Chat',
            route: { name: null },
            icon: 'comment',
            visible: false
        },
        {
            name: 'Products &amp; Equiments',
            route: { name: 'products' },
            icon: 'event_note',
            visible: true
        },
        {
            name: 'Calendar',
            route: { name: null },
            icon: 'date_range',
            visible: false
        },
        {
            name: 'Banking',
            route: { name: null },
            icon: 'local_atm',
            visible: false
        },
    ]
}
