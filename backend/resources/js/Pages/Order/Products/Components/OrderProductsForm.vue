<script setup>
import ModalButtonClose from '@/Apps/Shared/Components/ModalButtonClose.vue'
import { nextTick, ref } from 'vue'
import DeliveryTicketMaterials from './DeliveryTicketMaterials.vue'
import UsedProducts from './UsedProducts.vue'

defineExpose({
    show,
})

const props = defineProps({
    title: {
        required: true,
        type: String,
    },
    route: {
        required: false,
        type: String,
        default: null,
    },
    submitBtnText: {
        required: false,
        type: String,
        default: 'Save',
    },
})

const isShow = ref(false)
const activeTab = ref('used_products')
const endpoint = ref(props.route)
const usedProductsRef = ref()

function show(url = null) {
    if (url) {
        endpoint.value = url
    }

    isShow.value = true

    if (activeTab.value === 'used_products') {
        nextTick(() => {
            usedProductsRef.value.show()
        })
    }
}

function close() {
    activeTab.value = 'used_products'
    isShow.value = false
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ title }}
                </v-toolbar-title>
                <v-spacer />
                <ModalButtonClose @click.native="close" />
            </v-toolbar>
            <v-divider />
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

                <v-tab href="#delivery_ticket_materials">
                    <div class="tw-text-lg">
                        {{
                            $t(
                                'delivery_tickets.labels.delivery-ticket-materials',
                            )
                        }}
                    </div>
                    <v-icon color="cyan">menu_book</v-icon>
                </v-tab>

                <v-tab-item value="used_products">
                    <UsedProducts
                        ref="usedProductsRef"
                        :active-tab="activeTab"
                        :endpoint="endpoint"
                        @close="close"
                    />
                </v-tab-item>

                <v-tab-item value="delivery_ticket_materials">
                    <DeliveryTicketMaterials
                        :active-tab="activeTab"
                        :endpoint="endpoint"
                        @close="close"
                    />
                </v-tab-item>
            </v-tabs>
        </v-card>
    </v-dialog>
</template>
