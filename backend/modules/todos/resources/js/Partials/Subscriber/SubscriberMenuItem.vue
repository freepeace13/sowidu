<template>
    <v-list-tile
        avatar
        ripple
        class="px-2"
    >
        <v-list-tile-avatar>
            <img
                :src="photo"
                :alt="name"
            />
        </v-list-tile-avatar>

        <v-list-tile-content>
            <v-list-tile-title>
                {{ name }}
            </v-list-tile-title>
            <v-list-tile-sub-title>
                {{ description }}
            </v-list-tile-sub-title>
        </v-list-tile-content>

        <v-list-tile-action>
            <v-btn
                icon
                ripple
                @click="$emit(`click:${subscriber ? 'remove' : 'add'}`)"
            >
                <v-icon
                    v-if="subscriber"
                    color="red"
                >
                    group_remove
                </v-icon>
                <v-icon
                    v-else
                    color="primary"
                    >group_add</v-icon
                >
            </v-btn>
        </v-list-tile-action>
    </v-list-tile>
</template>
<script>
export default {
    props: {
        photo: {
            type: String,
            default: '',
            required: false,
        },
        name: {
            type: String,
            required: true,
        },
        description: {
            type: String,
            default: '',
            required: false,
        },
        subscriber: {
            type: Boolean,
            default: false,
        },
    },
}
</script>
