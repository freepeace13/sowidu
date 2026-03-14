import { getCurrentInstance } from 'vue'

export default function () {
    const app = getCurrentInstance()

    const $root = app.proxy.$root
    const $t = (key, params = null) => $root.$t(key, params)
    const $confirm = (props) => app.proxy.$root.$confirm.ask(props)
    const $route = window.route
    return { $t, $confirm, app, $route, $root }
}
