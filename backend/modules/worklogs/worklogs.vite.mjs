import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default {
    input: [
        'modules/worklogs/resources/js/core.js',
        'modules/worklogs/resources/css/styles.css',
    ],

    alias: {
        '@Worklogs': path.resolve(__dirname, './resources/js'),
    },
}
