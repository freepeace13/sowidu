<template>
    <v-dialog
        v-model="isShow"
        max-width="450"
    >
        <v-card>
            <v-card-title
                class="headline grey lighten-3"
                primary-title
            >
                {{ title }}
            </v-card-title>

            <v-card-text class="body-1">{{ question }}</v-card-text>

            <v-card-actions class="grey lighten-4">
                <v-spacer />

                <v-btn
                    flat
                    @click="closed"
                >
                    {{ cancelText }}
                </v-btn>

                <v-btn
                    :color="buttonColor"
                    depressed
                    @click="agree"
                >
                    {{ confirmText }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    data: () => ({
        isShow: false,
        title: null,
        question: null,
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        type: 'default',
        options: null,
    }),

    computed: {
        buttonColor() {
            if (this.options && this.options.submitColor) {
                return this.options.submitColor
            }

            let color = 'primary'
            if (this.type == 'delete') color = 'red white--text'
            if (this.type == 'warning') color = 'secondary white--text'
            if (this.type == 'info') color = 'blue white--text'
            return color
        },
    },

    beforeMount() {
        this.$confirm.$on('confirm.ask', this.show)
    },

    methods: {
        agree() {
            this.isShow = false
            this.confirm()
        },

        show({
            title,
            question,
            confirm,
            cancel,
            type,
            buttonText,
            cancelText,
            options = {},
        }) {
            if (this.isShow) this.isShow = false

            this.title = title
            this.question = question
            this.type = type
            this.isShow = true
            this.confirmText = buttonText
            this.cancelText = cancelText

            // Events
            this.confirm = confirm
            this.cancel = cancel

            this.options = options
        },

        closed() {
            this.isShow = false
            if (this.cancel != undefined) this.cancel()
        },
    },
}
</script>
