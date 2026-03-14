import vue from '@vitejs/plugin-vue2'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import { defineConfig } from 'vite'
import invoiceConfig from './app/Modules/Invoice/invoice.vite.js'
import offerConfig from './modules/offer/offer.vite.mjs'
import todosConfig from './modules/todos/todos.vite.mjs'
import worklogsConfig from './modules/worklogs/worklogs.vite.mjs'
import deliveryTicketConfig from './modules/delivery-ticket/delivery-ticket.vite.mjs'


export default defineConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js/'),
            '@components': path.resolve(__dirname, './resources/js/Components'),
            '~Invoicify': path.resolve(__dirname, './modules/invoicify/resources/js'),
            '~Shared': path.resolve(__dirname, './modules/shared/resources/js'),
            '~Chatly': path.resolve(__dirname, './modules/chatly/resources/js'),
            '~Catalog': path.resolve(__dirname, './modules/catalog/resources/js'),

            // Modules aliases
            ...(invoiceConfig?.alias || {}),
            ...(offerConfig?.alias || {}),
            ...(worklogsConfig?.alias || {}),
            ...(todosConfig?.alias || {}),
            ...(deliveryTicketConfig?.alias || {}),
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/js/core.js',
                'resources/css/views/invoice-pdf.css',
                'resources/css/tailwind.css',
                'resources/css/fonts.css',
                'resources/css/vendor.css',
                'resources/css/styles.css',

                // Modules
                ...(invoiceConfig?.input || []),

                // Modules inputs
                ...(offerConfig?.input || []),
                ...(worklogsConfig?.input || []),
                ...(todosConfig?.input || []),
                ...(deliveryTicketConfig?.input || []),
            ],
            refresh: [{
                paths: [
                    './resources/js/**',
                    './modules/**/resources/js/**',
                ],
                config: { delay: 300 }
            }],
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        hmr: {
            host: 'sowidu.localhost',
        },
        watch: {
            ignored: [
                '**/vendor/**',
                '**/node_modules/**',
                '**/storage/**',
                '**/public/build/**',
                '**/public/hot',
                '**/.git/**',
                '**/coverage-report/**',
                '**/docker/**',
                '**/database/dumps/**',
                '**/database/seeders/**',
                '**/database/factories/**',
                '**/app/Factories/**',
                '**/app/Database/**',
            ],
        },
    },
    define: {
        'process.platform': JSON.stringify(process.platform),
        // 'process.env': {}, // TODO temporary fix for `process is not defined error on media page`
    },
})
