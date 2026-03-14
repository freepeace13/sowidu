<template>
    <v-navigation-drawer
        v-model="isShow"
        fixed
        right
        temporary
        disable-resize-watcher
        width="450"
        v-bind="$attrs"
        class="has-navbar-on-top"
    >
        <v-toolbar flat>
            <v-list class="pa-0">
                <v-list-tile px-2>
                    <v-list-tile-title class="subheading">
                        Timeline
                    </v-list-tile-title>
                    <v-list-tile-action>
                        <v-btn
                            icon
                            flat
                            @click="isShow = false"
                        >
                            <v-icon>close</v-icon>
                        </v-btn>
                    </v-list-tile-action>
                </v-list-tile>
            </v-list>
        </v-toolbar>
        <v-divider />
        <div class="pa-2 tw-max-h-[86vh] tw-overflow-auto">
            <v-alert
                type="info"
                :value="!timelines.length"
            >
                Sorry, timeline is not yet supported on this order.
            </v-alert>
            <v-timeline v-if="timelines.length">
                <v-timeline-item
                    v-for="timeline in timelines"
                    :key="timeline.id"
                    :color="getColor(timeline.event)"
                    :icon="getIcon(timeline.event)"
                >
                    <template #opposite>
                        <div class="tw-flex tw-items-center">
                            <Subscriber :avatar="timeline.causer.photo" />
                            <div class="tw-ml-2">
                                {{ timeline.causer.name }}
                            </div>
                        </div>
                        <div
                            class="tw-text-left mt-2 tw-text-sm tw-italic tw-text-gray-400"
                        >
                            {{ timeline.created_at | formatDate('LLL') }}
                        </div>
                    </template>
                    <v-card
                        :color="getColor(timeline.event)"
                        class="elevation-2"
                    >
                        <v-card-title
                            class="tw-uppercase tw-text-white tw-font-semibold"
                        >
                            {{ timeline.event.replace('_', ' ') }}
                        </v-card-title>
                        <v-card-text class="white">
                            <div
                                class="tw-text-sm"
                                v-html="
                                    $t(timeline.description, {
                                        causer: timeline.causer.name,
                                    })
                                "
                            />
                        </v-card-text>
                    </v-card>
                </v-timeline-item>
            </v-timeline>
        </div>
    </v-navigation-drawer>
</template>

<script>
import Subscriber from '@Todos/Partials/Subscriber/Subscriber.vue'
export default {
    components: { Subscriber },
    props: {
        timelines: {
            type: Array,
            required: true,
        },
    },

    data: () => ({
        isShow: false,
    }),

    methods: {
        show() {
            this.isShow = true

            if (!this.timelines.length) {
                this.$inertia.reload({ only: ['timelines'] })
            }
        },

        getColor(event) {
            const colors = {
                created: '#2979FF',
                cancelled: '#D50000',
                confirmed: '#FFC107',
                start_working: 'info',
                finish_working: 'success',
                client_rejected: '#9C27B0',
                client_accepted: '#AFB42B',
                completed: '#4CAF50',
            }

            return colors[event]
        },

        getIcon(event) {
            const icon = {
                created: 'create',
                cancelled: 'highlight_off',
                confirmed: 'check_circle_outline',
                start_working: 'construction',
                finish_working: 'task',
                client_rejected: 'thumb_down_alt',
                client_accepted: 'thumb_up',
                completed: 'verified',
            }

            return icon[event]
        },
    },
}
</script>
<style lang="scss">
.v-list {
    .v-list__tile[px-2] {
        padding-left: 8px;
        padding-right: 8px;
    }
}
</style>
