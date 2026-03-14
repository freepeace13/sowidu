<template>
    <v-card class="grey darken-4">
        <template v-if="title">
            <v-card-title class="pa-0">
                <v-toolbar flat>
                    <v-toolbar-title v-html="title"></v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon dark @click="$modal.close(id)">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-toolbar>
            </v-card-title>
        </template>

         <Suspense :delay="200" class="overflow-y-auto">
            <v-container fluid class="modal-component" fill-height slot="fallback">
                <SuspensionSpinner message="Please wait..." />
            </v-container>

            <v-card-text class="pa-0 modal-component fill-height">
                <slot></slot>
            </v-card-text>
        </Suspense>

        <template v-if="hasActionsSlot">
            <v-divider></v-divider>

            <v-card-actions>
                <slot name="actions"></slot>
            </v-card-actions>
        </template>
    </v-card>
</template>

<script>
import SuspensionSpinner from '@common/components/SuspensionSpinner';

export default {
    components: {
        SuspensionSpinner
    },

    props: {
        id: {
            type: [String, Number],
        },

        title: {
            type: String,
            default: null
        },

        loading: {
            type: Boolean,
            default: false
        },

        rejected: {
            type: Boolean,
            default: false
        },

        fullscreen: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        hasActionsSlot() {
            return !!this.$slots['actions']
                || !!this.$scopedSlots['actions']
        }
    }
}
</script>

<style scoped>
.modal-component {
    min-height: 400px;
}
</style>