import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default {
    input: [
        'modules/todos/resources/js/core.js',
        'modules/todos/resources/css/styles.css',
    ],

    alias: {
        '@Todos': path.resolve(__dirname, './resources/js'),
    },
}