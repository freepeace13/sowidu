<template>
    <v-bottom-sheet
        persistent
        hide-overlay
        :value="__instance.show"
        :inset="!$vuetify.breakpoint.smAndDown"
    >
        <v-card tile flat>
            <v-progress-linear
                v-if="__instance.minimized"
                :value="$options.filters.percentage(__instance.progress)"
                class="my-0"
                height="3"
            />

            <v-list v-else two-line dense>
                <v-list-tile avatar v-for="file in __instance.files" :key="file.key">
                    <v-list-tile-avatar>
                        <v-icon
                            medium
                            :color="$options.filters.color(file)"
                            v-html="$options.filters.icon(file)"
                        />
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ file.name }}</v-list-tile-title>
                        <v-list-tile-sub-title>
                            <v-progress-linear
                                :value="$options.filters.percentage(file.progress)"
                                class="my-0"
                                :color="file | color"
                                height="5"
                            />
                        </v-list-tile-sub-title>
                        <v-alert class="ma-0 py-0 px-1" :value="file.isFail()">{{ file.errors }}</v-alert>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
            <v-divider></v-divider>
            <v-list>
                <v-list-tile>
                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{ __instance.uploading ? 'Uploading...' : 'Completed!' }}
                        </v-list-tile-title>
                        <v-list-tile-sub-title>
                            {{ __instance.uploaded.length }}
                            of
                            {{ __instance.files.length }}
                            file(s)
                            &middot;
                            {{ $options.filters.unit(__instance.uploaded) }} of
                            {{ $options.filters.unit(__instance.files) }} uploaded
                        </v-list-tile-sub-title>
                    </v-list-tile-content>

                    <v-spacer></v-spacer>

                    <v-list-tile-action v-if="__instance.completed">
                        <v-btn color="red" flat @click="__instance.close()">
                            Close
                        </v-btn>
                    </v-list-tile-action>

                    <v-list-tile-action :class="{ 'mr-3': $vuetify.breakpoint.mdAndUp }">
                        <v-btn icon @click="windowToggle">
                            <v-icon v-if="__instance.minimized">expand_less</v-icon>
                            <v-icon v-else>expand_more</v-icon>
                        </v-btn>
                    </v-list-tile-action>
                </v-list-tile>
            </v-list>
        </v-card>
    </v-bottom-sheet>
</template>

<script>
import { formatBytes, bytesSum } from '../utils';

export default {
    filters: {
        percentage(value) {
            return Math.floor(value * 100);
        },

        unit(values) {
            return formatBytes(bytesSum(values));
        },

        color(value) {
            if (value.isPending()) {
                return 'grey';
            } else if (value.isSuccess()) {
                return 'green';
            } else if (value.isFail()) {
                return 'red';
            }
        },

        icon(value) {
            if (value.isPending()) {
                return 'pending';
            } else if (value.isSuccess()) {
                return 'check_circle_outline';
            } else if (value.isFail()) {
                return 'error';
            } else {
                return 'cloud_upload'
            }
        }
    },

    computed: {
        __instance() {
            return this.__$resumableInstance;
        },
    },

    methods: {
        windowToggle() {
            if (this.__instance.minimized) {
                this.__instance.maximize();
            } else {
                this.__instance.minimize();
            }
        }
    }
}
</script>