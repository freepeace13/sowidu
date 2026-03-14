<script setup>
import { computed } from 'vue'

const props = defineProps({
    label: {
        required: true,
        type: String,
    },
    name: {
        required: true,
        type: String,
    },
    disabled: {
        required: false,
        type: Boolean,
        default: false,
    },
    permission: {
        required: true,
        type: [String, Boolean],
    },
    icon: {
        required: false,
        type: String,
        default: null,
    },
    color: {
        required: false,
        type: String,
        default: null,
    },
    route: {
        required: true,
        type: String,
    },
    iconSize: {
        type: Number,
        default: 35,
    },
    avatarSize: {
        type: Number,
        default: 70,
    },
    small: {
        type: Boolean,
        default: false,
    },
    onDenied: {
        required: false,
        type: String,
        default: 'hide',
    },
})

const big = computed(() => {
    return !props.small
})

const redirect = () => {
    if (props.disabled) return

    window.location = props.route

    // TODO hacky fix for translations that loaded lazily
    // this.$inertia.visit(this.route, {
    //     preserveState: false,
    //     preserveScroll: false,
    //     replace: true,
    // })
}
</script>
<template>
    <v-card
        :disabled="disabled"
        :hover="!disabled"
        v-bind="$attrs"
        @click="redirect"
    >
        <v-card-title
            primary-title
            :class="[
                'tw-flex tw-content-center tw-justify-center tw-flex-col',
                {
                    'pa-2 tw-space-y-2 caption font-weight-bold': small,
                    'pa-4 py-5': big && $vuetify.breakpoint.smAndUp,
                    'pa-4': big && $vuetify.breakpoint.smAndDown,
                },
            ]"
        >
            <v-avatar
                :size="avatarSize"
                :color="color"
            >
                <v-icon
                    dark
                    :size="iconSize"
                >
                    {{ icon }}
                </v-icon>
            </v-avatar>
            <h2
                :class="[
                    'primary--text tw-text-center tw-text-lg md:tw-text-xl lg:tw-text-2xl',
                    {
                        'py-0 mt-2 caption font-weight-bold': small,
                        'mt-4': big && $vuetify.breakpoint.mdAndUp,
                        'mt-2': big && $vuetify.breakpoint.smAndUp,
                        'mt-1': big && $vuetify.breakpoint.xsOnly,
                    },
                ]"
            >
                {{ $t(label) }}
            </h2>
        </v-card-title>
    </v-card>
</template>
