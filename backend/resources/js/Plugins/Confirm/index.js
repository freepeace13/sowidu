import ConfirmationDialogPlugin from './ConfirmationDialog.vue'

const Plugin = {
    install(Vue) {
        if (this.installed) return

        Vue.component('ConfirmationDialogPlugin', ConfirmationDialogPlugin)

        Vue.prototype.$confirm = new Vue({
            methods: {
                ask({
                    title = 'Confirm',
                    question = 'Are you sure?',
                    confirm,
                    cancel,
                    type = 'default',
                    buttonText = 'Confirm',
                    cancelText = 'Cancel',
                    options = {},
                }) {
                    this.$emit('confirm.ask', {
                        title,
                        question,
                        confirm,
                        cancel,
                        type,
                        buttonText,
                        cancelText,
                        options,
                    })
                },
            },
        })
    },
}

export default Plugin
