<template>
    <v-dialog v-model="show" max-width="450">
        <v-card>
            <v-card-title class="headline grey lighten-2" primary-title>
                {{ header }}
            </v-card-title>

            <v-card-text class="body-1">{{ message }}</v-card-text>

            <v-card-actions class="grey lighten-4">
                <v-spacer />

                <v-btn flat @click="show = false">
                    {{ cancelText }}
                </v-btn>

                <v-btn
                    :color="buttonColor"
                    depressed
                    @click=";(show = false), $emit('confirm')"
                >
                    {{ confirmText }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    props: {
        confirmText: {
            type: String,
            default: 'Confirm',
        },

        cancelText: {
            type: String,
            default: 'Cancel',
        },

        header: {
            type: String,
            default: 'Confirm',
        },

        message: {
            type: String,
            default: 'Are you sure?',
        },

        type: {
            type: String,
            default: 'default',
        },
    },

    data() {
        return {
            show: false,
        }
    },

    computed: {
        buttonColor() {
            let color = 'default'
            if (this.type == 'delete') color = 'red white--text'
            return color
        },
    },

    methods: {
        open() {
            this.show = true
        },
    },
}
</script>
