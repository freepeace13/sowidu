<template>
    <v-card style="position: relative">
        <v-img
            :src="thumbnail"
            :lazy-src="thumbnail"
            height="232"
            class="tw-cursor-pointer"
            @click.stop="() => $emit('preview')"
        >
            <v-layout
                v-show="shared"
                fill-height
                align-start
                justify-start
                ma-1
                class="tw-opacity-80"
            >
                <v-icon
                    large
                    color="primary"
                >
                    groups
                </v-icon>
            </v-layout>
            <v-layout
                v-show="type === 'video'"
                fill-height
                align-center
                justify-center
            >
                <v-btn
                    color="secondary"
                    dense
                    icon
                    large
                >
                    <v-icon large>play_arrow</v-icon>
                </v-btn>
            </v-layout>
        </v-img>
        <v-list class="py-0">
            <v-list-tile pl-0>
                <v-list-tile-avatar min-w-auto>
                    <v-icon :color="iconColor">
                        {{ icon }}
                    </v-icon>
                </v-list-tile-avatar>

                <v-list-tile-content>
                    <v-list-tile-title>{{ name }}</v-list-tile-title>
                </v-list-tile-content>

                <v-list-tile-action>
                    <v-btn
                        v-if="!hideDetails"
                        icon
                        @click.stop="(e) => $emit('click:more', e)"
                    >
                        <v-icon>more_vert</v-icon>
                    </v-btn>
                </v-list-tile-action>
            </v-list-tile>
            <v-list-group
                v-if="!hideDetails"
                no-action
            >
                <template #activator>
                    <v-list-tile>
                        <v-list-tile-content>
                            <v-list-tile-title class="caption info--text">
                                {{ $t('labels.show-more-details') }}
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </template>
                <v-list-tile
                    pl-3
                    h-auto
                >
                    <v-list-tile-content>
                        <div
                            class="tw-flex tw-flex-col tw-gap-y-theme-2 my-2 tw-text-sm"
                        >
                            <div class="tw-flex">
                                <div class="tw-text-gray-500 tw-shrink-0">
                                    {{ $t('labels.uploaded') }} :
                                </div>
                                <div class="tw-text-primary tw-ml-1">
                                    {{ uploadedAt }}
                                </div>
                            </div>
                            <div class="tw-flex">
                                <div class="tw-text-gray-500 tw-shrink-0">
                                    {{ $t('Created') }} :
                                </div>
                                <div class="tw-text-primary tw-ml-1">
                                    <span v-if="lastModified">{{
                                        lastModified | toDateTimeLocal
                                    }}</span>
                                    <span v-else>N/A</span>
                                </div>
                            </div>
                            <div class="tw-flex">
                                <div class="tw-text-gray-500 tw-shrink-0">
                                    {{ $t('labels.from') }} :
                                </div>
                                <div class="tw-text-primary tw-ml-1">
                                    {{ uploadedBy }}
                                </div>
                            </div>
                            <div class="tw-flex">
                                <div class="tw-text-gray-500 tw-shrink-0">
                                    {{ $t('headings.address') }} :
                                </div>
                                <div class="tw-text-primary tw-ml-1">
                                    {{ tagAddress }}
                                </div>
                            </div>
                            <div class="tw-flex">
                                <div class="tw-text-gray-500 tw-shrink-0">
                                    {{ $t('labels.category') }} :
                                </div>
                                <div
                                    :class="[
                                        'tw-text-primary tw-ml-1 tw-capitalize',
                                        {
                                            'tw-italic !tw-text-gray-500':
                                                !category,
                                        },
                                    ]"
                                >
                                    {{
                                        category?.trim().length === 0
                                            ? 'not set'
                                            : category
                                    }}
                                </div>
                            </div>
                            <div class="tw-flex">
                                <div class="tw-text-gray-500 tw-shrink-0">
                                    {{ $t('labels.order-no') }}:
                                </div>
                                <div v-if="orders.length">
                                    <a
                                        v-for="order in orders"
                                        :key="order.id"
                                        class="info--text tw-ml-1"
                                        @click.stop="
                                            $inertia.get(
                                                $route('orders.show', {
                                                    order: order.id,
                                                }),
                                            )
                                        "
                                    >
                                        {{ order.order_number }}
                                    </a>
                                </div>
                                <div
                                    v-else
                                    class="tw-italic !tw-text-gray-500"
                                >
                                    --
                                </div>
                            </div>
                        </div>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list-group>
        </v-list>
    </v-card>
</template>

<script>
export default {
    name: 'FileItem',
    props: {
        mediaId: {
            type: Number,
            required: true,
        },
        name: {
            type: String,
            default: null,
        },
        category: {
            type: String,
            default: null,
        },
        shared: {
            type: Boolean,
            default: false,
        },
        type: {
            type: String,
            default: 'folder',
            validator: (prop) =>
                ['folder', 'pdf', 'image', 'video', 'other'].includes(prop),
        },
        thumbnail: {
            type: String,
            default: '/storage/placeholder.png',
        },
        tagAddress: {
            required: false,
            type: String,
            default: null,
        },
        uploadedAt: {
            required: false,
            type: String,
            default: null,
        },
        uploadedBy: {
            required: false,
            type: String,
            default: null,
        },
        lastModified: {
            type: String,
            default: null,
        },
        hideDetails: {
            required: false,
            type: Boolean,
            default: false,
        },
        orders: {
            required: false,
            type: Array,
            default: () => [],
        },
    },
    data: () => ({
        showDetails: false,
    }),
    computed: {
        icon() {
            if (this.type === 'folder' && this.shared) {
                return 'folder_shared'
            }
            return {
                folder: 'folder',
                pdf: 'picture_as_pdf',
                image: 'image',
                video: 'movie',
            }[this.type]
        },
        iconColor() {
            return {
                image: 'red',
                pdf: 'green',
                video: 'blue',
            }[this.type]
        },
    },
    mounted() {
        if (this.type === 'video') {
            window.Echo.private(`video.transcode.${this.mediaId}`).listen(
                '.Sowidu\\Features\\Media\\Events\\VideoTranscodeProgress',
                console.log,
            )
        }
    },
}
</script>
