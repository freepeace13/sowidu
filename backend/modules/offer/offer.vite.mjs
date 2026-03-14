import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default {
    input: [
        'modules/offer/resources/js/core.js',
        'modules/offer/resources/css/styles.css',
        'modules/offer/resources/css/pdf.css',
    ],

    alias: {
        '@Offer': path.resolve(__dirname, './resources/js'),
    },
}
