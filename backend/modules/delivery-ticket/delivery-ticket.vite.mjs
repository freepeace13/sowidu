import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default {
    input: [
        'modules/delivery-ticket/resources/js/core.js',
        'modules/delivery-ticket/resources/css/styles.css',
    ],

    alias: {
        '@DeliveryTicket': path.resolve(__dirname, './resources/js'),
    },
}