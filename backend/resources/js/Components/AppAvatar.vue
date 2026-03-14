<template>
    <img
        v-tooltip="{ content: name, visible: hasTooltip }"
        :class="[
            'tw-inline-block tw-ring-1 tw-ring-grey-300 tw-cursor-pointer',
            box,
            {
                '!tw-w-8 tw-h-auto': $vuetify.breakpoint.xs,
            },
        ]"
        :src="avatar"
        :alt="name"
    />
</template>
<script>
export default {
    props: {
        avatar: {
            type: String,
            default: null,
        },

        name: {
            type: String,
            default: null,
        },

        size: {
            type: Number,
            default: 10,
        },

        hasTooltip: {
            type: Boolean,
            default: false,
        },
    },

    computed: {
        box() {
            if (this.size == 8) return 'tw-h-8 tw-w-8'
            return 'tw-h-10 tw-w-10'
        },
    },
}
</script>
