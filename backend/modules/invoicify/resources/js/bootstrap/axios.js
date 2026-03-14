import axios from 'axios'

const headers = {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-Feature-Request': true,
    'X-Internal-Json-Request': true,
}

window.axios = axios

window.axios.defaults.headers.common = headers
