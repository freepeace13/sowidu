import axios from 'axios'
import pusherjs from 'pusher-js'

window.axios = axios

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['X-Feature-Request'] = true
axios.defaults.headers.common['X-Internal-Json-Request'] = true

window.Pusher = pusherjs

import './theme'
import './echo'
