<script setup>
import { computed } from 'vue'

const { details } = defineProps({
    details: {
        required: true,
        type: Object,
    },
    item: {
        required: true,
        type: Object,
    },
})

const mediaUrl = computed(() => details.media_url)
const mediaThumbnailUrl = computed(() => details?.media_thumbnail_url)
</script>
<template>
    <v-card-text>
        <v-container
            grid-list-xs
            fluid
            py-0
            px-2
        >
            <v-layout
                row
                wrap
            >
                <v-flex
                    xs12
                    class="tw-flex tw-flex-row tw-gap-x-2"
                >
                    <div>
                        <v-avatar
                            tile
                            :size="50"
                            class="mr-2"
                        >
                            <v-img
                                :size="50"
                                :src="mediaUrl"
                                :alt="details.name"
                                :lazy-src="mediaThumbnailUrl"
                            />
                        </v-avatar>
                    </div>
                    <div class="">
                        <div class="tw-flex tw-gap-x-2">
                            <div class="label">
                                {{ $t('catalog.labels.item.internal-id') }}:
                            </div>
                            <div>
                                {{ details.internal_id }}
                            </div>
                        </div>
                        <div class="tw-flex tw-gap-x-2">
                            <div class="label">
                                {{ $t('catalog.labels.item.type') }}:
                            </div>
                            <div>
                                {{
                                    details?.type?.name ?? item.item_type.label
                                }}
                            </div>
                        </div>
                    </div>
                    <div class="ml-2">
                        <div class="tw-flex tw-gap-x-2">
                            <div class="label">
                                {{ $t('catalog.labels.item.vendor-id') }}
                            </div>
                            <div>
                                {{ details?.vendor_id ?? '--' }}
                            </div>
                        </div>
                        <div class="tw-flex tw-gap-x-2">
                            <div v-if="item.is_delivery_ticket_materials">
                                {{ item.item_type.label }}
                            </div>
                            <div v-if="item.is_work_log">
                                {{ details.work_log.started_at }}
                                -
                                {{ details.work_log.ended_at }}
                            </div>
                        </div>
                    </div>
                </v-flex>
            </v-layout>
        </v-container>
    </v-card-text>
</template>
