<template>
    <v-snackbar
        :value="visible"
        :right="true"
        :top="true"
        @change="logChange"
        :timeout="timeout"
        :color="color"
    >
        <component :is="template" />
    </v-snackbar>
</template>

<script>
import { convertToComponent } from '~/services/events/snackbar/utils'
import { $snackbar, defaultConfig } from '~/services/events/snackbar'

export default {
    data: () => ({
        visible: false,
        color: null,
        template: null,
        timeout: defaultConfig.timeout
    }),

    methods: {
        logChange(event) {
            console.log(event);
        },

        show(color = 'success', args) {
            if (this.visible) this.visible = false

            const { timeout, template } = args

            this.timeout = timeout || defaultConfig.timeout
            this.template = convertToComponent(template || args)
            this.color = color
            this.visible = true
        }
    },

    beforeMount() {
        $snackbar.listen('success', (args) => this.show('success', args))
        $snackbar.listen('fail', (args) => this.show('error', args))
        $snackbar.listen('info', (args) => this.show('info', args))
    }
}
</script>

<style lang="scss" scoped>
    // .v-snack {
    //     top: 45px;
    // }
</style>
