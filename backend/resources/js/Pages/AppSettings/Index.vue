<script>
import AppServiceMixin from '@/Mixins/AppServiceMixin'

export default {
    mixins: [AppServiceMixin],

    props: {
        settings: {
            required: true,
            type: Array,
        },
    },
}
</script>
<template>
    <v-container
        align-center
        fluid
        grid-list-lg
        fill-height
    >
        <v-layout
            align-center
            justify-center
            row
            wrap
            class="tw-content-center"
        >
            <v-flex
                grow
                tw-flex
                tw-justify-center
            >
                <div
                    class="tw-grid tw-grid-cols-2 tw-gap-x-4 tw-gap-y-5 sm:tw-grid-cols-3 sm:tw-gap-x-5 sm:tw-gap-y-5 md:tw-gap-x-5 md:tw-gap-y-6 tw-content-center"
                >
                    <v-card
                        v-for="setting in settings"
                        :key="setting.name"
                        class="tw-cursor-pointer"
                        @click="$inertia.get(setting.route)"
                    >
                        <v-card-title
                            primary-title
                            class="tw-flex tw-content-center tw-justify-center tw-flex-col"
                        >
                            <v-avatar
                                :size="70"
                                :color="setting.color"
                            >
                                <v-icon
                                    dark
                                    :size="35"
                                >
                                    {{ setting.icon }}
                                </v-icon>
                            </v-avatar>
                            <h2
                                class="primary--text tw-text-center tw-text-lg md:tw-text-xl lg:tw-text-2xl mt-2"
                            >
                                {{ $t(setting.label) }}
                            </h2>
                        </v-card-title>
                    </v-card>
                </div>
            </v-flex>
        </v-layout>
    </v-container>
</template>
