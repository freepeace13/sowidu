<template>
    <v-dialog
        v-model="show"
        max-width="450"
    >
        <template #activator="props">
            <slot
                name="activator"
                v-bind="props"
            />
        </template>

        <v-card>
            <v-card-title>
                <slot name="header" />
            </v-card-title>

            <v-card-text>
                <slot name="body" />
            </v-card-text>

            <v-card-actions class="grey lighten-4">
                <v-spacer />

                <v-btn
                    flat
                    @click="show = false"
                >
                    {{ cancelText }}
                </v-btn>

                <v-btn
                    color="primary"
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
    },

    data() {
        return {
            show: false,
        }
    },
}
</script>
