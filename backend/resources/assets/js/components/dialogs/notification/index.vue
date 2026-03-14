<template>
    <v-snackbar
        :timeout="timeout"
        :color="color"
        :bottom="true"
        :left="true"
        v-model="show"
    >
        <TemplateManager v-if="!plainText" :type="template" :data="data"/>
        <span v-else>{{ message }}</span>
    </v-snackbar>
</template>

<script>
    import TemplateManager from '../../partials/notifications/templates'

    export default {
        name: 'Notification',

        components: {
            TemplateManager
        },

        data: () => ({
            timeout: 5000,
            color: 'grey darken-4',
            show: false,
            message: 'Default message',
            plainText: true,
            template: null,
            data: {}
        }),

        methods: {
            success (message) {
                this.color = 'success'
                this.message = message
                this.show = true
            },

            error (message) {
                this.color = 'error'
                this.message = message
                this.show = true
            },

            notify (template, data) {
                this.plainText = false
                this.template = template
                this.data = data
                this.show = true
            }
        }
    }
</script>

