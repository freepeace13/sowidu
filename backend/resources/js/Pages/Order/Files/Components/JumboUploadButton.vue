<template>
    <v-container
        grid-list-xl
        fluid
        pa-0
        fill-height
    >
        <v-layout
            row
            align-center
            justify-center
            wrap
        >
            <v-flex v-bind="containerSize">
                <v-card
                    flat
                    hover
                    class="border-dashed"
                >
                    <v-card-title
                        primary-title
                        @click="$emit('click:card', $event)"
                    >
                        <div class="tw-flex tw-flex-col tw-w-full">
                            <div class="headline tw-text-center">
                                {{ title }}
                            </div>
                            <div class="tw-w-full tw-text-center mt-2">
                                <v-icon
                                    color="primary"
                                    size="60"
                                >
                                    {{ icon }}
                                </v-icon>
                            </div>
                        </div>
                    </v-card-title>
                </v-card>
                <slot />
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
export default {
    props: {
        title: {
            required: true,
            type: String,
        },
        icon: {
            required: false,
            type: String,
            default: 'add',
        },
        containerSize: {
            required: false,
            type: Object,
            default: () => ({ xs4: true }),
        },
    },
}
</script>
<style scoped>
.border-dashed {
    border: 2px dashed #37474f;
}
</style>
