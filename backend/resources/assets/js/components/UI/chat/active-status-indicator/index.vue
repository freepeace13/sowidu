<template>
    <v-tooltip right :color="color">
        <v-icon small :color="color" slot="activator">
            fiber_manual_record
        </v-icon>
        <span>{{ status | capitalize }}</span>
    </v-tooltip>
</template>
<script>
const colors = {
    offline: 'red',
    online: 'green',
    idle: 'yellow',
}

export default {
    props: {
        status: {
            type: String,
            required: true,
            validator: (value) => {
                return Object.prototype.hasOwnProperty.call(colors, value)
            },
        },
    },

    computed: {
        color() {
            return colors[this.status]
        },
    },
}
</script>
