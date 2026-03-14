const offerConfig = require('./modules/offer/offer.tailwind')
const workLogsConfig = require('./modules/worklogs/worklogs.tailwind')
const todosConfig = require('./modules/todos/todos.tailwind')
const deliveryTicketConfig = require('./modules/delivery-ticket/delivery-ticket.tailwind')

/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')

delete colors['lightBlue']
delete colors['warmGray']
delete colors['trueGray']
delete colors['coolGray']
delete colors['blueGray']

const moduleContent = [
    ...(offerConfig?.content || []),
    ...(workLogsConfig?.content || []),
    ...(todosConfig?.content || []),
    ...(deliveryTicketConfig?.content || []),
    // Add other module content arrays here as you create more modules
]

module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
        // Modules
        './modules/**/resources/js/**/*.js',
        './modules/**/resources/js/**/*.vue',

        // @todo - below soon to be removed
        // Modules content
        ...moduleContent,
    ],
    prefix: 'tw-',
    corePlugins: {
        preflight: false,
    },
    theme: {
        extend: {
            spacing: {
                'theme-1': '4px',
                'theme-2': '8px',
                'theme-3': '16px',
                'theme-4': '24px',
                'theme-5': '48px',
                'theme-6': '64px',
            },
            fontSize: {
                xxs: '8px',
            },
            lineHeight: {
                'extra-tight': '0.93',
            },
        },
        colors: {
            ...colors,
            grey: {
                DEFAULT: '#9E9E9E',
                100: '#FAFAFA',
                200: '#F5F5F5',
                300: '#EEEEEE',
                400: '#E0E0E0',
                500: '#BDBDBD',
                600: '#757575',
                700: '#616161',
                800: '#424242',
                900: '#212121',
            },
            primary: '#4a5e68',
            secondary: '#EF6C00',
            lightgrey: '#FAFAFA',
            info: '#2196f3',
            error: '#ff5252',
            // New UI design
            'bg-grey': '#d2dbdf',
            'primary-lighter': '#4a5e6814',
            black: '#000000de',
            'grey-lighter': '#e3e3e3',
            'grey-darker': '#474747',
            green: '#26a69a',
            greener: '#008000',
            blue: '#00008b',
            'blue-light': '#BBDEFB',
            link: '#039be5',
        },
        screens: {
            xs: { max: '600px' },
            sm: '600px',
            md: '960px',
            lg: '1264px',
            xl: '1904px',
        },
        plugins: [],
    },
}
