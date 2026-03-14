<template>
    <v-card>
        <v-card-text v-html="message" />

        <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn
                color="green darken-1"
                flat="flat"
                @click="$emit('confirmed', $vnode.key)"
            >
                {{ confirmText }}
            </v-btn>

            <v-btn
                color="red darken-1"
                flat="flat"
                @click="$emit('rejected', $vnode.key)"
            >
                {{ rejectText }}
            </v-btn>

            <v-btn
                color="grey darken-1"
                flat="flat"
                @click="$modal.close($vnode.key)"
            >
                {{ cancelText }}
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    props: {
        message: {
            type: String,
            default: "Are you sure?"
        },

        confirmText: {
            type: String,
            default: "Ok"
        },

        rejectText: {
            type: String,
            default: "No"
        },

        cancelText: {
            type: String,
            default: "Cancel"
        }
    }
}
</script>