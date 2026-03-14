<template>
    <v-list-tile-content
        :class="{ disabled: disabled }"
        @click="$emit('click', subscriber.id)"
    >
        <v-layout
            row
            wrap
            align-center
        >
            <v-flex
                shrink
                mr-2
            >
                <v-avatar
                    size="24"
                    color="primary"
                >
                    <img
                        :src="user.photo"
                        :alt="user.name"
                    />
                </v-avatar>
            </v-flex>
            <v-flex class="body-1">
                {{ user.name }}
            </v-flex>
        </v-layout>
    </v-list-tile-content>
</template>
<script>
export default {
    props: {
        subscriber: {
            type: Object,
            default: () => ({}),
        },

        disabled: {
            type: Boolean,
            default: false,
        },
    },

    computed: {
        user() {
            return this.subscriber.user
        },
    },
}
</script>
