import Vue from 'vue'
import Vuetify from 'vuetify'
import 'material-design-icons-iconfont/dist/material-design-icons.css'

// @NOTE - if you want to update color values - update also tailwind.config.js
Vue.use(Vuetify, {
    iconfont: 'md',
    icons: {
        contacts: 'contact_phone',
        employees: 'groups',
        media: 'perm_media',
        document: 'description',
        image: 'image',
        video: 'video_library',
        accounts: 'manage_accounts',
        chat: 'chat',
    },

    theme: {
        primary: '#4a5e68',
        secondary: '#EF6C00',
        magenta: '#9a3053',
        themes: {
            light: {
                primary: '#37474F',
                secondary: '#EF6C00',
            },
            dark: {
                primary: '#EF6C00',
                secondary: '#37474F',
            },
        },
        'grey-lighter': '#e3e3e3',
        'grey-darker': '#474747',
        'green-dark': '#548d54',
        green: '#26a69a',
        greener: '#008000',
        'blue-info': '#0D47A1',
        link: '#039be5',
        'dark-blue': '#00008b',
        'blue-light': '#BBDEFB',
        'primary-light': '#a5b7c0',
        pink: '#fe4e89',
        // 'primary-lighter': '#4a5e6814',
        // black: '#000000de',
    },
})
