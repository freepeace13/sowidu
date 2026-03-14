<script setup>
import ModalButtonClose from '@/Apps/Shared/Components/ModalButtonClose.vue'
import CatalogItemsTab from './CatalogItemsTab.vue'
import { ref } from 'vue'

const isShow = ref(false)
const activeTab = ref('catalog_items')

const show = () => (isShow.value = true)
const close = () => (isShow.value = false)

defineExpose({
    show,
    close,
})
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        lazy
        fullscreen
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $t('invoices.labels.add-item') }}
                </v-toolbar-title>
                <v-spacer />
                <ModalButtonClose @click.native="close" />
            </v-toolbar>
            <v-divider />
            <v-card-text>
                <v-tabs
                    v-model="activeTab"
                    fixed-tabs
                    centered
                    icons-and-text
                    color="transparent"
                >
                    <v-tabs-slider color="primary" />

                    <v-tab href="#used_products">
                        <div class="tw-text-lg">
                            {{ $t('invoices.items.labels.used-products') }}
                        </div>
                        <v-icon color="indigo">shopping_cart</v-icon>
                    </v-tab>

                    <v-tab href="#catalog_items">
                        <div class="tw-text-lg">
                            {{ $t('invoices.items.labels.catalog-items') }}
                        </div>
                        <v-icon color="cyan">menu_book</v-icon>
                    </v-tab>

                    <!-- <v-tab href="#delivery_tickets">
                        <div class="tw-text-lg">
                            {{ $t('headings.delivery_tickets') }}
                        </div>
                        <v-icon color="teal">local_shipping</v-icon>
                    </v-tab> -->

                    <v-tab-item value="used_products">
                        <CatalogItemsTab
                            tab-name="used_products"
                            :active-tab="activeTab"
                            @close="close"
                        />
                    </v-tab-item>

                    <v-tab-item value="catalog_items">
                        <CatalogItemsTab
                            tab-name="catalog_items"
                            :active-tab="activeTab"
                            @close="close"
                        />
                    </v-tab-item>
                    <!-- <v-tab-item value="delivery_tickets">
                        <DeliveryTicketsTab
                            :active-tab="activeTab"
                            @close="close"
                        />
                    </v-tab-item> -->
                </v-tabs>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>
