<template>
    <v-dialog
        v-model="isShow"
        persistent
        lazy
    >
        <v-card v-if="item">
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $t('labels.details') }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text>
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex
                            xs12
                            sm6
                        >
                            <v-img :src="item.media.url" />
                        </v-flex>
                        <v-flex
                            xs12
                            sm6
                            class="tw-flex tw-flex-col tw-gap-y-3"
                        >
                            <v-list dense>
                                <v-list-tile>
                                    <v-list-tile-content
                                        class="tw-font-semibold"
                                    >
                                        {{
                                            $t('catalog.labels.item.item-name')
                                        }}:
                                    </v-list-tile-content>
                                    <v-list-tile-content class="align-end">
                                        {{ item.name }}
                                    </v-list-tile-content>
                                </v-list-tile>
                                <v-list-tile>
                                    <v-list-tile-content
                                        class="tw-font-semibold"
                                    >
                                        {{ $t('catalog.labels.item.type') }}:
                                    </v-list-tile-content>
                                    <v-list-tile-content class="align-end">
                                        {{ item.type.name }}
                                    </v-list-tile-content>
                                </v-list-tile>
                                <v-list-tile>
                                    <v-list-tile-content
                                        class="tw-font-semibold"
                                    >
                                        {{
                                            $t(
                                                'catalog.labels.item.internal-id',
                                            )
                                        }}:
                                    </v-list-tile-content>
                                    <v-list-tile-content class="align-end">
                                        {{ item.internal_id }}
                                    </v-list-tile-content>
                                </v-list-tile>
                                <v-list-tile>
                                    <v-list-tile-content
                                        class="tw-font-semibold"
                                    >
                                        {{
                                            $t('catalog.labels.item.vendor-id')
                                        }}:
                                    </v-list-tile-content>
                                    <v-list-tile-content class="align-end">
                                        {{ item.vendor_id }}
                                    </v-list-tile-content>
                                </v-list-tile>
                                <v-list-tile>
                                    <v-list-tile-content
                                        class="tw-font-semibold"
                                    >
                                        {{
                                            $t(
                                                'catalog.labels.item.manufacture-id',
                                            )
                                        }}:
                                    </v-list-tile-content>
                                    <v-list-tile-content class="align-end">
                                        {{ item.manufacture_id ?? '--' }}
                                    </v-list-tile-content>
                                </v-list-tile>
                                <v-list-tile>
                                    <v-list-tile-content
                                        class="tw-font-semibold"
                                    >
                                        {{
                                            $t(
                                                'catalog.labels.item.purchasing-price',
                                            )
                                        }}:
                                    </v-list-tile-content>
                                    <v-list-tile-content class="align-end">
                                        {{ item.purchasing_price }}
                                    </v-list-tile-content>
                                </v-list-tile>
                                <v-list-tile>
                                    <v-list-tile-content
                                        class="tw-font-semibold"
                                    >
                                        {{
                                            $t(
                                                'catalog.labels.item.selling-price',
                                            )
                                        }}:
                                    </v-list-tile-content>
                                    <v-list-tile-content class="align-end">
                                        {{ item.selling_price }}
                                    </v-list-tile-content>
                                </v-list-tile>
                                <v-list-tile>
                                    <v-list-tile-content
                                        class="tw-font-semibold"
                                    >
                                        {{ $t('catalog.labels.item.unit') }}:
                                    </v-list-tile-content>
                                    <v-list-tile-content class="align-end">
                                        {{ item.unit }}
                                    </v-list-tile-content>
                                </v-list-tile>
                            </v-list>
                            <v-card>
                                <v-card-title>
                                    <span class="tw-font-semibold">
                                        {{
                                            $t(
                                                'catalog.labels.item.description',
                                            )
                                        }}
                                    </span>
                                </v-card-title>
                                <v-card-text>
                                    {{ item.description }}
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions>
                <v-spacer />
                <v-btn
                    outline
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.close') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    data: () => ({
        isShow: false,
        item: null,
    }),

    methods: {
        show(item) {
            if (!item) return

            this.item = item
            this.isShow = true
        },

        close() {
            this.isShow = false
        },
    },
}
</script>
