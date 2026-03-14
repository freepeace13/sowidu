import Vue from 'vue'

Vue.directive('router-push', {
    bind: (el, binding, vnode) => {
        el.addEventListener('click', (e) => {
            vnode.context.$router.push(binding.value).then(() => {})
        })
    }
})
